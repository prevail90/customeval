<?php
defined('MOODLE_INTERNAL') || die();

class mod_customeval_mobile {

    public static function mobile_course_view($args) {
        global $CFG, $DB, $OUTPUT;

        require_once($CFG->dirroot . '/mod/customeval/locallib.php');

        $args = (object) $args;
        $cm = get_coursemodule_from_id('customeval', $args->cmid);
        $customeval = $DB->get_record('customeval', ['id' => $cm->instance]);
        $context = context_module::instance($cm->id);

        // Prepare data for mobile app
        $data = [
            'cmid' => $cm->id,
            'courseid' => $cm->course,
            'name' => format_string($customeval->name),
            'intro' => format_module_intro('customeval', $customeval, $cm->id),
            'canEvaluate' => has_capability('mod/customeval:evaluate', $context),
            'canBeEvaluated' => has_capability('mod/customeval:beevaluated', $context),
            'students' => []
        ];

        if ($data['canEvaluate']) {
            $students = get_enrolled_users($context, 'mod/customeval:beevaluated');
            foreach ($students as $student) {
                $data['students'][] = [
                    'id' => $student->id,
                    'fullname' => fullname($student),
                    'picture' => new moodle_url('/user/pix.php/'.$student->id.'/f2.jpg')
                ];
            }
        }

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_customeval/mobile_view', $data)
                ]
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/mod/customeval/amd/build/mobile.min.js'),
            'otherdata' => json_encode(['offline' => true]),
            'files' => []
        ];
    }
}
