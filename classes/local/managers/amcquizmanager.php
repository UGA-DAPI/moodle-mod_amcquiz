<?php

namespace mod_amcquiz\local\managers;

class amcquizmanager
{
    const TABLE_AMCQUIZ = 'amcquiz';
    const TABLE_PARAMETERS = 'amcquiz_parameters';

    const RAND_MINI = 1000;
    const RAND_MAXI = 100000;

    private $groupmanager;
    private $questionmanager;

    public function __construct() {
        $this->groupmanager = new \mod_amcquiz\local\managers\groupmanager();
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
    }

    public function get_amcquiz_record(int $id, $cmid)
    {
        global $DB;
        // get amcquiz from db
        $amcquiz = $DB->get_record(self::TABLE_AMCQUIZ, ['id' => $id]);
        $amcquiz->parameters = $this->get_amcquiz_parameters_record($id);
        $amcquiz->groups = $this->groupmanager->get_quiz_groups($id);

        $nbquestions = 0;
        $scoresum = 0;
        // get all questions by groups
        foreach ($amcquiz->groups as $group) {
            if ($group->description_question_id) {
                // get question content and set it to group
                $questionInstance = \question_bank::load_question($group->description_question_id);
                $context = \context_module::instance($cmid);
                // will call mod/amcquiz/lib.php->amcquiz_question_preview_pluginfile
                $content = \question_rewrite_question_preview_urls(
                      $questionInstance->questiontext,
                      $questionInstance->id,
                      $questionInstance->contextid,
                      'question',
                      'questiontext',
                      $questionInstance->id,
                      $context->id,
                      'amcquiz'
                  );

                $group->description = format_text($content);
            }
            // get questions
            $group->questions = $this->questionmanager->get_group_questions($group->id);
            $nbquestions += count($group->questions);
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
        $amcquiz->groups[] = $this->groupmanager->add_group($amcquiz->id);
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

}
