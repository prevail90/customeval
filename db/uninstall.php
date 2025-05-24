<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_mod_customeval_uninstall() {
    global $DB;

    // Additional cleanup if needed
    // For example, remove any custom admin settings
    unset_all_config_for_plugin('mod_customeval');

    return true;
}
