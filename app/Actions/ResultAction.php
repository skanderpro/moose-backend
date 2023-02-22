<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ResultAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Result Action';
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
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'seasons';
    }

    public function getDefaultRoute()
    {
        return route('voyager.seasons.results', [
            'seasons' => $this->data,
        ]);
    }
}
