<?php

namespace mod_amcquiz\local\managers;

class amcquizmanager
{
    const TABLE_AMCQUIZ = 'amcquiz';
    const TABLE_PARAMETERS = 'amcquiz_parameter';

    const RAND_MINI = 1000;
    const RAND_MAXI = 100000;

    private $groupmanager;
    private $questionmanager;

    public function __construct() {
        $this->groupmanager = new \mod_amcquiz\local\managers\groupmanager();
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
    }

    /**
     * Get an amcquiz with all relevant data
     * @param  int    $id   amcquiz id
     * @param  int    $cmid course module id (needed for getting proper context)
     * @return \stdClass an amcquiz
     */
    public function get_amcquiz_record($id, $cmid)
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
                $group->description = format_text($content, $questionInstance->questiontextformat);
            }
            // get questions
            $group->questions = $this->questionmanager->get_group_questions($group->id, $cmid);
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

    /**
     * Get amcquiz paramters
     * @param  int    $id amcquiz id
     * @return \stdClass  amcquiz parameters
     */
    public function get_amcquiz_parameters_record(int $id)
    {
        global $DB;
        return $DB->get_record(self::TABLE_PARAMETERS, ['amcquiz_id' => $id]);
    }

    /**
     * Create a quiz based on form data
     * @param  \stdClass $data form data
     * @return \stdClass the new amc quiz
     */
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

    /**
     * Update a quiz based on form data
     * @param  \stdClass $data form data
     * @return \stdClass the new amc quiz
     */
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

    /**
     * Create parameters for a new quiz
     * @param  \stdClass $amcquiz the quiz
     * @param  array $data form parameters data
     * @return \stdClass the new amc quiz
     */
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

    /**
     * Update parameters for a new quiz
     * @param  \stdClass $amcquiz the quiz
     * @param  array $data form parameters data
     * @return \stdClass the updated parameters
     */
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


    public function amcquiz_export(int $id, int $cmid) {
        // get quiz and transform all its data (ie group description question content, question content and question anwer content)
        global $DB, $CFG;


        srand(microtime() * 1000000);
        $unique = str_replace('.', '', microtime(true) . '_' . rand(0, 100000));
        // quiz temp folder
        $amcquizfolder = $CFG->dataroot . "/temp/amcquiz/" . $unique . '/';
        if (!check_dir_exists($amcquizfolder, true, true)) {
            print_error("Could not create data directory");
        } else {
            // get amcquiz from db
            $amcquiz = $DB->get_record(self::TABLE_AMCQUIZ, ['id' => $id]);
            $amcquiz->parameters = $this->get_amcquiz_parameters_record($id);
            $latexfilename = $amcquizfolder . 'prepare-source.tex';
            $latexcontent = file_get_contents($latexfilename);
            $latexcontent .= '\documentclass[a4paper]{article}';
            $latexcontent .=  PHP_EOL;
            $latexcontent .= '\usepackage[utf8]{inputenc}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\usepackage[T1]{fontenc}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\usepackage{amsmath,amssymb}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\usepackage{multicol}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\usepackage{environ}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\usepackage{graphicx}';
            $latexcontent .= PHP_EOL;
            // options
            $latexcontent .= '\usepackage[box';
            if ($amcquiz->parameters->shuffleq) {
                $latexcontent .= ',noshuffle';
            }
            if ($amcquiz->parameters->separatesheet) {
                $latexcontent .= ',separateanswersheet';
            }

            $latexcontent .= ']{automultiplechoice}';
            $latexcontent .= PHP_EOL;

            $latexcontent .= '\date{}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\author{}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\title{'.$amcquiz->name.'}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\makeatletter';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\let\mytitle\@title';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\let\myauthor\@author';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\let\mydate\@date';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\makeatother';
            $latexcontent .= PHP_EOL;
            if ($amcquiz->parameters->customlayout) {
                $latexcontent .= $amcquiz->parameters->customlayout;
                $latexcontent .= PHP_EOL;
            }

            if ($amcquiz->parameters->acolumns > 2) {
                $latexcontent .= '\def\AMCformQuestion#1{';
                $latexcontent .= PHP_EOL;
                $latexcontent .= "\t" . '\vspace{\AMCformVSpace}';
                $latexcontent .= PHP_EOL;
                $latexcontent .= "\t" . '\par{\bf Q.#1 :}';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '}';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '\def\AMCformAnswer#1{';
                $latexcontent .= PHP_EOL;
                $latexcontent .= "\t" . '\hspace{\AMCformHSpace}#1';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '}';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '\makeatletter';
                $latexcontent .= PHP_EOL;
            }

            if ($amcquiz->parameters->markmulti) {
                $latexcontent .= '\def\multiSymbole{}';
                $latexcontent .= PHP_EOL;
            }

            $latexcontent .= '\AMCrandomseed{' . $amcquiz->parameters->randomseed . '}';
            $latexcontent .= PHP_EOL;

            $latexcontent .= '\scoringDefaultS{}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\scoringDefaultM{}';
            $latexcontent .= PHP_EOL;

            $latexcontent .= '\newenvironment{instructions}{}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '{';
            $latexcontent .= PHP_EOL;
            $latexcontent .= "\t" . '\vspace{1ex}\hrule';
            $latexcontent .= PHP_EOL;
            $latexcontent .= "\t" . '\vspace{2ex}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '}';
            $latexcontent .= PHP_EOL;

            $latexcontent .= '\newcommand{\answersheet}{';
            $latexcontent .= PHP_EOL;
            $latexcontent .= "\t" . '\begin{center}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= "\t\t" . '\Large\bf\mytitle{} --- ' . get_string('document_answer_sheet_title', 'mod_amcquiz');
            $latexcontent .= PHP_EOL;
            $latexcontent .= "\t" . '\end{center}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '}';
            $latexcontent .= PHP_EOL;


            $latexcontent .= '\begin{document}';
            $latexcontent .= PHP_EOL;

            $translator = new \mod_amcquiz\translator();

            $groups = $this->groupmanager->get_quiz_groups($id);
            // remove group that do not have questions
            $groups_filtered = array_filter($groups, function ($group) {
                return $this->questionmanager->count_group_questions($group->id) > 0;
            });

            // transform group data
            $groups_mapped = array_map(function ($group) use ($cmid, $translator) {
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
                    $content = format_text($content, $questionInstance->questiontextformat);

                    //$content = format_text($content, $questionInstance->questiontextformat, ['filter' => false]);
                    $group->description = $translator->html_to_tex($content);

                }
                return $group;
            }, $groups_filtered);

            $morethanonegroup = count($groups_mapped) > 1;
            $nbquestioninquiz = $this->count_quiz_questions($amcquiz);

            // if more than one group
            if ($morethanonegroup) {
                $latexcontent .= '%%% preparation of the groups';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '\setdefaultgroupmode{withoutreplacement}';
                $latexcontent .= PHP_EOL;
            }

            // all scoring rules available in config
            $scoringrulesrawdata = get_config('mod_amcquiz', 'scoringrules');
            $splittedrules = preg_split('/\n-{3,}\s*\n/s', $scoringrulesrawdata, -1, PREG_SPLIT_NO_EMPTY);
            $choosenone = $splittedrules[$amcquiz->parameters->scoringset];

            $ruleslines = array_filter(explode("\n", $choosenone));
            $scoringrule = new \stdClass();
            // take the first element of the array (name of scoringrule)
            $scoringrule->name = array_shift($ruleslines);
            $scoringrule->rules = [];
            // remove all descriptions texts for the scoringrule
            while ($ruleslines && !preg_match('/^\s*[SM]\s*;/i', $ruleslines[0])) {
                array_shift($ruleslines);
            }
            // remove empty values
            $nonemptyrules = array_filter($ruleslines, function ($line) {
                return trim($line) !== '';
            });

            // build scoring rules
            foreach ($nonemptyrules as $rawrule) {
                $rule = new \stdClass();
                $rawrulesplitted = explode(';', $rawrule);
                $rule->multiple = strtoupper(trim($rawrulesplitted[0])) === 'M';
                $rule->score = (double) $rawrulesplitted[1];
                $rule->expression = trim($rawrulesplitted[2]);
                $scoringrule->rules[] = $rule;
            }

            foreach ($groups_mapped as $group) {

                $groupquestions = $this->questionmanager->export_group_questions($group->id, $cmid);

                foreach ($groupquestions as $question) {
                    if ($question->score === round($question->score)) {
                        $points = $question->score;
                    } elseif (abs(round(10*$question->score) - 10*$question->score) < 1) {
                        $points =  sprintf('%.1f', $question->score);
                    } else {
                        $points = '(' . sprintf('%.2f', $question->score) . ' pt' . $question->score > 1 ? 's)' : ')';
                    }
                    $questionrule = null;
                    foreach ($scoringrule->rules as $rule) {
                        //si toutes ces conditions sont réunies alors on peut appliquer la règle...
                        $rulematch = true;

                        if ($question->qtype->plugin_name() === 'truefalse' && $rule->multiple) {
                            $rulematch = false;
                        }
                        if ($rule->score && $question->score !== $rule->score) {
                            $rulematch = false;
                        }
                        if ($rulematch) {
                            if ($rule->score) {
                                $questionrule = str_replace('SCORE', $rule->score, $rule->expression);
                            } else {
                                $questionrule = str_replace('SCORE', $question->score, $rule->expression);
                            }
                            break;
                        }
                    }

                    $latexcontent .= $morethanonegroup ? '\element{'.$group->name.'}{' : '\element{default}{';
                    $latexcontent .= PHP_EOL;
                    $questionname = preg_replace('/[^a-zA-Z]+/', '', @iconv('UTF-8', 'ASCII//TRANSLIT', substr( html_entity_decode(strip_tags($question->name)), 0, 30 )));
                    $latexcontent .= "\t" . '\begin{question}{'.$questionname.'}';
                    $latexcontent .= PHP_EOL;
                    $latexcontent .= "\t" . '\scoring{' . $scoring . '}';
                    $latexcontent .= PHP_EOL;
                    // 0 -> no | 1 -> au début | 2 à la fin
                    if ($amcquiz->parameters->displaypoints === 1) {
                        $latexcontent .= "\t" . $points;
                        $latexcontent .= PHP_EOL;
                    }
                    $latexcontent .= "\t" . $question->questiontext;
                    $latexcontent .= PHP_EOL;
                    // 0 -> no | 1 -> au début | 2 à la fin
                    if ($amcquiz->parameters->displaypoints === 2) {
                        $latexcontent .= "\t" . $points;
                        $latexcontent .= PHP_EOL;
                    }
                    $latexcontent .= "\t\t" .'\begin{choices}';
                    $latexcontent .= PHP_EOL;

                    foreach ($question->answers as $answer) {
                        $latexcontent .= $answer->valid ? "\t\t\t" . '\correctchoice' : "\t\t\t" .'\wrongchoice';
                        $latexcontent .= '{'. $answer->answertext .'}'; // NO EOL !!
                        $latexcontent .= PHP_EOL;
                    }

                    $latexcontent .= "\t\t" .'\end{choices}';
                    $latexcontent .= PHP_EOL;
                    $latexcontent .= "\t" .'\end{question}';
                    $latexcontent .= PHP_EOL;
                    $latexcontent .= '}';
                    $latexcontent .= PHP_EOL;
                }
            }

            $latexcontent .= '\begin{examcopy}['.$amcquiz->parameters->versions.']';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\begin{center}\Large\bf\mytitle\end{center}';
            $latexcontent .= PHP_EOL;

            if (!$amcquiz->parameters->separatesheet) {
                $latexcontent .= $this->get_student_block($amcquiz);
            }

            $latexcontent .= '\begin{instructions}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= $translator->html_to_tex($amcquiz->parameters->globalinstructions);
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\end{instructions}';
            $latexcontent .= PHP_EOL;

            $nbcolumns = $amcquiz->parameters->qcolumns;
            if ($nbcolumns === 0 && $nbquestioninquiz > 5) {
                $nbcolumns = 2;
            }

            // group data to print
            foreach ($groups_mapped as $group) {

                $latexcontent .= $morethanonegroup ? '\insertgroup{'.$group->name.'}' : '\insertgroup{default}';
                $latexcontent .= PHP_EOL;
                if ($nbcolumns > 1) {
                    $latexcontent .= '\begin{multicols}{'.$nbcolumns.'}';
                    $latexcontent .= PHP_EOL;
                }
                if ($amcquiz->parameters->shuffleq) {
                    $latexcontent .= $morethanonegroup ? '\shufflegroup{'.$group->name.'}' : '\shufflegroup{default}';
                    $latexcontent .= PHP_EOL;
                }
                if ($group->description_question_id) {
                      $latexcontent .= '\begin{center}';
                      $latexcontent .= PHP_EOL;
                      $latexcontent .= "\t" . '\hrule\vspace{2mm}';
                      $latexcontent .= PHP_EOL;
                      // use DOM
                      $latexcontent .= "\t" . '\bf\Large ' . $group->description;
                      $latexcontent .= PHP_EOL;
                      $latexcontent .= "\t" . '\vspace{2mm}\hrule';
                      $latexcontent .= PHP_EOL;
                      $latexcontent .= '\end{center}';
                      $latexcontent .= PHP_EOL;
                }

                if ($nbcolumns > 1) {
                    $latexcontent .= '\end{multicols}';
                    $latexcontent .= PHP_EOL;
                }
            }

            if ($amcquiz->parameters->separatesheet) {
                $nbanswercolumns = 0;
                if (empty($amcquiz->parameters->acolumns)) {
                    $nbanswercolumns = $nbquestioninquiz > 22 ? 2 : 0;
                } elseif ($amcquiz->parameters->acolumns === 1) {
                    $nbanswercolumns = 0;
                } else {
                    $nbanswercolumns = $amcquiz->parameters->acolumns;
                }
                $latexcontent .= '\AMCcleardoublepage';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '\AMCformBegin';
                $latexcontent .= PHP_EOL;
                $latexcontent .= '\answersheet';
                $latexcontent .= PHP_EOL;
                $latexcontent .= $this->get_student_block($amcquiz);
                if ($nbanswercolumns > 1) {
                    $latexcontent .= '\begin{multicols}{'.$nbanswercolumns.'}';
                    $latexcontent .= PHP_EOL;
                    $latexcontent .= '\raggedcolumns';
                    $latexcontent .= PHP_EOL;
                }
                $latexcontent .= '\AMCform';
                $latexcontent .= PHP_EOL;
                if ($nbanswercolumns > 1) {
                    $latexcontent .= '\end{multicols}{'.$nbanswercolumns.'}';
                    $latexcontent .= PHP_EOL;
                }
                $latexcontent .= '\clearpage';
                $latexcontent .= PHP_EOL;
            }

            $latexcontent .= '\end{examcopy}';
            $latexcontent .= PHP_EOL;
            $latexcontent .= '\end{document}';

          //  print_r($amcquiz);

            // get files and save them in a media directory
            // create latex file based on quiz
            // zip everything
            // sending the file will be handled by another method...
            //die('titi');
            file_put_contents($latexfilename, $latexcontent);


        }
    }

    public function count_quiz_questions(\stdClass $amcquiz) {
        $count = 0;
        foreach ($amcquiz->groups as $group) {
            $count += $this->questionmanager->count_group_questions($group->id);
        }
        return $count;
    }

    public function get_student_block(\stdClass $amcquiz) {
        $studentblock = '';
        $codelength = get_config('mod_amcquiz', 'amccodelength');
        $studentblock .= '\setlength{\parindent}{0pt}';
        $studentblock .= PHP_EOL;
        $studentblock .= '\begin{multicols}{2}';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t".'\raggedcolumns';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t".'\AMCcode{student.number}{' . $codelength . '}';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t".'\columnbreak';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t".'$\longleftarrow{}$\hspace{0pt plus 1cm}';
        $studentblock .= $amcquiz->parameters->studentnumberinstructions;
        $studentblock .= '\\\\[3ex]';
        $studentblock .= PHP_EOL;
        $studentblock .= '\hfill{}';
        $studentblock .= PHP_EOL;


        $studentblock .= '\namefield{';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t" . '\fbox{';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t\t" . '\begin{minipage}{.9\linewidth}';
        $studentblock .= PHP_EOL;
        if ($amcquiz->parameters->studentnameinstructions) {
            $studentblock .= $amcquiz->parameters->studentnameinstructions;
            $studentblock .= '\\\\[3ex]';
            $studentblock .= PHP_EOL;
        }
        $studentblock .=  "\t\t".'\null\dotfill\\\\[2.5ex]';
        $studentblock .= PHP_EOL;
        $studentblock .=  "\t\t".'\null\dotfill\vspace*{3mm}';
        $studentblock .= PHP_EOL;
        $studentblock .= "\t" . '\end{minipage}';
        $studentblock .= PHP_EOL;
        $studentblock .= '}';
        $studentblock .= PHP_EOL;
        $studentblock .= '}';
        $studentblock .= PHP_EOL;




        $studentblock .= '\hfill\\\\';
        $studentblock .= PHP_EOL;
        $studentblock .= '\end{multicols}';
        $studentblock .= PHP_EOL;


        return $studentblock;
    }


}
