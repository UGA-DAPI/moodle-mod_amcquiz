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
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $PAGE;
        $PAGE->requires->js_call_amd('mod_amcquiz/mod_form', 'init');
        $mform = $this->_form;

        // General fieldset
        $mform->addElement('header', 'general', get_string('general', 'form'));
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
        $mform->addElement(
            'filepicker',
            'latexfile',
            get_string('modform_latexfile', 'mod_amcquiz'),
            null,
            ['accepted_types' => 'tex']
        );
        $mform->disabledIf('latexfile', 'uselatexfile', 'eq', 0);



        // Instructions fieldset
        $mform->addElement('header', 'instructions', get_string('modform_instructionsheader', 'mod_amcquiz'));

        $mform->addElement(
            'editor',
            'parameters[generalinstructions]',
            get_string('modform_general_instructions', 'mod_amcquiz'),
            [
                'rows' => '4',
                'cols' => '64'
            ]
        );
        $mform->setDefault('parameters[generalinstructions]', ['text' => get_config('mod_amcquiz', 'instructions')]);
        $mform->setType('parameters[generalinstructions]', PARAM_RAW);
        $mform->disabledIf('parameters[generalinstructions]', 'uselatexfile', 'eq', 1);

        // Not persisted. Only used to allow javascript to enable / disable some field
        $mform->addElement('advcheckbox', 'anonymous', get_string('modform_anonymous', 'mod_amcquiz'));

        $mform->addElement(
            'text',
            'parameters[studentnumberinstructions]',
            get_string('modform_studentnumber_instructions', 'mod_amcquiz'),
            array('size' => 64)
        );
        $mform->setType('parameters[studentnumberinstructions]', PARAM_TEXT);
        $mform->setDefault('parameters[studentnumberinstructions]', get_config('mod_amcquiz', 'instructionslstudent'));
        $mform->disabledIf('parameters[studentnumberinstructions]', 'uselatexfile', 'eq', 1);



        $mform->addElement(
            'text',
            'parameters[studentnameinstructions]',
            get_string('modform_studentname_instructions', 'amcquiz'),
            array(
                'data-std' => get_config('mod_amcquiz', 'instructionslnamestd'),
                'data-anon' => get_config('mod_amcquiz', 'instructionslnameanon'),
            )
        );
        $mform->setType('parameters[studentnameinstructions]', PARAM_TEXT);
        // should depend on quiz = anonymous or not
        $mform->setDefault('parameters[studentnameinstructions]', get_config('mod_amcquiz', 'instructionslnamestd'));
        $mform->disabledIf('parameters[studentnameinstructions]', 'uselatexfile', 'eq', 1);

        // Scoring fieldset
        /*$mform->addElement('header', 'scoring', get_string('modform_scoring_parameters_header', 'mod_amcquiz'));

        $mform->addElement('text', 'parameters[grademax]', get_string('modform_grademax', 'mod_amcquiz'));
        $mform->setDefault('parameters[grademax]', 20);
        $mform->addElement('text', 'parameters[gradegranularity]', get_string('modform_gradegranularity', 'mod_amcquiz'));
        $mform->setDefault('parameters[gradegranularity]', 0.25);

        $graderoundingvalues = get_grade_rounding_strategies();
        $mform->addElement(
            'select',
            'parameters[graderounding]',
            get_string('modform_graderounding_strategy', 'mod_amcquiz'),
            $graderoundingvalues
        );



        // select from config (ie call locallib method that will parse the appropriate config field value)
        $scoringrules = parse_scoring_rules();
        $mform->addElement(
            'select',
            'parameters[scoringset]',
            get_string('modform_scoring_strategy', 'mod_amcquiz'),
            $scoringrules
        );*/

        // Grading override (dans quelle table ces infos vont s'enregistrer ? comment on les récupère etc.)
        // do not show when updating... normal behavior ?
        $this->standard_grading_coursemodule_elements(); // gradecat + gradepass added //  grade[modgrade_point] replaced by parameters[grademax]
        // remove the block since we only need one field of the block (the 2 others wont apply to our case type de notation (points / bareme) et bareme) but did not find how to remove fields inside the block...
        $mform->removeElement('grade');
        // grade[modgrade_point] -> maxgrade c le seul élément que je veux dans le groupe 'grade' mais ne semble pas possible de ne garder que lui dans le groupe...
        $mform->addElement('text', 'parameters[grademax]', get_string('modform_grademax', 'mod_amcquiz'));
        $mform->setDefault('parameters[grademax]', 20);
        $mform->addElement('text', 'parameters[gradegranularity]', get_string('modform_gradegranularity', 'mod_amcquiz'));
        $mform->setDefault('parameters[gradegranularity]', 0.25);

        $graderoundingvalues = get_grade_rounding_strategies();
        $mform->addElement(
            'select',
            'parameters[graderounding]',
            get_string('modform_graderounding_strategy', 'mod_amcquiz'),
            $graderoundingvalues
        );

        // select from config (ie call locallib method that will parse the appropriate config field value)
        $scoringrules = parse_scoring_rules();
        $mform->addElement(
            'select',
            'parameters[scoringset]',
            get_string('modform_scoring_strategy', 'mod_amcquiz'),
            $scoringrules
        );

        // Amc params fieldset
        $mform->addElement('header', 'parameters', get_string('modform_amc_parameters_header', 'mod_amcquiz'));

        $mform->addElement('text', 'parameters[versions]', get_string('modform_sheets_versions', 'mod_amcquiz'));
        $mform->setType('parameters[versions]', PARAM_INTEGER);
        $mform->setDefault('parameters[versions]', 1);
        $mform->disabledIf('parameters[versions]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'parameters[qcolumns]',
            get_string('modform_questions_columns', 'mod_amcquiz'),
            array("Auto", 1, 2)
        );
        $mform->disabledIf('parameters[qcolumns]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'parameters[shuffleq]', get_string('modform_shuffle_questions', 'mod_amcquiz'));
        $mform->setType('parameters[shuffleq]', PARAM_BOOL);
        $mform->disabledIf('parameters[shuffleq]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'parameters[shufflea]', get_string('modform_shuffle_answers', 'mod_amcquiz'));
        $mform->setType('parameters[shufflea]', PARAM_BOOL);
        $mform->disabledIf('parameters[shufflea]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'parameters[separatesheet]', get_string('modform_separate_sheets', 'mod_amcquiz'));
        $mform->setType('parameters[separatesheet]', PARAM_BOOL);
        $mform->disabledIf('parameters[separatesheet]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'parameters[acolumns]',
            get_string('modform_sheets_columns', 'mod_amcquiz'),
            array('Auto', 1, 2, 3, 4)
        );
        $mform->disabledIf('parameters[acolumns]', 'parameters[separatesheet]', 'eq', 0);
        $mform->disabledIf('parameters[acolumns]', 'uselatexfile', 'eq', 1);

        $mform->addElement(
            'select',
            'parameters[displaypoints]',
            get_string('modform_display_scores', 'mod_amcquiz'),
            [
              get_string('modform_display_scores_no', 'mod_amcquiz'),
              get_string('modform_display_scores_beginning', 'mod_amcquiz'),
              get_string('modform_display_scores_end', 'mod_amcquiz')
            ]
        );
        $mform->setType('parameters[displaypoints]', PARAM_INTEGER);
        $mform->disabledIf('parameters[displaypoints]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'parameters[markmulti]', get_string('modform_mark_multi', 'mod_amcquiz'));
        $mform->setType('parameters[markmulti]', PARAM_BOOL);
        $mform->addHelpButton('parameters[markmulti]', 'modform_mark_multi', 'mod_amcquiz');
        $mform->disabledIf('parameters[markmulti]', 'uselatexfile', 'eq', 1);

        $mform->addElement('advcheckbox', 'parameters[showscoringset]', get_string('modform_display_score_rules', 'mod_amcquiz'));
        $mform->setType('parameters[showscoringset]', PARAM_BOOL);
        $mform->addHelpButton('parameters[showscoringset]', 'modform_display_score_rules', 'mod_amcquiz');
        $mform->disabledIf('parameters[showscoringset]', 'uselatexfile', 'eq', 1);

        $mform->addElement('textarea', 'parameters[customlayout]', get_string('modform_custom_layout', 'mod_amcquiz'), array('rows'=>'3', 'cols'=>'64'));
        $mform->setType('parameters[customlayout]', PARAM_TEXT);
        $mform->addHelpButton('parameters[customlayout]', 'modform_custom_layout', 'amcquiz');
        $mform->disabledIf('parameters[customlayout]', 'uselatexfile', 'eq', 1);

        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();

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
        $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
        $parameters = $amcquizmanager->get_amcquiz_parameters_record((int)$default_values['instance']);

        // Moodle seems to get only primary object ie an object from amcquiz table without other table values
        // So we need to explicitely override default parameters values with real values when updating instance
        $this->_form->setDefault('parameters[generalinstructions]', $parameters->generalinstructions);
        $this->_form->setDefault('parameters[studentnumberinstructions]', $parameters->studentnumberinstructions);
        $this->_form->setDefault('parameters[studentnameinstructions]', $parameters->studentnameinstructions);
        $this->_form->setDefault('parameters[grademax]', $parameters->grademax);
        $this->_form->setDefault('parameters[gradegranularity]', $parameters->gradegranularity);
        $this->_form->setDefault('parameters[graderounding]', $parameters->graderounding);
        $this->_form->setDefault('parameters[scoringset]', $parameters->scoringset);
        $this->_form->setDefault('parameters[versions]', $parameters->versions);
        $this->_form->setDefault('parameters[qcolumns]', $parameters->qcolumns);
        $this->_form->setDefault('parameters[shuffleq]', $parameters->shuffleq);
        $this->_form->setDefault('parameters[shufflea]', $parameters->shufflea);
        $this->_form->setDefault('parameters[separatesheet]', $parameters->separatesheet);
        $this->_form->setDefault('parameters[acolumns]', $parameters->acolumns);
        $this->_form->setDefault('parameters[displaypoints]', $parameters->displaypoints);
        $this->_form->setDefault('parameters[markmulti]', $parameters->markmulti);
        $this->_form->setDefault('parameters[showscoringset]', $parameters->showscoringset);
        $this->_form->setDefault('parameters[customlayout]', $parameters->customlayout);


        //  print_r($parameters);die;
        // convert parameters to array
        //$default_values['parameters'] = json_decode(json_encode($parameters), true);

      //  echo '<pre>';
      //  print_r($default_values);
      //  die;

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
