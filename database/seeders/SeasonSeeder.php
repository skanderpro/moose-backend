<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Season;
use App\Models\SeasonTeam;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $season = Season::create([
            'title' => 'Test seasons',
            'start' => NOW()->add('days', 3),
            'is_active' => true,
            'results_left' => '[]',
            'results_right' => '[]',
            'results_final' => '[]',
        ]);

        $i = 0;
        $j = 0;
        $group = 0;
        $rating = 1;
        while ($i < 32) {
            $fTeam = Team::create([
                'name' => 't-' . $i . '-1',
                'score' => 0,
                'logo' => '/',
            ]);

            SeasonTeam::create([
                'team_id' => $fTeam->id,
                'season_id' => $season->id,
                'rating' => $rating,
                'group' => 'g-' . $group,
            ]);

            if ($rating++ >= 16) {
                $rating = 1;
                $group++;
            }

            $sTeam = Team::create([
                'name' => 't-' . $i . '-2',
                'score' => 0,
                'logo' => '/',
            ]);

            SeasonTeam::create([
                'team_id' => $sTeam->id,
                'season_id' => $season->id,
                'rating' => $rating,
                'group' => 'g-' . $group,
            ]);

            if ($rating++ >= 16) {
                $rating = 1;
                $group++;
            }

            $i++;

            Game::create([
               'first_team_id' => $fTeam->id,
               'second_team_id' => $sTeam->id,
               'season_id' => $season->id,
               'type' => $j++ % 2 ? 'right' : 'left',
            ]);
        }
    }
}
