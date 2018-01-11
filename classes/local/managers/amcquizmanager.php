<?php

namespace mod_amcquiz\local\managers;

class amcquizmanager
{
    const TABLE_AMCQUIZ = 'amcquiz';
    const TABLE_PARAMETERS = 'amcquiz_parameters';
    const TABLE_GROUPS = 'amcquiz_groups';
    const TABLE_QUESTIONS = 'amcquiz_questions';

    const RAND_MINI = 1000;
    const RAND_MAXI = 100000;

    public function get_amcquiz_record(int $id)
    {
        global $DB;
        // get amcquiz from db
        $amcquiz = $DB->get_record(self::TABLE_AMCQUIZ, ['id' => $id]);
        //$parameters = $DB->get_record(self::TABLE_PARAMETERS, ['amcquiz_id' => $id]);
        $amcquiz->parameters = $this->get_amcquiz_parameters_record($id);


          //$groups = $DB->get_records(self::TABLE_GROUPS, ['quiz_id' => $id]);
          // get all questions by groups
          /*foreach ($groups as $group) {
            $questions = $DB->get_records_sql(self::TABLE_QUESTIONS, ['group_id' => $group->id]);
          }*/

        return $amcquiz;

    }

    public function get_amcquiz_parameters_record(int $id)
    {
        global $DB;
        return $DB->get_record(self::TABLE_PARAMETERS, ['amcquiz_id' => $id]);
    }

    public function create_quiz_from_form(\stdClass $data)
    {
        global $DB, $USER;

        $amcquiz = new \stdClass(); // \mod_amcquiz\local\entity\amcquiz();
        $amcquiz->name = $data->name;
        $amcquiz->course_id = $data->course;
        $amcquiz->author_id = $USER->id;
        $amcquiz->timecreated = time();
        $amcquiz->timemodified = time();
        $amcquiz->anonymous = (boolean)$data->anonymous;
        $amcquiz->studentcorrectionaccess = (boolean)$data->studentcorrectionaccess;
        $amcquiz->studentannotatedaccess = (boolean)$data->studentannotatedaccess;
        // save in order to have the id
        $amcquiz->id = $DB->insert_record(self::TABLE_AMCQUIZ, $amcquiz);
        return $amcquiz;
    }

    public function update_quiz_from_form(\stdClass $data)
    {
        global $DB;
        // do not create a quiz object since all object values are not needed for update !
        $updated = new \stdClass();
        $updated->id = $data->instance;
        $updated->name = $data->name;
        $updated->timemodified = time();
        $updated->anonymous = (boolean)$data->anonymous;
        $updated->studentcorrectionaccess = (boolean)$data->studentcorrectionaccess;
        $updated->studentannotatedaccess = (boolean)$data->studentannotatedaccess;
        $DB->update_record(self::TABLE_AMCQUIZ, $updated);
        return $updated;
    }

    public function create_amcquiz_parameters(\stdClass $amcquiz, array $data)
    {
        global $DB;
        $parameters = new \stdClass(); //\mod_amcquiz\local\entity\parameters();
        $parameters->amcquiz_id = $amcquiz->id;
        //echo '<pre>';
        //print_r($data);die;
        // if anonymous data is not persisted how does mod_form handle quiz update
        $parameters->generalinstructions = $data['generalinstructions']['text'];
        $parameters->generalinstructionsformat = $data['generalinstructions']['format'];
        $parameters->studentnumberinstructions = $data['studentnumberinstructions'];
        $parameters->studentnameinstructions = $data['studentnameinstructions'];
        $parameters->grademax = (int)$data['grademax'];
        $parameters->gradegranularity = (float)$data['gradegranularity'];
        $parameters->graderounding = $data['graderounding'];
        $parameters->scoringset = $data['scoringset'];
        $parameters->versions = (int)$data['versions'];
        $parameters->shuffleq = (boolean)$data['shuffleq'];
        $parameters->shufflea = (boolean)$data['shufflea'];
        $parameters->qcolumns = (int)$data['qcolumns'];
        $parameters->acolumns = (int)$data['acolumns'];
        $parameters->separatesheet = (boolean)$data['separatesheet'];
        $parameters->displaypoints = (boolean)$data['displaypoints'];
        $parameters->markmulti = (boolean)$data['markmulti'];
        $parameters->showscoringset = (boolean)$data['showscoringset'];
        $parameters->customlayout = $data['customlayout'] ? $data['customlayout'] : null;
        $parameters->randomseed = rand(self::RAND_MINI, self::RAND_MAXI);
        $parameters->id = $DB->insert_record(self::TABLE_PARAMETERS, $parameters);

        $amcquiz->parameters = $parameters;
        return $amcquiz;
    }

    public function update_amcquiz_parameters(\stdClass $amcquiz, array $data)
    {
        global $DB;
        // we need to retrieve parameters id...
        $paramrecord = $this->get_amcquiz_parameters_record($amcquiz->id);
        $parameters = new \stdClass();

        $parameters->id = $paramrecord->id;
        $parameters->generalinstructions = $data['generalinstructions']['text'];
        $parameters->generalinstructionsformat = $data['generalinstructions']['format'];
        $parameters->studentnumberinstructions = $data['studentnumberinstructions'];
        $parameters->studentnameinstructions = $data['studentnameinstructions'];
        $parameters->grademax = (int)$data['grademax'];
        $parameters->gradegranularity = (float)$data['gradegranularity'];
        $parameters->graderounding = $data['graderounding'];
        $parameters->scoringset = $data['scoringset'];
        $parameters->versions = (int)$data['versions'];
        $parameters->shuffleq = (boolean)$data['shuffleq'];
        $parameters->shufflea = (boolean)$data['shufflea'];
        $parameters->qcolumns = (int)$data['qcolumns'];
        $parameters->acolumns = (int)$data['acolumns'];
        $parameters->separatesheet = (boolean)$data['separatesheet'];
        $parameters->displaypoints = (boolean)$data['displaypoints'];
        $parameters->markmulti = (boolean)$data['markmulti'];
        $parameters->showscoringset = (boolean)$data['showscoringset'];
        $parameters->customlayout = $data['customlayout'] ? $data['customlayout'] : null;

        $DB->update_record(self::TABLE_PARAMETERS, $parameters);
        return $parameters;
    }

    public function send_latex_file(\stdClass $amcquiz, \stdClass $data, \mod_amcquiz_mod_form $form)
    {


        if (isset($data->latexfile) && !empty($data->latexfile)) {
            $filename = $form->get_new_filename('latexfile');
            // @TODO file content should be sent to API https://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms#filepicker
            $content = $form->get_file_content('latexfile');
            /*$uploadsuccess = $form->save_file(
                'latexfile',
                $this->getDirName(true).'/'.$filename,
                true
            );*/
            $amcquiz->latexfile = $filename;
            return true;
        }

        return false;
    }

}
