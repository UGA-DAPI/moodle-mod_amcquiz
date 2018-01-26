<?php

namespace mod_amcquiz\local\managers;

class questionmanager
{

    const TABLE_QUESTIONS = 'amcquiz_group_question';

    /**
     * Get questions related to a group
     * @param  int    $groupid
     * @param  int    $cmid course module id
     * @return array a collection of moodle question
     */
    public function get_group_questions(int $groupid, int $cmid = null) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $amcquestions = $DB->get_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $groupid], 'position');
        $result = array_map(function ($amcquestion) use ($DB, $cmid) {
            $item = new \stdClass();
            $moodle_question = \question_bank::load_question($amcquestion->question_id);
            if ($cmid !== null) {
                $context = \context_module::instance($cmid);
                $mappedanswers = array_map(function ($answer) use ($context, $moodle_question) {
                    $item = new \stdClass();
                    // answer content might contain image / sound / video
                    $content = \question_rewrite_question_preview_urls(
                        $answer->answer,
                        $moodle_question->id,
                        $moodle_question->contextid,
                        'question',
                        'answer',
                        $answer->id,
                        $context->id,
                        'amcquiz'
                    );
                    $item->answertext = format_text($content, $answer->answerformat);
                    $item->valid = $answer->fraction > 0;
                    return $item;
                }, $moodle_question->answers);
                $moodle_question->answers = array_values($mappedanswers);
            }

            $moodle_question->icon_plugin_name = $moodle_question->qtype->plugin_name();
            $moodle_question->icon_title = $moodle_question->qtype->local_name();
            $moodle_question->score = $amcquestion->score;
            $moodle_question->amcgroup_id = $amcquestion->amcgroup_id;
            $moodle_question->position = $amcquestion->position;
            return $moodle_question;
        }, $amcquestions);

        return array_values($result);
    }

    /**
     * Add an amcquiz question_group to a group
     * @param int   $groupid
     * @param array $questionsids moodle questions ids
     */
    public function add_group_questions(int $groupid, array $questionsids) {
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

    /**
     * Delete all group_question from a group
     * @param  int    $groupid
     */
    public function delete_group_questions(int $groupid) {
        global $DB;
        $DB->delete_records(self::TABLE_QUESTIONS, ['amcgroup_id' => $groupid]);
    }

    /**
     * Delete a group_question from a group
     * @param  int    $questionid
     */
    public function delete_group_question(int $questionid) {
        global $DB;
        if (isset($questionid) && !empty($questionid)) {
            $DB->delete_records(self::TABLE_QUESTIONS, ['question_id' => $questionid]);
        }
    }

    /**
     * Get the proper position for a newly added question
     * @param  int    $groupid
     * @return int the proper position
     */
    public function get_question_next_position(int $groupid) {
        global $DB;
        $sql = 'SELECT MAX(position) as next FROM {'.self::TABLE_QUESTIONS.'} q ';
        $sql .= 'WHERE q.amcgroup_id=' .$groupid;
        $record_with_max_position = $DB->get_record_sql($sql);
        return $record_with_max_position && $record_with_max_position->next ? (int)$record_with_max_position->next + 1 : 1;
    }

    /**
     * Update a amcquiz_group_question score
     * @param  int    $qid   moodle question id
     * @param  float  $score
     * @return bool
     */
    public function update_question_score(int $qid, float $score) {
        global $DB;
        $row = $DB->get_record(self::TABLE_QUESTIONS, ['question_id' => $qid]);
        $row->score = $score;
        return $DB->update_record(self::TABLE_QUESTIONS, $row);
    }

    /**
     * reorder a set of amcquiz_group_question
     * @param  array  $data set of moodle_question_id / position
     * @return bool
     */
    public function reorder_group_questions(array $data) {
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
