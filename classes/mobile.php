<?php
defined('MOODLE_INTERNAL') || die();

class mod_customeval_mobile {

    public static function view_activity($args) {
        global $CFG, $DB, $USER, $OUTPUT;

        require_once($CFG->dirroot . '/mod/customeval/locallib.php');

        $args = (object) $args;
        $cm = get_coursemodule_from_id('customeval', $args->cmid);
        $customeval = $DB->get_record('customeval', ['id' => $cm->instance]);
        $context = context_module::instance($cm->id);

        require_capability('mod/customeval:view', $context);

        $data = [
            'cmid' => $cm->id,
            'courseid' => $cm->course,
            'name' => $customeval->name,
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
                    'html' => $OUTPUT->render_from_template('mod_customeval/mobile/view_activity', $data)
                ]
            ],
            'javascript' => '',
            'otherdata' => json_encode(['offline' => true]),
            'files' => []
        ];
    }
}
