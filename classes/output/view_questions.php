<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_questions implements \renderable, \templatable {

    /**
     * The auto multiple choice questionnaire.
     *
     * @var stdClass
     */
    protected $amcquiz;

    /**
     *
     * @var array a set of usefull data
     */
    protected $data;

    /**
     * Construct
     *
     * @param stdClass $amcquiz A questionnaire
     * @param array $data A set of usefull data
     */
    public function __construct(\stdClass $amcquiz, array $data) {
        $this->amcquiz = $amcquiz;
        $this->data = $data;
    }
    /**
     * Prepare data for use in a template
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        global $CFG;
        $questions = [];
        $questionindex = 1;
        /*foreach ($this->quiz->questions as $questionitem) {
            $item = new \stdClass();
            $item->id = $questionitem->id;
            $item->name = $questionitem->name;
            $item->section = $questionitem->getType() === 'section';
            $questionscore = $questionitem->score;
            if ($questionscore <= 0) {
                $questionscore = empty($questionitem->defaultmark) ? '' : sprintf('%.2f', $questionitem->defaultmark);
            }
            $item->score = $questionscore;
            if ($questionitem->getType() === 'section') {
                $item->index = -1;
            } else {
                $item->index = $questionindex;
                $questionindex++;
            }
            $questions[] = $item;
        }*/




        $courseid = $this->data['courseid'];
        $cmid = $this->data['cmid'];
        $pageurl = $this->data['pageurl'];
        $content = [
          'amcquiz' => $this->amcquiz,
          'courseid' => $courseid,
          'cmid' => $cmid,
          'wwwroot' => $CFG->wwwroot,
          'questionbankurl' => new \moodle_url('/question/edit.php', array('courseid' => $courseid)),
          'questionediturl' => new \moodle_url('/question/question.php', array('cmid' => $cmid)),
          'pageurl' => $pageurl/*
          'questions' => $questions,
          'importfilequestionsurl' => new \moodle_url('/question/import.php', array('courseid' => $courseid)),
          'importquestionsurl' => new \moodle_url('/local/questionssimplified/edit_wysiwyg.php', array('courseid' => $courseid)),
          'createquestionsurl' =>  new \moodle_url('/local/questionssimplified/edit_standard.php', array('courseid' => $courseid)),
          'questionbankurl' => new \moodle_url('/question/edit.php', array('courseid' => $courseid))*/
        ];
        return $content;
    }
}
