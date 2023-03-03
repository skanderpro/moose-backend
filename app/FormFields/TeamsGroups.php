<?php

namespace App\FormFields;

use App\Models\SeasonTeam;
use App\Models\Team;
use TCG\Voyager\FormFields\AbstractHandler;

class TeamsGroups extends AbstractHandler
{
    protected $codename = 'teams_groups';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $groups = [
            [
                'key' => 'group_a',
                'title' => 'Group A',
            ],
            [
                'key' => 'group_b',
                'title' => 'Group B',
            ],
            [
                'key' => 'group_c',
                'title' => 'Group C',
            ],
            [
                'key' => 'group_d',
                'title' => 'Group D',
            ],
        ];

        $teams = Team::all();

        $values = SeasonTeam::all()->groupBy('group')->toArray();
        foreach ($values as $group => $value) {
            $_v = [];
            foreach ($value as $st) {
                $_v[$st['rating'] - 1] = $st['team_id'];
            }
            $values[$group] = $_v;
        }

        return view('formfields.teams-groups', [
            'row' => $row,
            'teams' => $teams,
            'values' => $values,
            'groups' => $groups,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}
