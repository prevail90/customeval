<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_customeval_view_activity' => [
        'classname'     => 'mod_customeval_external',
'methodname'    => 'view_activity',
'description'   => 'Get activity data for mobile view',
'type'          => 'read',
'ajax'          => true,
'capabilities'  => 'mod/customeval:view',
'services'      => ['mobile_service'],
'loginrequired' => true
    ],
'mod_customeval_get_evaluation_form' => [
    'classname'     => 'mod_customeval_external',
'methodname'    => 'get_evaluation_form',
'description'   => 'Get evaluation form structure',
'type'          => 'read',
'ajax'          => true,
'capabilities'  => 'mod/customeval:evaluate',
'services'      => ['mobile_service'],
'loginrequired' => true
],
'mod_customeval_submit_evaluation' => [
    'classname'     => 'mod_customeval_external',
'methodname'    => 'submit_evaluation',
'description'   => 'Submit evaluation data',
'type'          => 'write',
'ajax'          => true,
'capabilities'  => 'mod/customeval:evaluate',
'services'      => ['mobile_service'],
'loginrequired' => true
]
];

$services = [
    'Custom Evaluations Mobile Service' => [
        'shortname'         => 'mobile_service',
'functions'         => [
    'mod_customeval_view_activity',
'mod_customeval_get_evaluation_form',
'mod_customeval_submit_evaluation'
],
'restrictedusers'   => 0,
'enabled'           => 1,
'downloadfiles'     => 1,
'uploadfiles'       => 1,
'offlinefunctions'  => [
    'mod_customeval_submit_evaluation' => [
        'uploadfiles' => false,
'timeout'     => 300
    ]
]
    ]
];
