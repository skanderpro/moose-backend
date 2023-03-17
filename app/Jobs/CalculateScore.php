<?php

namespace App\Jobs;

use App\Mail\ResultsMail;
use App\Models\Guess;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculateScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $season = Season::first();
        $guesses = $season->guesses;

        /** @var Guess $guess */
        foreach ($guesses as $guess) {
            $guess->calculateScore($season);
            try {
                Mail::to($guess->user->email)->send(new ResultsMail($guess->user));
            } catch (\Exception $e) {
                Log::error($e->getMessage(), [
                    'error' => $e,
                ]);
            }
        }
    }
}
