<?php

namespace mod_amcquiz\local\managers;

class questionmanager
{

    const TABLE_QUESTIONS = 'amcquiz_question';

    public function __construct(){

    }

    public function get_group_questions(int $groupid) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $amcquestions = $DB->get_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $groupid], 'position');
        $result = array_map(function ($amcquestion) use ($DB) {
            $item = new \stdClass();
            $moodle_question = $DB->get_record('question', ['id' => $amcquestion->id]);
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


    public function add_group_questions($groupid, $questionsids) {
        global $DB;
        $row = new \stdClass();
        $row->amcgroup_id = $groupid;
        // get max position from question table where amcgroup_id = $group_id
        $next = $this->get_question_next_position($groupid);

        foreach ($questionsids as $id) {
            $row->question_id = $id;
            $row->score = 1;
            $row->position = $next;
            $DB->insert_record(self::TABLE_QUESTIONS, $row);
            $next++;
        }
    }

    public function get_question_next_position($groupid) {
        $sql = 'SELECT MAX(position) as next FROM {'.self::TABLE_QUESTIONS.'} q ';
        $sql .= 'WHERE q.amcgroup_id=' .$groupid;
        $record_with_max_position = $DB->get_record_sql($sql);
        return $record_with_max_position && $record_with_max_position->next ? (int)$record_with_max_position->next + 1 : 1;
    }


}
