<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_customeval_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // General section (adds standard 'name', 'intro', 'introformat')
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $this->add_intro_editor(true);

        // Custom settings section
        $mform->addElement('header', 'customsettings', get_string('customsettings', 'mod_customeval'));

        // Formula field (required)
        $mform->addElement('textarea', 'formula', get_string('formula', 'mod_customeval'), ['rows' => 4, 'cols' => 60]);
        $mform->setType('formula', PARAM_RAW);
        $mform->addRule('formula', null, 'required', null, 'client');
        $mform->setDefault('formula', '');
        $mform->addHelpButton('formula', 'formula', 'mod_customeval');

        // Custom checkbox (optional setting)
        $mform->addElement('advcheckbox', 'markcompletion',
            get_string('markcompletion', 'mod_customeval'),
            get_string('markcompletion_desc', 'mod_customeval')
        );
        $mform->setDefault('markcompletion', 0);

        // Standard module elements (groups, availability, completion, etc.)
        $this->standard_coursemodule_elements();

        // Action buttons
        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty(trim($data['formula']))) {
            $errors['formula'] = get_string('required');
        }

        return $errors;
    }

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);
        // Add any required preprocessing here
    }
}
