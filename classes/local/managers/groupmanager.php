<?php

namespace mod_amcquiz\local\managers;

class groupmanager
{
    private $questionmanager;

    const TABLE_GROUPS = 'amcquiz_group';


    public function __construct() {
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
    }

    public function add_group($amcquizid) {
        global $DB;
        $group = new \stdClass();
        $group->name = '';
        $group->amcquiz_id = $amcquizid;
        $group->position = $this->get_group_next_position($amcquizid);
        $DB->insert_record(self::TABLE_GROUPS, $group);
    }

    public function add_group_description($groupid, $questionid) {
        global $DB;
        $row = new \stdClass();
        $row->id = $groupid;
        $row->description_question_id = $question_id;
        $DB->update_record(self::TABLE_GROUPS, $row);
    }

    public function delete_group_description($groupid) {
        global $DB;
        $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
        $row->description_question_id = null;
        $DB->update_record(self::TABLE_GROUPS, $row);
    }

    public function update_group_name($groupid, $name) {
        global $DB;
        $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
        $row->name = $name;
        return $DB->update_record(self::TABLE_GROUPS, $row);
    }


    public function delete_group($amcquizid, $groupid) {
        global $DB;
        // check if another group exist for this amcquiz
        $hasmorethanonegroup = $DB->count_records(self::TABLE_GROUPS, ['amcquiz_id' => $amcquizid]) > 1;
        if ($hasmorethanonegroup) {
            $groupquestions = $this->questionmanager->get_group_questions($groupid);
            $questionids = array_map(function ($question) {
                return $question->id;
            }, $groupquestions);
            $current = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
            $prevgroup = $this->get_prev_group($amcquizid, $current->position);
            $nextgroups = $this->get_next_groups($amcquizid, $current->position);
            if ($prevgroup) {
                $this->questionmanager->add_add_group_questions($prevgroup->id, $questionids);
            } else {
                // if no previous group add question to the first next group
                $followinggroup = $nextgroups[0];
                $this->questionmanager->add_add_group_questions($followinggroup->id, $questionids);
            }
            // in any case update following groups position
            $next = $current->position;
            foreach ($nextgroups as $group) {
                $group->position = $next;
                $DB->update_record(self::TABLE_GROUPS, $group);
                $next++;
            }
        }

    }

    public function get_quiz_groups(int $amcquizid) {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $groups = $DB->get_records(self::TABLE_GROUPS, ['amcquiz_id' => $amcquizid], 'position');
        // Need to rebuild array for template iteration to work (https://docs.moodle.org/dev/Templates#Iterating_over_php_arrays_in_a_mustache_template)
        return array_values($groups);
    }

    public function get_group_next_position($amcquizid) {
        global $DB;
        $sql = 'SELECT MAX(position) as next FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id=' .$amcquizid;
        $record_with_max_position = $DB->get_record_sql($sql);
        return $record_with_max_position && $record_with_max_position->next ? (int)$record_with_max_position->next + 1 : 1;
    }

    public function get_next_groups($amcquizid, $position) {
        global $DB;
        $sql = 'SELECT * FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id = ? AND g.position > ? ORDER BY g.position ASC';
        $result = $DB->get_records_sql($sql, [$amcquizid , $position]);
        return $result;
    }

    public function get_prev_group($amcquizid, $position) {
        global $DB;
        $sql = 'SELECT * FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id = ? AND g.position = ?';
        $result = $DB->get_record_sql($sql, [$amcquizid , (int)$position - 1]);
        return $result;
    }

}
