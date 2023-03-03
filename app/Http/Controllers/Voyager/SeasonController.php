<?php

namespace App\Http\Controllers\Voyager;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Guess;
use App\Models\Season;
use App\Models\SeasonTeam;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class SeasonController extends VoyagerBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function combineTeams($season)
    {
        Game::query()->delete();
        $teams = $season->meta->groupBy('group');
        $j = 0;
        foreach ($teams as $teamGroup) {
            $mid = count($teamGroup) / 2;
            for ($i = 0; $i < count($teamGroup) / 4; $i++) {
                Game::create([
                    'first_team_id' => $teamGroup[$i]->team->id,
                    'second_team_id' => $teamGroup[count($teamGroup) - $i - 1]->team->id,
                    'season_id' => $season->id,
                    'type' => $j % 2 ? 'right' : 'left',
                ]);
                Game::create([
                    'first_team_id' => $teamGroup[$mid - $i - 1]->team->id,
                    'second_team_id' => $teamGroup[$mid + $i]->team->id,
                    'season_id' => $season->id,
                    'type' => $j % 2 ? 'right' : 'left',
                ]);
            }

            $j++;
        }

    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        SeasonTeam::query()->delete();
        $sts = $request->get('teams_groups');

        foreach ($sts as $group => $ids) {
            foreach ($ids as $rating => $id) {
                SeasonTeam::create([
                    'team_id' => $id,
                    'season_id' => $data->id,
                    'rating' => $rating + 1,
                    'group' => $group,
                ]);
            }
        }



        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public function results(Season $season)
    {
        /** @var Collection $games */
        $games = $season->games->groupBy('type');

        $teamMapper = fn($game) => [Team::findForSeason($game->first_team_id, $season), Team::findForSeason($game->second_team_id, $season)];

        return Voyager::view('voyager::seasons.results', [
            'season' => $season,
            'games_left' => $games['left']->map($teamMapper)->toArray(),
            'games_right' => $games['right']->map($teamMapper)->toArray(),
            'left' => $season->getResults('left'),
            'right' => $season->getResults('right'),
            'final' => $season->getResults('final'),
        ]);
    }

    public function storeResults(Request $request, Season $season)
    {
        try {
            $payload = Validator::make($request->request->all(), [
                'type' => 'required|in:left,right,final',
                'results' => 'required|array',
            ])->validate();

            $user = Auth::user();

            $season->{"results_" . trim($payload['type'])} = json_encode($payload['results']);
            $season->save();

            return response()->json([], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    protected function recalculateScores(Season $season)
    {
        $guesses = $season->guesses;

        foreach ($guesses as $guess) {
            $guess->calculateScore($season);
            $guess->user->recalculateScore();
        }
    }

    public function runRecalculateJob(Season $season)
    {
        $this->recalculateScores($season);

        return response()->json([]);
    }

    public function resetSeason(Season $season)
    {
        $season->results_left = '[]';
        $season->results_right = '[]';
        $season->results_final = '[]';
        $season->save();

        Guess::removeSeason($season);

        $this->combineTeams($season);

        $this->recalculateScores($season);

        return back();
    }
}
