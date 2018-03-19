<?php

defined('MOODLE_INTERNAL') || die;

global $USER;
// for constants and some methods
require_once __DIR__.'./../locallib.php';

/**
 * This file handle all ajax requests for the question view associated with questions.js.
 */
function valid_post_data($action)
{
    switch ($action) {
        case ACTION_LOAD_CATEGORIES:
            return isset($_POST['cmid']) && !empty($_POST['cmid'])
                    && isset($_POST['target']) && !empty($_POST['target'])
                    && in_array($_POST['target'], ALLOWED_TARGETS);
        break;
        case ACTION_LOAD_QUESTIONS:
            return isset($_POST['contextid']) && !empty($_POST['contextid']) && isset($_POST['catid']) && !empty($_POST['catid']) && isset($_POST['target']) && !empty($_POST['target']) && in_array($_POST['target'], ALLOWED_TARGETS);
        break;
        case ACTION_UPDATE_GROUP_NAME:
            return isset($_POST['gid']) && !empty($_POST['gid']);
        break;
        case ACTION_UPDATE_QUESTION_SCORE:
            return isset($_POST['qid']) && !empty($_POST['qid']) && isset($_POST['score']) && !empty($_POST['score']) && is_numeric($_POST['score']);
        break;
        case ACTION_REORDER_GROUPS:
        case ACTION_REORDER_GROUP_QUESTIONS:
            return isset($_POST['data']) && !empty($_POST['data']);
        break;
        default:
            return false;
    }
}

// mandatory parmeters for all actions
if (empty($_POST) || !isset($_POST['cid']) || empty($_POST['cid']) || !isset($_POST['action']) || empty($_POST['action'])) {
    $result = [
      'status' => 400,
      'message' => 'Bad Request.',
    ];
} else {
    $course_context = context_course::instance($_POST['cid']);

    if (!has_capability('moodle/question:useall', $course_context, $USER)) {
        $result = [
          'status' => 401,
          'message' => 'You are not allowed to see this.',
        ];
    } elseif (ACTION_LOAD_CATEGORIES === $_POST['action'] && valid_post_data(ACTION_LOAD_CATEGORIES)) {
        $used_ids = isset($_POST['usedids']) ? $_POST['usedids'] : [];
        // get categories as options to populate select element
        $categories = amcquiz_list_categories_options($_POST['cid'], $_POST['cmid'], $_POST['target'], $used_ids);
        $result = [
          'status' => 200,
          'categories' => $categories,
        ];
    } elseif (ACTION_LOAD_QUESTIONS === $_POST['action'] && valid_post_data(ACTION_LOAD_QUESTIONS)) {
        $used_ids = isset($_POST['usedids']) ? $_POST['usedids'] : [];
        $questions_db = amcquiz_list_cat_and_context_questions($_POST['catid'], $_POST['contextid'], $_POST['target'], $used_ids);
        $result = [
          'status' => 200,
          'questions' => $questions_db,
        ];
    } elseif (ACTION_UPDATE_GROUP_NAME === $_POST['action'] && valid_post_data(ACTION_UPDATE_GROUP_NAME)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $manager = new \mod_amcquiz\local\managers\groupmanager();
        $success = $manager->update_group_name($_POST['gid'], $name);
        $result = [
            'status' => $success ? 200 : 400,
            'message' => $success ? 'success' : 'error',
        ];
    } elseif (ACTION_UPDATE_QUESTION_SCORE === $_POST['action'] && valid_post_data(ACTION_UPDATE_QUESTION_SCORE)) {
        $manager = new \mod_amcquiz\local\managers\questionmanager();
        $success = $manager->update_question_score($_POST['qid'], $_POST['score']);
        if ($success) {
            $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
            $amcquizmanager->set_timemodified($_POST['amcquizid']);
        }
        $result = [
            'status' => $success ? 200 : 400,
            'message' => $success ? 'success' : 'error',
        ];
    } elseif (ACTION_REORDER_GROUP_QUESTIONS === $_POST['action'] && valid_post_data(ACTION_REORDER_GROUP_QUESTIONS)) {
        $manager = new \mod_amcquiz\local\managers\questionmanager();
        $success = $manager->reorder_group_questions($_POST['data']);
        if ($success) {
            $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
            $amcquizmanager->set_timemodified($_POST['amcquizid']);
        }
        $result = [
            'status' => $success ? 200 : 400,
            'message' => $success ? 'success' : 'error',
        ];
    } elseif (ACTION_REORDER_GROUPS === $_POST['action'] && valid_post_data(ACTION_REORDER_GROUPS)) {
        $manager = new \mod_amcquiz\local\managers\groupmanager();
        $success = $manager->reorder_groups($_POST['data']);
        if ($success) {
            $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
            $amcquizmanager->set_timemodified($_POST['amcquizid']);
        }
        $result = [
            'status' => $success ? 200 : 400,
            'message' => $success ? 'success' : 'error',
        ];
    } else {
        $result = [
          'status' => 400,
          'message' => 'Bad Request.',
        ];
    }
}

echo json_encode($result);
