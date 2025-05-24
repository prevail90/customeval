<?php
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_customeval_mod_form extends moodleform_mod {
    public function definition() {
        global $PAGE;

        $mform = $this->_form;

        // General section
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        // Grading section
        $mform->addElement('header', 'grading', get_string('grading', 'mod_customeval'));
        $mform->addElement('text', 'maxgrade', get_string('maxgrade', 'mod_customeval'));
        $mform->setType('maxgrade', PARAM_INT);
        $mform->setDefault('maxgrade', 100);

        // Criteria and options would be managed via JS
        $mform->addElement('hidden', 'criteriajson', '');
        $mform->setType('criteriajson', PARAM_RAW);

        // Add AMD module initialization HERE
        $PAGE->requires->js_call_amd('mod_customeval/sectionmanager', 'init');

        // Add standard grading elements
        $this->standard_grading_coursemodule_elements();

        // Add "Grade to pass" field
        $mform->addElement('float', 'gradepass', get_string('gradepass', 'grades'));
        $mform->addHelpButton('gradepass', 'gradepass', 'grades');
        $mform->setDefault('gradepass', 0);
        $mform->setType('gradepass', PARAM_FLOAT);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
