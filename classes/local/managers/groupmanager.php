<?php

namespace mod_amcquiz\local\managers;

class groupmanager
{
    private $questionmanager;

    const TABLE_GROUPS = 'amcquiz_group';

    public function __construct()
    {
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
    }

    /**
     * Add a group to a quiz.
     *
     * @param int $amcquizid amcquiz id
     */
    public function add_group(int $amcquizid)
    {
        global $DB;
        $group = new \stdClass();
        $group->amcquiz_id = $amcquizid;
        $group->position = $this->get_group_next_position($amcquizid);
        $group->name = 'group-'.$group->position;
        $DB->insert_record(self::TABLE_GROUPS, $group);
    }

    /**
     * Add a description question to a group.
     *
     * @param int $groupid
     * @param int $questionid
     */
    public function add_group_description(int $groupid, int $questionid)
    {
        global $DB;
        $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
        if ($row && isset($questionid) && !empty($questionid)) {
            $row->description_question_id = $questionid;
            $DB->update_record(self::TABLE_GROUPS, $row);
        }
    }

    /**
     * Remove description question from group.
     *
     * @param int $groupid
     */
    public function delete_group_description(int $groupid)
    {
        global $DB;
        $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
        $row->description_question_id = null;
        $DB->update_record(self::TABLE_GROUPS, $row);
    }

    /**
     * Set / update group name.
     *
     * @param int    $groupid
     * @param string $name    group name
     *
     * @return bool
     */
    public function update_group_name(int $groupid, string $name)
    {
        global $DB;
        $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $groupid]);
        $row->name = $name;

        return $DB->update_record(self::TABLE_GROUPS, $row);
    }

    /**
     * Delete a group from an amcquiz
     * Also handle questions related to the deleted group.
     *
     * @param int $amcquizid
     * @param int $groupid
     */
    public function delete_group(int $amcquizid, int $groupid)
    {
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
                $this->questionmanager->add_group_questions($prevgroup->id, $questionids);
            } else {
                // if no previous group add question to the first next group
                $followinggroup = $nextgroups[0];
                $this->questionmanager->add_group_questions($followinggroup->id, $questionids);
            }
            // in any case update following groups position
            $next = $current->position;
            foreach ($nextgroups as $group) {
                $group->position = $next;
                $group->name = 'group-'.$next;
                $DB->update_record(self::TABLE_GROUPS, $group);
                ++$next;
            }

            $DB->delete_records(self::TABLE_GROUPS, ['id' => $groupid]);
            // remove deleted group questions
            $this->questionmanager->delete_group_questions($groupid);
        }
    }

    /**
     * Reorder a set of groups.
     *
     * @param array $data array of groupid / position
     *
     * @return bool
     */
    public function reorder_groups(array $data)
    {
        global $DB;
        foreach ($data as $item) {
            $gid = $item['id'];
            $position = $item['position'];
            $row = $DB->get_record(self::TABLE_GROUPS, ['id' => $gid]);
            if ($row) {
                $row->position = $position;
                $row->name = 'group-'.$position;
                $DB->update_record(self::TABLE_GROUPS, $row);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an amcquiz groups.
     *
     * @param int $amcquizid
     *
     * @return array a collection of groups
     */
    public function get_quiz_groups(int $amcquizid)
    {
        global $DB;
        // sort parameter how to tell if ASC or DESC ?
        $groups = $DB->get_records(self::TABLE_GROUPS, ['amcquiz_id' => $amcquizid], 'position');
        // need to rebuild array for template iteration to work
        // (https://docs.moodle.org/dev/Templates#Iterating_over_php_arrays_in_a_mustache_template)
        return array_values($groups);
    }

    /**
     * Get the position for a newly created group.
     *
     * @param int $amcquizid
     *
     * @return int the position for the group
     */
    public function get_group_next_position(int $amcquizid)
    {
        global $DB;
        $sql = 'SELECT MAX(position) as next FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id='.$amcquizid;
        $record_with_max_position = $DB->get_record_sql($sql);

        return $record_with_max_position && $record_with_max_position->next ? (int) $record_with_max_position->next + 1 : 1;
    }

    /**
     * Get groups after a given position.
     *
     * @param int $amcquizid
     * @param int $position
     *
     * @return array a collection of group
     */
    public function get_next_groups(int $amcquizid, int $position)
    {
        global $DB;
        $sql = 'SELECT * FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id = ? AND g.position > ? ORDER BY g.position ASC';
        $result = $DB->get_records_sql($sql, [$amcquizid, $position]);

        return array_values($result);
    }

    /**
     * Get a group which position is prior to the given one.
     *
     * @param int $amcquizid
     * @param int $position
     *
     * @return stdClass an amcquiz group
     */
    public function get_prev_group(int $amcquizid, int $position)
    {
        global $DB;
        $sql = 'SELECT * FROM {'.self::TABLE_GROUPS.'} g ';
        $sql .= 'WHERE g.amcquiz_id = ? AND g.position = ?';
        $result = $DB->get_record_sql($sql, [$amcquizid, (int) $position - 1]);

        return $result;
    }

    /**
     * Get questions related to a group.
     *
     * @param int $groupid
     * @param int $cmid    course module id
     *
     * @return array a collection of moodle question
     */
    public function get_group_questions(int $groupid, int $cmid = null)
    {
        return $this->questionmanager->get_group_questions($groupid, $cmid);
    }

    /**
     * Count questions belonging to a given group.
     *
     * @param int $groupid
     *
     * @return int
     */
    public function count_group_questions(int $groupid)
    {
        return $this->questionmanager->count_group_questions($groupid);
    }

    /**
     * Export questions belonging to a given group.
     *
     * @param int                   $groupid
     * @param string                $destfolder
     * @param mod_amcquiztranslator $translator
     *
     * @return array
     */
    public function export_group_questions(int $groupid, string $destfolder, \mod_amcquiz\translator $translator)
    {
        return $this->questionmanager->count_group_questions($groupid, $destfolder, $translator);
    }
}
