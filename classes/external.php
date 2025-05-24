<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/../locallib.php');

class mod_customeval_external extends external_api {
    public static function view_activity_parameters() { /* ... */ }
    public static function view_activity($cmid) { /* ... */ }
    public static function view_activity_returns() { /* ... */ }

    public static function get_evaluation_form_parameters() { /* ... */ }
    public static function get_evaluation_form($cmid, $userid) { /* ... */ }
    public static function get_evaluation_form_returns() { /* ... */ }

    public static function submit_evaluation_parameters() { /* ... */ }
    public static function submit_evaluation($cmid, $userid, $formdata) { /* ... */ }
    public static function submit_evaluation_returns() { /* ... */ }
}

    // Get evaluation form structure
    public static function get_evaluation_form($cmid, $userid) {
        global $DB;

        $params = self::validate_parameters(self::get_evaluation_form_parameters(),
                                            ['cmid' => $cmid, 'userid' => $userid]);

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability('mod/customeval:evaluate', $context);

        list($course, $cm, $customeval) = mod_customeval_get_activity($params['cmid']);
        $student = $DB->get_record('user', ['id' => $params['userid']], '*', MUST_EXIST);

        // Same data preparation as view.php but for web service
        $sections = $DB->get_records('mod_customeval_sections', ['customevalid' => $customeval->id], 'sortorder');

        $result = [
            'cmid' => $cm->id,
            'userid' => $student->id,
            'studentname' => fullname($student),
            'sections' => []
        ];

        foreach ($sections as $section) {
            $criteria = $DB->get_records('mod_customeval_criteria', ['sectionid' => $section->id], 'sortorder');
            $sectiondata = [
                'sectionid' => $section->id,
                'name' => $section->name,
                'description' => $section->description,
                'criteria' => []
            ];

            foreach ($criteria as $criterion) {
                $options = $DB->get_records('mod_customeval_options',
                                            ['sectionid' => $section->id], 'sortorder');

                $sectiondata['criteria'][] = [
                    'id' => $criterion->id,
                    'description' => $criterion->description,
                    'options' => array_values($options)
                ];
            }

            $result['sections'][] = $sectiondata;
        }

        return $result;
    }

    public static function get_evaluation_form_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                                                'userid' => new external_value(PARAM_INT, 'User being evaluated')
        ]);
    }

    public static function get_evaluation_form_returns() {
        return new external_single_structure([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                                             'userid' => new external_value(PARAM_INT, 'User ID'),
                                             'studentname' => new external_value(PARAM_TEXT, 'Student name'),
                                             'sections' => new external_multiple_structure(
                                                 new external_single_structure([
                                                     'sectionid' => new external_value(PARAM_INT, 'Section ID'),
                                                                               'name' => new external_value(PARAM_TEXT, 'Section name'),
                                                                               'description' => new external_value(PARAM_TEXT, 'Section description'),
                                                                               'criteria' => new external_multiple_structure(
                                                                                   new external_single_structure([
                                                                                       'id' => new external_value(PARAM_INT, 'Criterion ID'),
                                                                                                                 'description' => new external_value(PARAM_TEXT, 'Criterion text'),
                                                                                                                 'options' => new external_multiple_structure(
                                                                                                                     new external_single_structure([
                                                                                                                         'id' => new external_value(PARAM_INT, 'Option ID'),
                                                                                                                                                   'optiontext' => new external_value(PARAM_TEXT, 'Display text'),
                                                                                                                                                   'optionvalue' => new external_value(PARAM_TEXT, 'Value for formula'),
                                                                                                                                                   'weight' => new external_value(PARAM_FLOAT, 'Numeric weight')
                                                                                                                     ]), 'Evaluation options')
                                                                                   ]), 'Section criteria')
                                                 ]), 'Activity sections')
        ]);
    }

    // Submit evaluation
    public static function submit_evaluation($cmid, $userid, $formdata) {
        global $DB, $USER;

        $params = self::validate_parameters(self::submit_evaluation_parameters(), [
            'cmid' => $cmid,
            'userid' => $userid,
            'formdata' => $formdata
        ]);

        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        require_capability('mod/customeval:evaluate', $context);

        list($course, $cm, $customeval) = mod_customeval_get_activity($params['cmid']);
        $student = $DB->get_record('user', ['id' => $params['userid']], '*', MUST_EXIST);

        // Process form data
        $data = [];
        foreach ($params['formdata'] as $item) {
            if (strpos($item['name'], 'criteria[') === 0) {
                preg_match('/criteria\[(\d+)\]/', $item['name'], $matches);
                $data['criteria'][$matches[1]] = $item['value'];
            } elseif (strpos($item['name'], 'comments[') === 0) {
                preg_match('/comments\[(\d+)\]/', $item['name'], $matches);
                $data['comments'][$matches[1]] = $item['value'];
            }
        }

        // Save to database (implementation from locallib.php)
        mod_customeval_save_evaluation($customeval, $data, $USER->id);

        return [
            'success' => true,
            'message' => get_string('evaluationsaved', 'mod_customeval')
        ];
    }

    public static function submit_evaluation_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                                                'userid' => new external_value(PARAM_INT, 'User being evaluated'),
                                                'formdata' => new external_multiple_structure(
                                                    new external_single_structure([
                                                        'name' => new external_value(PARAM_TEXT, 'Form field name'),
                                                                                  'value' => new external_value(PARAM_RAW, 'Form field value')
                                                    ]), 'Submitted form data')
        ]);
    }

    public static function submit_evaluation_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'True if successful'),
                                             'message' => new external_value(PARAM_TEXT, 'Status message')
        ]);
    }

    // Sync offline data (stub implementation)
    public static function sync_offline_data($cmid, $data) {
        // Implement your offline sync logic here
        return ['status' => 'notimplemented'];
    }
}
