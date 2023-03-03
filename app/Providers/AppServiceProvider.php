<?php

namespace App\Providers;

use App\Actions\ResetAction;
use App\Actions\ResultAction;
use App\FormFields\TeamsGroups;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(ResultAction::class);
        Voyager::addAction(ResetAction::class);
        Voyager::addFormField(TeamsGroups::class);
    }
}
