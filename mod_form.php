<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/course/moodleform_mod.php';

require_once __DIR__.'/locallib.php';

/* @var $PAGE moodle_page */

/**
 * Module instance settings form.
 */
class mod_amcquiz_mod_form extends moodleform_mod
{
    /**
     * Defines forms elements.
     */
    public function definition()
    {
        global $CFG, $PAGE;
        $PAGE->requires->js_call_amd('mod_amcquiz/mod_form', 'init');
        $mform = &$this->_form;

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
            [0, 1]
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
            'parameters[globalinstructions]',
            get_string('modform_general_instructions', 'mod_amcquiz'),
            [
                'rows' => '4',
                'cols' => '64',
            ]
        );
        $mform->setDefault('parameters[globalinstructions]', ['text' => get_config('mod_amcquiz', 'instructions')]);
        $mform->setType('parameters[globalinstructions]', PARAM_RAW);
        $mform->disabledIf('parameters[globalinstructions]', 'uselatexfile', 'eq', 1);

        // Not persisted. Only used to allow javascript to enable / disable some field
        $mform->addElement('advcheckbox', 'anonymous', get_string('modform_anonymous', 'mod_amcquiz'));
        $mform->disabledIf('anonymous', 'uselatexfile', 'eq', 1);

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

        $graderoundingvalues = amcquiz_get_grade_rounding_strategies();
        $mform->addElement(
            'select',
            'parameters[graderounding]',
            get_string('modform_graderounding_strategy', 'mod_amcquiz'),
            $graderoundingvalues
        );

        // select from config (ie call locallib method that will parse the appropriate config field value)
        $scoringrules = amcquiz_parse_scoring_rules();
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
            array('Auto', 1, 2)
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
              get_string('modform_display_scores_end', 'mod_amcquiz'),
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

        $mform->addElement('textarea', 'parameters[customlayout]', get_string('modform_custom_layout', 'mod_amcquiz'), array('rows' => '3', 'cols' => '64'));
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
    public function data_preprocessing(&$default_values)
    {
        $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
        if ($default_values['instance']) {
            $parameters = $amcquizmanager->get_amcquiz_parameters_record((int) $default_values['instance']);

            // Moodle seems to get only primary object ie an object from amcquiz table without other table values
            // So we need to explicitely override default parameters values with real values when updating instance
            $this->_form->setDefault('parameters[globalinstructions]', $parameters->globalinstructions);
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
        }
    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $USER;
        $errors = parent::validation($data, $files);

        /*
          A Note on the code below :
          We need to check if a file (latex quiz definition file) has been associated only in some cases
          - $files is ALWAYS empty.
          - $this->_form->addRule does not apply since conditions can not be set on it.
          - $mform = &$this->_form; $content = $mform->get_file_content('latexfile'); does not work here (all to undefined method MoodleQuickForm::get_file_content())
          - trying to catch the conditions in modamcquiz_add_instance method and returning false if conditions are not met throws a "invalid function" exception
          - checking if (isset($data->latexfile) && !empty($data->latexfile)) is useless since evene if no file has been selected the conditions return true
          ...
          the code below is working... but its not as simple (and reliable?) as it should be...
          see https://github.com/moodle/moodle/blob/master/mod/resource/mod_form.php#L198 && https://moodle.org/mod/forum/discuss.php?d=318199
         */

        if ($data['uselatexfile']) {
            // check if a file has been selected via the filepicker
            $usercontext = context_user::instance($USER->id);
            $fs = get_file_storage();
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['latexfile'], 'sortorder, id', false);
            $fileselected = 1 === count($files);
            // if its an update the file must be there or the file must exists on the API side
            if ((bool) $data['update'] && !$fileselected) {
                // check if a .tex definition file exists for this quiz on the API side
                $curlmanager = new \mod_amcquiz\local\managers\curlmanager();
                $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
                $amcquiz = $amcquizmanager->get_amcquiz_record($data['instance']);
                $result = $curlmanager->get_amcquiz_latex_file($amcquiz);
                if ('' === $result['data']['url']) {
                    $errors['latexfile'] = get_string('required');
                }
            } elseif (!$fileselected) {
                $errors['latexfile'] = get_string('required');
            }
        }

        return $errors;
    }
}
