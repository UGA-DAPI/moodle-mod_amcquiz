<?php

global $USER;
// for constants and some methods
require_once(__DIR__ . './../locallib.php');

/**
 * This file handle all ajax requests for the question view associated with documents.js
 */

function valid_post_data($action) {
    switch ($action) {
        case 'export':
            return isset($_POST['cmid']) && !empty($_POST['cmid']) && isset($_POST['amcquizid']) && !empty($_POST['amcquizid']);
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
    } elseif ($_POST['action'] === 'export' && valid_post_data('export')) {
        $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
        $data = $amcquizmanager->amcquiz_export($_POST['amcquizid'], $_POST['cmid']);
        $result = [
          'status' => 200,
          'message' => 'success',
          'data' => $data
        ];
    } else {
        $result = [
          'status' => 400,
          'message' => 'Bad Request.'
        ];
    }
}

echo json_encode($result);