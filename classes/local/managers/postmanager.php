<?php

namespace mod_amcquiz\local\managers;

// get some usefull constants
require_once __DIR__.'./../../../locallib.php';

class postmanager
{
    private $groupmanager;
    private $questionmanager;
    private $amcquizmanager;
    private $curlmanager;
    private $amcquiz;

    public function __construct($amcquiz)
    {
        $this->groupmanager = new \mod_amcquiz\local\managers\groupmanager();
        $this->questionmanager = new \mod_amcquiz\local\managers\questionmanager();
        $this->amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
        $this->curlmanager = new \mod_amcquiz\local\managers\curlmanager();
        $this->amcquiz = $amcquiz;
    }

    /**
     * Handles all POST requests from view.php.
     *
     * @param stdClass $amcquiz
     * @param array    $post    array of post data
     */
    public function handle_post_request($post)
    {
        switch ($post['action']) {
            case ACTION_ADD_DESCRIPTION:
                $success = $this->groupmanager->add_group_description($post['group-id'], $post['question-description-id']);
                if ($success) {
                    $success = $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while adding group description question.',
                ];

                return $result;
            case ACTION_ADD_QUESTIONS:
                $success = $this->questionmanager->add_group_questions($post['group-id'], $post['question-ids']);
                if ($success) {
                    $success = $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while adding question.',
                ];

                return $result;
            case ACTION_DELETE_GROUP_DESCRIPTION:
                $success = $this->groupmanager->delete_group_description($post['group-id']);
                if ($susccess) {
                    $success = $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while deleting group description question.',
                ];

                return $result;
            case ACTION_ADD_GROUP:
                $success = $this->groupmanager->add_group($this->amcquiz->id);
                if ($success) {
                    $success = $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz->id);
                }

                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while adding group.',
                ];

                return $result;
            case ACTION_DELETE_GROUP:
                $success = $this->groupmanager->delete_group($this->amcquiz->id, $post['group-id']);
                if ($success) {
                    $success = $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while deleting group.',
                ];

                return $result;
            case ACTION_DELETE_QUESTION:
                $success = $this->questionmanager->delete_group_question($post['question-id']);
                if ($success) {
                    $this->amcquizmanager->amcquiz_set_timemodified($this->amcquiz);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while deleting question.',
                ];

                return $result;
            case ACTION_STUDENT_ACCESS:
                $success = $this->amcquizmanager->set_student_access($this->amcquiz->id, $post['studentcorrectionaccess'], $post['studentaanotatedaccess']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while setting student access.',
                ];

                return $result;
            case ACTION_SEND_NOTIFICATION:
                $success = $this->amcquizmanager->send_student_notification($this->amcquiz->id, $post['cmid']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => 'error while sending student notification.',
                ];

                return $result;
            case ACTION_EXPORT_QUIZ:
                $result = $this->amcquizmanager->amcquiz_export($this->amcquiz->id);

                if (!$result || (isset($result['errors']) && count($result['errors']) > 0)) {
                    return [
                    'status' => 400,
                    'message' => 'error while exporting amcquiz.',
                  ];
                }

                // send zip to API
                $result = $this->curlmanager->send_zipped_amcquiz($this->amcquiz, $result['zipfile']);

                if (!$result || (isset($result['errors']) && count($result['errors']) > 0)) {
                    return [
                    'status' => 400,
                    'message' => 'error while sending amcquiz zip file.',
                  ];
                }

                $this->amcquizmanager->amcquiz_set_documents_created($this->amcquiz->id);

                return $result;
            case 'test':
                return $this->curlmanager->test_post_api($this->amcquiz, $post);
        }
    }
}
