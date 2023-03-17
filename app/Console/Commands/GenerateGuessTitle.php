<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateGuessTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guess:title';

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
        $users = User::all();
        foreach ($users as $user) {
            foreach ($user->guesses as $guess) {
                $guess->title = 'Variant #' . ($guess->user->guesses()->count() + 1);
                $guess->save();
            }
        }

        return Command::SUCCESS;
    }
}
