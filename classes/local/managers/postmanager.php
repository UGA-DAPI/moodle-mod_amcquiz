<?php

namespace mod_amcquiz\local\managers;

// get some usefull constants
require_once __DIR__.'./../../../locallib.php';

class postmanager
{
    private $groupmanager;
    private $questionmanager;
    private $amcquizmanager;

    public function __construct()
    {
        $this->groupmanager = new \mod_amcquiz\local\managers\groupmanager();
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
        $this->amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    }

    /**
     * Handles all POST requests from view.php.
     *
     * @param array $post array of post data
     */
    public function handle_post_request($amcquizid, $post)
    {
        switch ($post['action']) {
            case ACTION_ADD_DESCRIPTION:
                $this->groupmanager->add_group_description($post['group-id'], $post['question-description-id']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_ADD_QUESTIONS:
                $this->questionmanager->add_group_questions($post['group-id'], $post['question-ids']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_DELETE_GROUP_DESCRIPTION:
                $this->groupmanager->delete_group_description($post['group-id']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_ADD_GROUP:
                $this->groupmanager->add_group($post['amcquiz-id']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_DELETE_GROUP:
                $this->groupmanager->delete_group($post['amcquiz-id'], $post['group-id']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_DELETE_QUESTION:
                $this->questionmanager->delete_group_question($post['question-id']);
                $this->amcquizmanager->amcquiz_set_timemodified($amcquizid);
                break;
            case ACTION_STUDENT_ACCESS:
                $this->amcquizmanager->set_student_access($post['amcquiz-id'], $post['studentcorrectionaccess'], $post['studentaanotatedaccess']);
                break;
            case ACTION_SEND_NOTIFICATION:
                $this->amcquizmanager->send_student_notification($post['amcquiz-id'], $post['cmid']);
                break;
        }
    }
}
