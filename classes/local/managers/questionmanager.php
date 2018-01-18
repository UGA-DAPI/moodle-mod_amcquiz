<?php

namespace mod_amcquiz\local\managers;

class questionmanager
{

    private $amcquizmanager;


    public function __construct(){
        $this->amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    }

    public function add_group_description($group_id, $question_id){
        global $DB;
        $row = new \stdClass();
        $row->id = $group_id;
        $row->description_question_id = $question_id;
        $DB->update_record($this->amcquizmanager::TABLE_GROUPS, $row);
    }

    public function add_group_questions($group_id, $questionsids){
        global $DB;
        $row = new \stdClass();
        $row->amcgroup_id = $group_id;
        // get max position from question table where amcgroup_id = $group_id
        $sql = 'SELECT MAX(position) as next FROM {'.$this->amcquizmanager::TABLE_QUESTIONS.'} q ';
        $sql .= 'WHERE q.amcgroup_id=' .$group_id;
        $record_with_max_position = $DB->get_record_sql($sql);
        $next = $record_with_max_position->next ? (int)$record_with_max_position->next + 1 : 1;

        foreach ($questionsids as $id) {
            $row->question_id = $id;
            $row->score = 1;
            $row->position = $next;
            $DB->insert_record($this->amcquizmanager::TABLE_QUESTIONS, $row);
            $next++;
        }
    }
}
