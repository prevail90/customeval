<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Fetch the grade_item object for the given instance.
 *
 * @param int $instanceid
 * @return grade_item|false Grade item object or false if not found.
 */
function customeval_get_grade_item($instanceid) {
    return grade_item::fetch(array('itemmodule' => 'customeval', 'iteminstance' => $instanceid));
}

/**
 * Get the grade to pass for a given customeval instance.
 *
 * @param int $instanceid
 * @return float grade to pass or 0 if none set.
 */
function customeval_get_gradepass($instanceid) {
    $gradeitem = customeval_get_grade_item($instanceid);
    return $gradeitem ? $gradeitem->gradepass : 0;
}

/**
 * Get the max grade for a given customeval instance.
 *
 * @param int $instanceid
 * @return float max grade or 0 if none set.
 */
function customeval_get_maxgrade($instanceid) {
    $gradeitem = customeval_get_grade_item($instanceid);
    return $gradeitem ? $gradeitem->grademax : 0;
}

function customeval_add_instance($data, $mform) {
    global $DB;

    $record = new stdClass();
    $record->course = $data->course;
    $record->name = $data->name;
    $record->intro = $data->intro;
    $record->introformat = $data->introformat;
    $record->formula = $data->formula;
    $gradepass = customeval_get_gradepass($customeval->id);
    $maxgrade = customeval_get_maxgrade($customeval->id);

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
    $gradepass = customeval_get_gradepass($customeval->id);
    $maxgrade = customeval_get_maxgrade($customeval->id);

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
        'idnumber' => $customeval->cmidnumber
        $gradepass = customeval_get_gradepass($customeval->id);
        $maxgrade = customeval_get_maxgrade($customeval->id);
    ];

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/customeval', $customeval->course, 'mod', 
                      'customeval', $customeval->id, 0, $grades, $params);
}
