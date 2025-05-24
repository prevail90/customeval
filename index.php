<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/mod/customeval/locallib.php');

$id = required_param('id', PARAM_INT); // Course ID

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/customeval/index.php', array('id' => $id));
$PAGE->set_title($course->shortname.': '.get_string('modulenameplural', 'mod_customeval'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_customeval'));

if (!$customevals = get_all_instances_in_course('customeval', $course)) {
    notice(get_string('thereareno', 'moodle', get_string('modulenameplural', 'mod_customeval')),
           new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = array(
    get_string('name'),
                     get_string('description')
);
$table->align = array('left', 'left');
$table->attributes = array('class' => 'mod-customeval-index-table');

foreach ($customevals as $customeval) {
    $url = new moodle_url('/mod/customeval/view.php', array('id' => $customeval->coursemodule));
    $table->data[] = array(
        html_writer::link($url, format_string($customeval->name)),
                           format_module_intro('customeval', $customeval, $customeval->coursemodule)
    );
}

echo html_writer::table($table);
echo $OUTPUT->footer();
