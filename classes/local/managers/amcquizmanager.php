<?php

namespace mod_amcquiz\local\managers;

class amcquizmanager
{
    const TABLE_AMCQUIZ = 'amcquiz';
    const TABLE_PARAMETERS = 'amcquiz_parameters';
    const TABLE_GROUPS = 'amcquiz_group';
    const TABLE_QUESTIONS = 'amcquiz_question';

    const RAND_MINI = 1000;
    const RAND_MAXI = 100000;

    public function get_amcquiz_record(int $id)
    {
        global $DB;
        // get amcquiz from db
        $amcquiz = $DB->get_record(self::TABLE_AMCQUIZ, ['id' => $id]);
        $amcquiz->parameters = $this->get_amcquiz_parameters_record($id);
        $amcquiz->groups = $this->get_quiz_groups($id);

        $nbquestions = 0;
        $scoresum = 0;
        // get all questions by groups
        foreach ($amcquiz->groups as $group) {
            if ($group->description_question_id) {
                // get question content and set it to group
                $description_question = $DB->get_record('question', ['id' => $group->description_question_id]);
                $group->description = $description_question->questiontext;
            }
            // get questions
            $group->questions = $this->get_group_questions($group->id);
            $nbquestions += count($questions);
            foreach ($group->questions as $question) {
                $scoresum += $question->score;
            }
        }
        // add usefull data to quiz
        $amcquiz->nbquestions = $nbquestions;
        $amcquiz->scoresum = $scoresum;

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

        // create default group
        $amcquiz->groups[] = $this->create_group($amcquiz->id);
        return $amcquiz;
    }

    public function update_quiz_from_form(\stdClass $data)
    {
        global $DB;
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
        $parameters = new \stdClass();
        $parameters->amcquiz_id = $amcquiz->id;
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

    // NEED API
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



    // need API should read grades from amc csv
    protected function read_amc_csv(\stdClass $amcquiz) {
        return [];
        /*$input = $this->fopenRead($this->workdir . self::PATH_AMC_CSV);
        if (!$input) {
            return false;
        }
        $header = fgetcsv($input, 0, self::CSV_SEPARATOR);
        if (!$header) {
            return false;
        }
        $getCol = array_flip($header);
        $grades = array();

        while (($data = fgetcsv($input, 0, self::CSV_SEPARATOR)) !== false) {
            $idnumber = $data[$getCol['student.number']];
            $userid = null;
            $userid = $data[$getCol['moodleid']];
            if ($userid) {
                $this->usersknown++;
            } else {
                $this->usersunknown++;
            }
            $grades[] = (object) array(
                'userid' => $userid,
                'rawgrade' => str_replace(',', '.', $data[6])
            );
        }
        fclose($input);
        return $grades;*/
    }


    public function get_grades(array $amcgradesdata = []) {
        $grades = [];
        foreach ($amcgradesdata as $grade) {
            if ($grade->userid) {
                $grades[$grade->userid] = (object) array(
                    'id' => $grade->userid,
                    'userid' => $grade->userid,
                    'rawgrade' => $grade->rawgrade,
                );
            }
        }
        return $grades;
    }

    public function create_group(int $amcquiz_id, string $name = '', string $description_question_id = null, int $position = 1) {
        global $DB;
        $group = new \stdClass();
        $group->amcquiz_id = $amcquiz_id;
        $group->name = $name;
        $group->shuffle = $shuffle;
        $group->description_question_id = $description_question_id;
        $group->position = $position;
        $group->id = $DB->insert_record(self::TABLE_GROUPS, $group);

        return $group;
    }

    public function get_quiz_groups(int $amcquiz_id) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $groups = $DB->get_records(self::TABLE_GROUPS, ['amcquiz_id' => $amcquiz_id], 'position');
        // Need to rebuild array for template iteration to work (https://docs.moodle.org/dev/Templates#Iterating_over_php_arrays_in_a_mustache_template)
        return array_values($groups);
    }

    public function get_group_questions(int $group_id) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $amcquestions = $DB->get_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $group_id], 'position');
        $result = array_map(function ($amcquestion) use ($DB) {
            $item = new \stdClass();
            //echo '<pre>';
            $moodle_question = $DB->get_record('question', ['id' => $amcquestion->id]);
            //print_r($moodle_question);
            $qtype = \question_bank::get_qtype($moodle_question->qtype, false);
            $namestr = $qtype->local_name();
            $moodle_question->icon_plugin_name = $qtype->plugin_name();
            $moodle_question->icon_title = $qtype->local_name();
            $moodle_question->score = $amcquestion->score;
            $moodle_question->amcgroup_id = $amcquestion->amcgroup_id;
            $moodle_question->position = $amcquestion->position;
            return $moodle_question;
        }, $amcquestions);


        return array_values($result);
    }

}
