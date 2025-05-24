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

    return true;
}
