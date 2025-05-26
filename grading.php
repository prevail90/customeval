<?php
require_once('../../config.php');
require_once('setup_form.php');

$id = required_param('id', PARAM_INT); // Course module ID
$cm = get_coursemodule_from_id('customeval', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$customeval = $DB->get_record('customeval', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/customeval:manage', context_module::instance($cm->id));

$PAGE->set_url('/mod/customeval/setup.php', ['id' => $id]);
$PAGE->set_title(get_string('setupactivity', 'customeval'));
$PAGE->set_heading($course->fullname);

$mform = new customeval_setup_form(null, ['customeval' => $customeval]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/customeval/view.php', ['id' => $id]));
} elseif ($data = $mform->get_data()) {
    // Update formula
    $customeval->formula = $data->formula;
    $DB->update_record('customeval', $customeval);

    // Delete old criteria
    $DB->delete_records('customeval_criteria', ['customevalid' => $customeval->id]);

    // Save new criteria
    foreach ($data->criteria as $index => $criterion) {
        if (!empty(trim($criterion))) {
            $record = new stdClass();
            $record->customevalid = $customeval->id;
            $record->description = $criterion;
            $record->sortorder = $index;
            $DB->insert_record('customeval_criteria', $record);
        }
    }

    redirect(new moodle_url('/mod/customeval/view.php', ['id' => $id]),
        get_string('setupsaved', 'customeval'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('setupactivity', 'customeval'));
$mform->display();
echo $OUTPUT->footer();
