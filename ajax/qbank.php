<?php

global $USER;
require_once(__DIR__ . './../locallib.php');


// course id
$cid = required_param('cid', PARAM_INT);
// course module id
$cmid = optional_param('cmid', null, PARAM_INT);
// category id and context id
$catid = optional_param('catid', null, PARAM_TEXT);
$contextid = optional_param('contextid', null, PARAM_TEXT);
$target = optional_param('target', AMC_TARGET_QUESTION, PARAM_TEXT);


$course_context = context_course::instance($cid);
if (!has_capability('moodle/question:useall', $course_context, $USER)) {
    $result = [
      'status' => 401,
      'message' => 'You are not allowed to see this.'
    ];
} elseif ($catandcontext) {
    $questions_db = amcquiz_list_cat_and_context_questions($catid, $contextid, $target);
    $result = [
      'status' => 200,
      'questions' => $questions_db
    ];
} else {
    // retrieve categories as options to populate select element
    //$categories_db = amcquiz_list_categories();
    $categories = amcquiz_list_categories_options($cid, $cmid);
    $result = [
      'status' => 200,
      'categories' => $categories
    ];
}

echo json_encode($result);
