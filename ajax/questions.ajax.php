<?php

global $USER;
require_once(__DIR__ . './../locallib.php');

/**
 * This file handle all ajax requests for the question view associated with questions.js
 */

// course id
/*$cid = required_param('cid', PARAM_INT);
// course module id
$cmid = optional_param('cmid', null, PARAM_INT);
// category id and context id
$catid = optional_param('catid', null, PARAM_TEXT);

// current cat and context selection
$contextid = optional_param('contextid', null, PARAM_TEXT);
$target = optional_param('target', AMC_TARGET_QUESTION, PARAM_TEXT);*/
const ACTION_LOAD_CATEGORIES = 'load-categories';
const ACTION_LOAD_QUESTIONS = 'load-questions';
const ACTION_ADD_GROUP = '';
const ACTION_DELETE_GROUP = '';
const ACTION_UPDATE_GROUP_NAME = 'update-group-name';
const ACTION_DELETE_QUESTION = '';
const ACTION_ADD_QUESTIONS = '';
const ALLOWED_TARGETS = ['group', 'question'];




function valid_post_data($action) {
    switch ($action) {
        case ACTION_LOAD_CATEGORIES:
          return isset($_POST['cmid']) && !empty($_POST['cmid'])
                    && isset($_POST['target']) && !empty($_POST['target'])
                    && in_array($_POST['target'], ALLOWED_TARGETS);
        break;
        case ACTION_LOAD_QUESTIONS:
          return isset($_POST['contextid']) && !empty($_POST['contextid']) && isset($_POST['catid']) && !empty($_POST['catid']) && isset($_POST['target']) && !empty($_POST['target']) && in_array($_POST['target'], ALLOWED_TARGETS);
        break;
        default:
          return false;
    }
}

// mandatory parmeters for all actions
if (empty($_POST) || !isset($_POST['cid']) || empty($_POST['cid']) || !isset($_POST['action']) || empty($_POST['action'])) {
    $result = [
      'status' => 400,
      'message' => 'Bad Request.'
    ];
} else {
    $course_context = context_course::instance($_POST['cid']);

    if (!has_capability('moodle/question:useall', $course_context, $USER)) {
        $result = [
          'status' => 401,
          'message' => 'You are not allowed to see this.'
        ];
    } elseif ($_POST['action'] === ACTION_LOAD_CATEGORIES && valid_post_data(ACTION_LOAD_CATEGORIES)) {

        // get categories as options to populate select element
        $categories = amcquiz_list_categories_options($_POST['cid'], $_POST['cmid'], $_POST['target']);
        $result = [
          'status' => 200,
          'categories' => $categories
        ];
    } elseif ($_POST['action'] === ACTION_LOAD_QUESTIONS && valid_post_data(ACTION_LOAD_QUESTIONS)) {
        $questions_db = amcquiz_list_cat_and_context_questions($_POST['catid'], $_POST['contextid'], $_POST['target']);
        $result = [
          'status' => 200,
          'questions' => $questions_db
        ];
    } else {
        $result = [
          'status' => 400,
          'message' => 'Bad Request.'
        ];
    }
}



echo json_encode($result);


/*

if (!has_capability('moodle/question:useall', $course_context, $USER)) {
    $result = [
      'status' => 401,
      'message' => 'You are not allowed to see this.'
    ];
} elseif ($contextid && $target) {
    $questions_db = amcquiz_list_cat_and_context_questions($catid, $contextid, $target);
    $result = [
      'status' => 200,
      'questions' => $questions_db
    ];
} elseif ($cid && $cmid && $target) {
    // retrieve categories as options to populate select element
    $categories = amcquiz_list_categories_options($cid, $cmid, $target);
    $result = [
      'status' => 200,
      'categories' => $categories
    ];
} else {
    $result = [
      'status' => 400,
      'message' => 'Bad Request.'
    ];
}

echo json_encode($result);
*/
