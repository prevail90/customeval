<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_customeval_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // General section
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Activity name (manually added)
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Standard description (intro + introformat)
        $this->standard_intro_elements();

        // Standard Moodle sections: grade, tags, restrict access, completion, etc.
        $this->standard_grading_coursemodule_elements(); // includes Grade section
        $this->standard_coursemodule_elements();          // includes Groups, Access restrictions, etc.

        // Action buttons
        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        return parent::validation($data, $files);
    }
}
