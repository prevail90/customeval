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

function xmldb_customeval_upgrade(int $oldversion): bool {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024060100) {
        // Initial installation - handled by install.xml
        upgrade_mod_savepoint(true, 2024060100, 'customeval');
    }

    if ($oldversion < 2024060200) {
        // Add index for performance on customeval_grades table (corrected table name)
        $table = new xmldb_table('customeval_grades');
        $index = new xmldb_index('user_evaluator', XMLDB_INDEX_NOTUNIQUE, ['userid', 'evaluatorid']);

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2024060200, 'customeval');
    }

    if ($oldversion < 2024060300) {
        // Add mobilecompletion field to customeval table
        $table = new xmldb_table('customeval');
        $field = new xmldb_field('mobilecompletion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2024060300, 'customeval');
    }

    if ($oldversion < 2025052422) {
        // Add gradepass and maxgrade fields, and an index on gradepass in customeval table
        $table = new xmldb_table('customeval');

        $gradepass_field = new xmldb_field('gradepass', XMLDB_TYPE_NUMBER, '10,2', null, 
                                          XMLDB_NOTNULL, null, 0, 'maxgrade');
        if (!$dbman->field_exists($table, $gradepass_field)) {
            $dbman->add_field($table, $gradepass_field);
        }

        $maxgrade_field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '10,2', null, 
                                         XMLDB_NOTNULL, null, 100, 'formula');
        if (!$dbman->field_exists($table, $maxgrade_field)) {
            $dbman->add_field($table, $maxgrade_field);
        }

        $index = new xmldb_index('gradepass_idx', XMLDB_INDEX_NOTUNIQUE, ['gradepass']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2025052422, 'customeval');
    }

    if ($oldversion < 2025052521) {
        // Recheck schema against install.xml - no explicit changes
        upgrade_mod_savepoint(true, 2025052521, 'customeval');
    }

    if ($oldversion < 2025052601) {
        // Fix table name references or other small fixes if needed
        // (No explicit changes here; just bumping version for clarity)
        upgrade_mod_savepoint(true, 2025052601, 'customeval');
    }
    
    if ($oldversion < 2025052602) {
        // Place upgrade logic here (if needed), or leave empty if only a version bump

        // Example: (no structural change, just a bump)
        upgrade_mod_savepoint(true, 2025052602, 'customeval');
    }
    
    if ($oldversion < 2025052603) {
        // Place upgrade logic here (if needed), or leave empty if only a version bump

        // Example: (no structural change, just a bump)
        upgrade_mod_savepoint(true, 2025052603, 'customeval');
    }

    if ($oldversion < 2025052604) {
        // Place upgrade logic here (if needed), or leave empty if only a version bump

        // Example: (no structural change, just a bump)
        upgrade_mod_savepoint(true, 2025052604, 'customeval');
    }

     if ($oldversion < 2025052605) {
        // Place upgrade logic here (if needed), or leave empty if only a version bump

        // Example: (no structural change, just a bump)
        upgrade_mod_savepoint(true, 2025052605, 'customeval');
    }
    
    return true;
}

