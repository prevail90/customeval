<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class mod_customeval_evaluation_form extends moodleform {
    public function definition() {
        global $DB;

        $mform = $this->_form;
        $customdata = $this->_customdata;

        $criteria = $customdata['criteria'];
        $options = $customdata['options'];

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        foreach ($criteria as $criterion) {
            $mform->addElement('header', 'criterion_'.$criterion->id,
                               format_text($criterion->description, FORMAT_HTML));

            $radioarray = array();
            foreach ($options as $option) {
                if ($option->sectionid == $criterion->sectionid) {
                    $radioarray[] = $mform->createElement('radio', 'criteria['.$criterion->id.']', '',
                                                          format_string($option->optiontext), $option->id);
                }
            }

            $mform->addGroup($radioarray, 'criteria_'.$criterion->id, '', array('<br>'), false);
            $mform->addRule('criteria_'.$criterion->id, get_string('required'), 'required', null, 'client');

            $mform->addElement('textarea', 'comments['.$criterion->id.']',
                               get_string('comments', 'mod_customeval'),
                               array('rows' => 3, 'cols' => 60, 'class' => 'form-control'));
        }

        $this->add_action_buttons(true, get_string('saveevaluation', 'mod_customeval'));
    }
}
