<?php
defined('MOODLE_INTERNAL') || die();

function customeval_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_MOD_PURPOSE: return MOD_PURPOSE_ASSESSMENT;
        default: return null;
    }
}

function customeval_add_instance($data, $mform = null) {
    global $DB;
    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    return $DB->insert_record('mod_customeval', $data);
}

function customeval_update_instance($data, $mform) {
    global $DB;
    $data->timemodified = time();
    $data->id = $data->instance;
    return $DB->update_record('mod_customeval', $data);
}

function customeval_delete_instance($id) {
    global $DB;

    if (!$customeval = $DB->get_record('mod_customeval', ['id' => $id])) {
        return false;
    }

    // Delete all related records
    $DB->delete_records('mod_customeval_sections', ['customevalid' => $id]);
    $DB->delete_records('mod_customeval_criteria', ['customevalid' => $id]);
    $DB->delete_records('mod_customeval_options', ['customevalid' => $id]);
    $DB->delete_records('mod_customeval_ratings', ['customevalid' => $id]);

    // Delete the main record
    $DB->delete_records('mod_customeval', ['id' => $id]);

    return true;
}

// Add this function for complete uninstallation
function customeval_uninstall() {
    global $DB;

    // Drop all plugin tables
    $dbman = $DB->get_manager();

    $tables = [
        'mod_customeval',
        'mod_customeval_sections',
        'mod_customeval_criteria',
        'mod_customeval_options',
        'mod_customeval_ratings'
    ];

    foreach ($tables as $table) {
        $table = new xmldb_table($table);
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
    }

    // Clean up any other plugin data (file storage, etc.)
    $fs = get_file_storage();
    $fs->delete_area_files(context_system::instance()->id, 'mod_customeval');

    return true;
}

function mod_customeval_get_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/mod/customeval/styles.css');
}
