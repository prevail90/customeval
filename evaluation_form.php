<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class mod_customeval_evaluation_form extends moodleform {
    protected $criteria;
    protected $formula;

    public function __construct($action=null, $customdata=null) {
        // Accept criteria and formula from outside
        $this->criteria = $customdata['criteria'] ?? [];
        $this->formula = $customdata['formula'] ?? '';
        parent::__construct($action, $customdata);
    }

    public function definition() {
        $mform = $this->_form;

        if (empty($this->criteria)) {
            $mform->addElement('static', 'nocriteria', '', get_string('nocriteria', 'mod_customeval'));
            return;
        }

        foreach ($this->criteria as $criterion) {
            $options = [];
            foreach ($criterion->answers as $answer) {
                $options[$answer->answerid] = format_text($answer->answertext);
            }

            $mform->addElement('select', 'criterion_' . $criterion->id, format_string($criterion->description), $options);
            $mform->setType('criterion_' . $criterion->id, PARAM_RAW);
            $mform->addRule('criterion_' . $criterion->id, null, 'required', null, 'client');
        }

        // Show grading formula, read-only
        $mform->addElement('static', 'formula', get_string('formula', 'mod_customeval'), format_text($this->formula));

        // Comments
        $mform->addElement('textarea', 'comments', get_string('comments', 'mod_customeval'), ['rows' => 5, 'cols' => 50]);
        $mform->setType('comments', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('submitgrade', 'mod_customeval'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Add any custom validation if needed
        return $errors;
    }
}
