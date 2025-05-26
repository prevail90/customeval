<?php
defined('MOODLE_INTERNAL') || die();

function customeval_add_instance($data, $mform) {
    global $DB;

    $record = new stdClass();
    $record->course = $data->course;
    $record->name = $data->name;
    $record->intro = $data->intro;
    $record->introformat = $data->introformat;
    $record->formula = $data->formula;
    $record->gradepass = $data->gradepass;

    $record->timecreated = time();

    $id = $DB->insert_record('customeval', $record);
    $record->id = $id;

    customeval_grade_item_update($record);

    return $id;
}

function customeval_update_instance($data, $mform) {
    global $DB;

    $record = new stdClass();
    $record->id = $data->instance;
    $record->course = $data->course;
    $record->name = $data->name;
    $record->intro = $data->intro;
    $record->introformat = $data->introformat;
    $record->formula = $data->formula;
    $record->gradepass = $data->gradepass;

    $record->timemodified = time();

    $DB->update_record('customeval', $record);

    customeval_grade_item_update($record);

    return true;
}

/**
 * Update grades in gradebook.
 */
function customeval_update_grades($customeval, $userid = 0) {
    global $DB;

    $params = ['customevalid' => $customeval->id];
    $sql = "SELECT userid, grade 
            FROM {customeval_grades}
            WHERE customevalid = :customevalid";
    
    if ($userid) {
        $sql .= " AND userid = :userid";
        $params['userid'] = $userid;
    }

    if ($records = $DB->get_records_sql($sql, $params)) {
        $grades = [];
        foreach ($records as $record) {
            $grades[$record->userid] = [
                'rawgrade' => $record->grade,
                'dategraded' => time()
            ];
        }

        customeval_grade_item_update($customeval, $grades); // Only call ONCE here with all grades
    } else {
        // No grades found; send null grades if needed
        customeval_grade_item_update($customeval);
    }
}


/**
 * Get all grades for activity.
 */
function customeval_get_grades($customevalid) {
    global $DB;
    return $DB->get_records('customeval_grades', ['customevalid' => $customevalid]);
}

/**
 * Delete all grades when activity is deleted.
 */
function customeval_delete_grades($customevalid) {
    global $DB;
    $DB->delete_records('customeval_grades', ['customevalid' => $customevalid]);
}

// Standard Moodle module functions
function customeval_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE: return true;
        case FEATURE_GRADE_OUTCOMES: return true;
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_BACKUP_MOODLE2: return true;
        default: return null;
    }
}


function customeval_grade_item_update($customeval, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $params = [
        'itemname' => $customeval->name,
        'idnumber' => $customeval->cmidnumber,
        'gradepass' => $customeval->gradepass
    ];

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/customeval', $customeval->course, 'mod', 
                      'customeval', $customeval->id, 0, $grades, $params);
}
