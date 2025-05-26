<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/customeval/locallib.php');

$id = required_param('id', PARAM_INT); // Course module ID
$userid = optional_param('userid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

list($course, $cm, $customeval) = customeval_get_activity($id);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/customeval/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($customeval->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('limitedwidth');

$tab = optional_param('tab', 'evaluations', PARAM_ALPHA);

$tabs = [
    new tabobject('evaluations',
        new moodle_url('/mod/customeval/view.php', ['id' => $cm->id, 'tab' => 'evaluations']),
        get_string('evaluations', 'customeval')),
];

if (has_capability('mod/customeval:manage', $context)) {
    $tabs[] = new tabobject('grading',
        new moodle_url('/mod/customeval/view.php', ['id' => $cm->id, 'tab' => 'grading']),
        get_string('advancedgrading', 'customeval'));
}

print_tabs([$tabs], $tab);

if ($tab === 'grading' && has_capability('mod/customeval:manage', $context)) {
    require_once('grading_form.php');
    $mform = new grading_form(null, ['customevalid' => $customeval->id]);

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/mod/customeval/view.php', ['id' => $cm->id, 'tab' => 'evaluations']));
    } else if ($data = $mform->get_data()) {
        // Save grading criteria/formulas here (your DB code)
        // Then redirect back to evaluations tab or stay on grading tab
        redirect(new moodle_url('/mod/customeval/view.php', ['id' => $cm->id, 'tab' => 'grading']));
    } else {
        $mform->display();
    }
} else if ($action === 'evaluate') {
    require_capability('mod/customeval:evaluate', $context);
    $userid = required_param('userid', PARAM_INT);
} else {
    require_capability('mod/customeval:view', $context);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($customeval->name));

if (has_capability('mod/customeval:manage', $context)) {
    $setupurl = new moodle_url('/mod/customeval/setup.php', ['id' => $cm->id]);
    echo $OUTPUT->single_button($setupurl, get_string('setupactivity', 'customeval'));
}

if (!empty($customeval->intro)) {
    echo $OUTPUT->box(format_module_intro('customeval', $customeval, $cm->id),
                      'generalbox mod_introbox', 'customevalintro');
}

if ($action === 'evaluate') {
    $student = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    $sections = $DB->get_records('mod_customeval_sections',
                                 array('customevalid' => $customeval->id),
                                 'sortorder ASC');

    $data = array(
        'cmid' => $cm->id,
        'userid' => $userid,
        'studentname' => fullname($student),
                  'sections' => array()
    );

    foreach ($sections as $section) {
        $criteria = $DB->get_records('mod_customeval_criteria',
                                     array('sectionid' => $section->id),
                                     'sortorder ASC');

        $sectiondata = array(
            'sectionid' => $section->id,
            'name' => format_string($section->name),
                             'description' => format_text($section->description),
                             'criteria' => array()
        );

        foreach ($criteria as $criterion) {
            $options = $DB->get_records('mod_customeval_options',
                                        array('sectionid' => $section->id),
                                        'sortorder ASC');

            $sectiondata['criteria'][] = array(
                'id' => $criterion->id,
                'description' => format_string($criterion->description),
                                               'options' => array_values($options)
            );
        }

        $data['sections'][] = $sectiondata;
    }

    $renderer = $PAGE->get_renderer('mod_customeval');
    echo $renderer->render_evaluation_form($data);

} else {
    if (has_capability('mod/customeval:evaluate', $context)) {
        $students = get_enrolled_users($context, 'mod/customeval:beevaluated');

        if (!empty($students)) {
            echo html_writer::start_div('mod-customeval-student-list');
            echo html_writer::tag('h3', get_string('studentstoevaluate', 'mod_customeval'));

            foreach ($students as $student) {
                $url = new moodle_url('/mod/customeval/view.php', array(
                    'id' => $cm->id,
                    'userid' => $student->id,
                    'action' => 'evaluate'
                ));

                echo html_writer::start_div('mod-customeval-student-item');
                echo html_writer::link($url, fullname($student));
                echo html_writer::end_div();
            }

            echo html_writer::end_div();
        } else {
            echo $OUTPUT->notification(get_string('nostudentsfound', 'mod_customeval'), 'notifyinfo');
        }
    } else {
        $evaluations = customeval_get_user_evaluations($customeval, $USER->id);

        if (!empty($evaluations)) {
            echo html_writer::start_div('mod-customeval-evaluation-results');
            echo html_writer::tag('h3', get_string('yourevaluations', 'mod_customeval'));

            foreach ($evaluations as $evaluation) {
                // Display evaluation results here
            }

            echo html_writer::end_div();
        } else {
            echo $OUTPUT->notification(get_string('noevaluationsyet', 'mod_customeval'), 'notifyinfo');
        }
    }
}

echo $OUTPUT->footer();
