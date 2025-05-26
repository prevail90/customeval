<?php
require_once($CFG->libdir . '/formslib.php');

class customeval_setup_form extends moodleform {

    public function definition() {
        $mform = $this->_form;
        $customeval = $this->_customdata['customeval'];

        $mform->addElement('header', 'setupheader', get_string('definecriteria', 'customeval'));

        // Add 5 repeating elements for criteria by default
        $repeatcount = 5;
        $repeatoptions = [];
        $repeatels = [
            $mform->createElement('text', 'criteria', get_string('criterion', 'customeval')),
        ];
        $mform->setType('criteria', PARAM_TEXT);
        $this->repeat_elements($repeatels, $repeatcount, $repeatoptions, 'criteria_repeats', 'criteria_add_fields', 1, get_string('addmorecriteria', 'customeval'), true);

        $mform->addElement('header', 'formulaheader', get_string('gradingformula', 'customeval'));
        $mform->addElement('textarea', 'formula', get_string('formula', 'customeval'), ['rows' => 4, 'cols' => 70]);
        $mform->setType('formula', PARAM_RAW);
        $mform->addRule('formula', null, 'required', null, 'client');

        $this->add_action_buttons();
    }

    public function set_data($defaultvalues) {
        // Optionally prefill criteria if available
        parent::set_data($defaultvalues);
    }
}
