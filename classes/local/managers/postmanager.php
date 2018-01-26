<?php

namespace mod_amcquiz\local\managers;

// get some usefull constants
require_once(__DIR__ . './../../../locallib.php');

class postmanager
{

    private $groupmanager;
    private $questionmanager;

    public function __construct() {
        $this->groupmanager = new \mod_amcquiz\local\managers\groupmanager();
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
    }

    /**
     * Handles all POST requests from view.php
     * @param  array $post array of post data
     */
    public function handle_post_request($post) {
        switch ($post['action']) {
            case ACTION_ADD_DESCRIPTION:
                $this->groupmanager->add_group_description($post['group-id'], $post['question-description-id']);
                break;
            case ACTION_ADD_QUESTIONS:
                $this->questionmanager->add_group_questions($post['group-id'], $post['question-ids']);
                break;
            case ACTION_DELETE_GROUP_DESCRIPTION:
                $this->groupmanager->delete_group_description($post['group-id']);
                break;
            case ACTION_ADD_GROUP:
                $this->groupmanager->add_group($post['amcquiz-id']);
                break;
            case ACTION_DELETE_GROUP:
                $this->groupmanager->delete_group($post['amcquiz-id'], $post['group-id']);
                break;
            case ACTION_DELETE_QUESTION:
                $this->questionmanager->delete_group_question($post['question-id']);
                break;
        }
    }

}
