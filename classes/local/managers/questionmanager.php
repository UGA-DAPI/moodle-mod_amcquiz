<?php

namespace mod_amcquiz\local\managers;

class questionmanager
{

    const TABLE_QUESTIONS = 'amcquiz_group_question';

    public function __construct() {}

    public function get_group_questions(int $groupid) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $amcquestions = $DB->get_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $groupid], 'position');
        $result = array_map(function ($amcquestion) use ($DB) {
            $item = new \stdClass();
            $moodle_question = \question_bank::load_question($amcquestion->question_id);
            $mappedanswers = array_map(function ($answer) {
                $item = new \stdClass();
                $item->answertext = $answer->answer;
                $item->valid = $answer->fraction > 0;
                return $item;
            }, $moodle_question->answers);
            $moodle_question->answers = array_values($mappedanswers);
            $moodle_question->icon_plugin_name = $moodle_question->qtype->plugin_name();
            $moodle_question->icon_title = $moodle_question->qtype->local_name();
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

    public function delete_group_questions($groupid) {
        global $DB;
        $DB->delete_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $groupid]);
    }

    public function delete_group_question($questionid) {
        global $DB;
        if (isset($questionid) && !empty($questionid)) {
            $DB->delete_records(self::TABLE_QUESTIONS, ['question_id' => $questionid]);
        }
    }

    public function get_question_next_position($groupid) {
        global $DB;
        $sql = 'SELECT MAX(position) as next FROM {'.self::TABLE_QUESTIONS.'} q ';
        $sql .= 'WHERE q.amcgroup_id=' .$groupid;
        $record_with_max_position = $DB->get_record_sql($sql);
        return $record_with_max_position && $record_with_max_position->next ? (int)$record_with_max_position->next + 1 : 1;
    }

    public function update_question_score($qid, $score) {
        global $DB;
        $row = $DB->get_record(self::TABLE_QUESTIONS, ['question_id' => $qid]);
        $row->score = $score;
        return $DB->update_record(self::TABLE_QUESTIONS, $row);
    }

    public function reorder_group_questions($data) {
        global $DB;
        foreach ($data as $groupquestion) {
            $groupid = $groupquestion['id'];
            $questions = $groupquestion['questions'];
            foreach ($questions as $question) {
                $position = $question['position'];
                $id = $question['id'];
                $row = $DB->get_record(self::TABLE_QUESTIONS, ['question_id' => $id]);
                if ($row) {
                    $row->amcgroup_id = $groupid;
                    $row->position = $position;
                    $DB->update_record(self::TABLE_QUESTIONS, $row);
                } else {
                    return false;
                }
            }
        }
        return true;
    }


}
