<?php

namespace App\Console\Commands;

use App\Models\Guess;
use App\Models\Season;
use App\Models\User;
use Illuminate\Console\Command;

class TestCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $season = Season::first();
        /** @var Guess $guess */
        $guess = Guess::find(266);
//        $guess = Guess::where('user_id', 96)->first();
//        $guess = Guess::where('user_id', 1)->first();
        $guess->calculateScore($season);

        return Command::SUCCESS;
    }
}
