<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/course/moodleform_mod.php';

require_once __DIR__ . '/locallib.php';

/* @var $PAGE moodle_page */

/**
 * Module instance settings form
 */
class mod_amcquiz_mod_form extends moodleform_mod {
    /**
     * @var mod_amcquiz\entity\amcquiz
     */
    protected $current;

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $PAGE;
        $PAGE->requires->js_call_amd('mod_amcquiz/mod_form', 'init');
        $mform = $this->_form;
        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('modform_amcquizname', 'mod_amcquiz'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Use a latex file to define the MCQ
        $mform->addElement(
            'advcheckbox',
            'uselatexfile',
            get_string('modform_uselatexfile', 'mod_amcquiz'),
            get_string('modform_uselatexfilelabel', 'mod_amcquiz'),
            null,
            [0,1]
        );
        $mform->setType('uselatexfile', PARAM_BOOL);
        // latex file upload
        $mform->addElement('filepicker', 'latexfile', get_string('modform_latexfile', 'mod_amcquiz'), null, ['accepted_types' => 'tex']);
        $mform->disabledIf('latexfile', 'uselatexfile', 'eq', 0);


        $mform->setDefault('amc[lstudent]', get_config('mod_amcquiz', 'instructionslstudent'));
        $mform->setDefault('amc[lname]', get_config('mod_amcquiz', 'instructionslnamestd'));



        // Instructions
        $mform->addElement('header', 'general', get_string('modform_instructionsheader', 'mod_amcquiz'));
        $mform->addElement('select', 'instructions_select', get_string('modform_top_instructions_predefined', 'mod_amcquiz'), parse_default_instructions());

        $mform->addHelpButton('instructions_select', 'modform_top_instructions_predefined_help', 'mod_amcquiz');
        $mform->disabledIf('instructions_select', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'editor',
            'amc[instructionsprefix]',
            get_string('modform_top_instructions', 'mod_amcquiz'),
            array(
                'rows' => '4',
                'cols' => '64'
            )
        );
        $mform->setType('amc[instructionsprefix]', PARAM_RAW);
        $mform->disabledIf('amc[instructionsprefix]', 'uselatexfile', 'eq', 1);

        $mform->addElement('editor', 'description', get_string('modform_description', 'mod_amcquiz'), array('rows'=>'6', 'cols'=>'64'));
        $mform->setType('description', PARAM_RAW);
        $mform->addHelpButton('description', 'modform_description_help', 'mod_amcquiz');
        $mform->disabledIf('description', 'uselatexfile', 'eq', 1);


        $mform->addElement('advcheckbox', 'anonymous', get_string('modform_anonymous', 'mod_amcquiz'));

        $mform->addElement('text', 'amc[lstudent]', get_string('modform_studentnumber_instructions', 'mod_amcquiz'), array('size' => 64));
        $mform->setType('amc[lstudent]', PARAM_TEXT);
        $mform->disabledIf('amc[lstudent]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'text',
            'amc[lname]',
            get_string('modform_studentname_instructions', 'amcquiz'),
            array(
                'data-std' => get_config('mod_amcquiz', 'instructionslnamestd'),
                'data-anon' => get_config('mod_amcquiz', 'instructionslnameanon'),
            )
        );
        $mform->setType('amc[lname]', PARAM_TEXT);
        $mform->disabledIf('amc[lname]', 'uselatexfile', 'eq', 1);

        // AMC settings
        //-------------------------------------------------------------------------------
        // Adding the "amcparams" fieldset, parameters specific to printed output
        $mform->addElement('header', 'amcparameters', get_string('modform_amc_parameters_header', 'mod_amcquiz'));

        $mform->addElement('text', 'amc[copies]', get_string('modform_sheets_versions', 'mod_amcquiz'));
        $mform->setType('amc[copies]', PARAM_INTEGER);
        $mform->setDefault('amc[copies]', 1);
        $mform->disabledIf('amc[copies]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'amc[questionsColumns]',
            get_string('modform_questions_columns', 'mod_amcquiz'),
            array("Auto", 1, 2)
        );
        $mform->disabledIf('amc[questionsColumns]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'amc[shuffleq]', get_string('modform_shuffle_questions', 'mod_amcquiz'));
        $mform->setType('amc[shuffleq]', PARAM_BOOL);
        $mform->disabledIf('amc[shuffleq]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'amc[shufflea]', get_string('modform_shuffle_answers', 'mod_amcquiz'));
        $mform->setType('amc[shufflea]', PARAM_BOOL);
        $mform->disabledIf('amc[shufflea]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'amc[separatesheet]', get_string('modform_separate_sheets', 'mod_amcquiz'));
        $mform->setType('amc[separatesheet]', PARAM_BOOL);
        $mform->disabledIf('amc[separatesheet]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'amc[answerSheetColumns]',
            get_string('modform_sheets_columns', 'mod_amcquiz'),
            array("Auto", 1, 2, 3, 4)
        );
        $mform->disabledIf('amc[answerSheetColumns]', 'amc[separatesheet]', 'eq', 0);
        $mform->disabledIf('amc[answerSheetColumns]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'amc[displaypoints]',
            get_string('modform_display_scores', 'mod_amcquiz'),
            [
              get_string('modform_display_scores_no', 'mod_amcquiz'),
              get_string('modform_display_scores_beginning', 'mod_amcquiz'),
              get_string('modform_display_scores_end', 'mod_amcquiz')
            ]
        );
        $mform->setType('amc[displaypoints]', PARAM_INTEGER);
        $mform->disabledIf('amc[displaypoints]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'amc[markmulti]', get_string('modform_mark_multi', 'mod_amcquiz'));
        $mform->setType('amc[markmulti]', PARAM_BOOL);
        $mform->addHelpButton('amc[markmulti]', 'modform_mark_multi_help', 'mod_amcquiz');
        $mform->disabledIf('amc[markmulti]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'amc[score]', get_string('modform_display_score_rules', 'mod_amcquiz'));
        $mform->setType('amc[score]', PARAM_BOOL);
        $mform->addHelpButton('amc[score]', 'modform_display_score_rules_help', 'mod_amcquiz');
        $mform->disabledIf('amc[score]', 'uselatexfile', 'eq', 1);

        $mform->addElement('textarea', 'amc[customlayout]', get_string('modform_custom_layout', 'mod_amcquiz'), array('rows'=>'3', 'cols'=>'64'));
        $mform->setType('amc[customlayout]', PARAM_TEXT);
        $mform->addHelpButton('amc[customlayout]', 'amc_customlayout', 'amcquiz');
        $mform->disabledIf('amc[customlayout]', 'uselatexfile', 'eq', 1);
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons(true, null, false);

    }

    /**
     * Only available on moodleform_mod.
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values)
    {

        /*if (isset($default_values['description'])) {
            $default_values['description'] = array('text' => $default_values['description']);
        }

        // Convert from JSON to array
        if (!empty($default_values['amcparams'])) {

            $params = \mod_amcquiz\local\amc\params::fromJson($default_values['amcparams']);
            $default_values['amc'] = (array) $params;
            $default_values['amc']['instructionsprefix'] = array(
                'text' => $params->instructionsprefix,
            );
            $this->_form->setDefault(
                'amc[instructionsprefix]',
                array(
                    'text' => $params->instructionsprefix,
                )
            );

            if (!empty($this->current->id) && !empty($params->locked)) {
                $this->_form->freeze(
                    array(
                        'qnumber',
                        'amc[copies]',
                        'amc[shuffleq]',
                        'amc[shufflea]',
                        'amc[separatesheet]',
                        'amc[displaypoints]',
                        'amc[markmulti]',
                        'amc[customlayout]',
                        'amc[score]',
                    )
                );
            } else if (!empty($this->current->uselatexfile) && !$this->current->uselatexfile) {
                // Only add the required rule if the field is not disabled
                $this->_form->addRule('amc[copies]', null, 'required', null, 'client');
            }

            $this->_form->setDefault('instructions', '');
            foreach (parse_default_instructions() as $v) {
                if ($params->instructionsprefix === $v) {
                    $this->_form->setDefault('instructions', $v);
                }
            }

        }*/

        // Hideous hack to insert a tab bar at the top of the page
        /*if (!empty($this->current->id)) {
            global $PAGE, $OUTPUT;
            $output = $PAGE->get_renderer('mod_amcquiz');
            $output->quiz =  \mod_amcquiz\local\models\quiz::buildFromRecord($this->current);
            $OUTPUT = $output;
        }*/
    }
}
