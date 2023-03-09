<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ResetTeamsAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Reset teams';
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right',
            'data-role' => 'reset-btn',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'seasons';
    }

    public function getDefaultRoute()
    {
        return route('voyager.seasons.reset.teams', [
            'season' => $this->data,
        ]);
    }
}
