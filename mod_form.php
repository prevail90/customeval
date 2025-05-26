<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_customeval_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // General section header (modern Moodle convention)
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name field (using standard 'name' string)
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client'); // Client-side validation
        $mform->addHelpButton('name', 'name', 'mod_customeval'); // Help button

        // Formula field (required)
        $mform->addElement('textarea', 'formula', get_string('formula', 'mod_customeval'), ['rows' => 4, 'cols' => 60]);
        $mform->setType('formula', PARAM_RAW); // Allow formulas like (count(s1)/...)
        $mform->addRule('formula', null, 'required', null, 'client'); // Make it required
        $mform->setDefault('formula', ''); // Default to empty string
        $mform->addHelpButton('formula', 'formula', 'mod_customeval'); // Optional help

        // Description field (modern editor with file upload support)
        $this->standard_intro_elements(get_string('description', 'mod_customeval'));
        
        // Custom settings section
        $mform->addElement('header', 'customsettings', get_string('customsettings', 'mod_customeval'));
        
        // Modern "advcheckbox" instead of basic checkbox
        $mform->addElement('advcheckbox', 'markcompletion', 
            get_string('markcompletion', 'mod_customeval'),
            get_string('markcompletion_desc', 'mod_customeval') // Description
        );
        $mform->setDefault('markcompletion', 0);

        // Add standard activity elements (completion, tags, etc.)
        $this->standard_coursemodule_elements();

        // Action buttons (modern explicit declaration)
        $this->add_action_buttons();
    }

    // Add custom validation
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Example: Ensure name is not empty
        if (empty(trim($data['name']))) {
            $errors['name'] = get_string('namerequired', 'mod_customeval');
        }

        return $errors;
    }

    // Optional: Preprocess data for file managers or editors
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);
        
        // Example for future file upload support:
        /*
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('somefile');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_customeval', 'content', 0);
            $defaultvalues['somefile'] = $draftitemid;
        }
        */
    }
}
