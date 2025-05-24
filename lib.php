<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Update grades in gradebook.
 */
function customeval_update_grades($customeval, $userid=0) {
    global $DB;

    $params = ['customevalid' => $customeval->id];
    $sql = "SELECT userid, grade 
            FROM {customeval_grades}
            WHERE customevalid = :customevalid";
    
    if ($userid) {
        $sql .= " AND userid = :userid";
        $params['userid'] = $userid;
    }

    if ($grades = $DB->get_records_sql($sql, $params)) {
        foreach ($grades as $grade) {
            $gradeitem = [
                'userid' => $grade->userid,
                'rawgrade' => $grade->grade,
                'dategraded' => time()
            ];
            grade_update('mod/customeval', $customeval->course, 'mod', 
                       'customeval', $customeval->id, 0, $gradeitem);
            customeval_grade_item_update($customeval);
        }
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
        case FEATURE_MOD_GRADEBOOK: return true;
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
        'gradepass' => $customeval->gradepass,
        'grademax' => $customeval->maxgrade
    ];

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/customeval', $customeval->course, 'mod', 
                      'customeval', $customeval->id, 0, $grades, $params);
}
