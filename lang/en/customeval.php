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

// ============ Core ============
$string['modulename'] = 'Custom Evaluations';
$string['modulenameplural'] = 'Custom Evaluations';
$string['modulename_help'] = 'The custom evaluation module allows creating customized evaluation forms with configurable criteria and scoring options.';
$string['pluginname'] = 'Custom Evaluations';
$string['pluginadministration'] = 'Custom Evaluations Administration';
$string['search:activity'] = 'Custom evaluations';
$string['activityloaded'] = 'Activity loaded';
$string['formloaded'] = 'Evaluation form loaded';

// New strings for the modernized form
$string['customsettings'] = 'Custom Settings';
$string['markcompletion'] = 'Mark completion when viewed';
$string['markcompletion_desc'] = 'If enabled, students must view this activity to mark it as complete.';
$string['namerequired'] = 'You must provide a name for this activity.';

// ============ Forms ============
$string['name'] = 'Evaluation Name';
$string['maxgrade'] = 'Maximum Grade';
$string['grading'] = 'Grading';
$string['gradetopass'] = 'Grade to pass';
$string['gradingformula'] = 'Grading Formula';
$string['gradingformula_help'] = 'Enter a PHP formula using option values (e.g., (GO*2 + NOGO*1)/3)';
$string['optiontext'] = 'Option Text';
$string['optionvalue'] = 'Option Value';
$string['weight'] = 'Weight';
$string['addmoreoptions'] = 'Add more options';
$string['criteria'] = 'Criteria';
$string['addcriterion'] = 'Add Criterion';
$string['sectionname'] = 'Section Name';
$string['sectiondescription'] = 'Description';

// ============ Evaluation ============
$string['evaluate'] = 'Evaluate';
$string['savesuccess'] = 'Evaluation saved successfully';
$string['saving'] = 'Saving evaluation...';
$string['saveevaluation'] = 'Save Evaluation';
$string['evaluationsaved'] = 'Evaluation saved successfully';
$string['evaluationfor'] = 'Evaluation for {$a}';
$string['comments'] = 'Comments';
$string['studentstoevaluate'] = 'Students to Evaluate';
$string['yourevaluations'] = 'Your Evaluations';
$string['evaluationswillappearhere'] = 'Completed evaluations will appear here when available';

// ============ Errors ============
$string['error:minoptions'] = 'You must provide at least {$a} options';
$string['error:uniquevalues'] = 'All option values must be unique';
$string['error:submissionfailed'] = 'Evaluation submission failed';
$string['error:invalidformula'] = 'Invalid grading formula syntax';
$string['error:nopermission'] = 'You don\'t have permission to perform this action';
$string['rendererror'] = 'Display error';
$string['saveerror'] = 'Error saving evaluation';

// ============ Mobile ============
$string['mobileapp:offlineevaluation'] = 'Evaluation saved for offline sync';
$string['mobileapp:synccomplete'] = 'Evaluations synced successfully';
