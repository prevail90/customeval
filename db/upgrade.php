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

function xmldb_mod_customeval_upgrade(int $oldversion): bool {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024060100) {
        // Initial installation - handled by install.xml
        upgrade_mod_savepoint(true, 2024060100, 'customeval');
    }

    if ($oldversion < 2024060200) {
        // Add index for performance
        $table = new xmldb_table('mod_customeval_ratings');
        $index = new xmldb_index('user_evaluator', XMLDB_INDEX_NOTUNIQUE, ['userid', 'evaluatorid']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2024060200, 'customeval');
    }

    if ($oldversion < 2024060300) {
        // Add mobile completion tracking
        $table = new xmldb_table('mod_customeval');
        $field = new xmldb_field('mobilecompletion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2024060300, 'customeval');
    }

    if ($oldversion < 2025052422) { // Update with your new version
        
        // Define table customeval to be modified
        $table = new xmldb_table('customeval');

        // Add gradepass field
        $gradepass_field = new xmldb_field('gradepass', XMLDB_TYPE_NUMBER, '10,2', null, 
                                         XMLDB_NOTNULL, null, 0, 'maxgrade');
        if (!$dbman->field_exists($table, $gradepass_field)) {
            $dbman->add_field($table, $gradepass_field);
        }

        // Add maxgrade field (if not already present)
        $maxgrade_field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '10,2', null, 
                                        XMLDB_NOTNULL, null, 100, 'formula');
        if (!$dbman->field_exists($table, $maxgrade_field)) {
            $dbman->add_field($table, $maxgrade_field);
        }

        // Conditionally add index for performance
        $index = new xmldb_index('gradepass_idx', XMLDB_INDEX_NOTUNIQUE, ['gradepass']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2025052422, 'customeval');
    }
    
    if ($oldversion < 2025052521) {
        // This forces Moodle to recheck the schema against install.xml
        // No explicit changes needed - Moodle auto-detects missing comments
        upgrade_mod_savepoint(true, 2025052521, 'customeval');
    }

    return true;
}
