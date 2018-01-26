<?php

/* FRONT CTRL */

require_once(__DIR__ . '/locallib.php');

global $OUTPUT, $PAGE, $CFG, $USER;


$service = new \mod_amcquiz\shared_service();
$service->parse_request();
$amcquiz = $service->amcquiz;
$current_view = $service->current_view;
$cm = $service->cm;
$course = $service->course;

$viewcontext = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/amcquiz:view', $viewcontext);

$renderer = $PAGE->get_renderer('mod_amcquiz');
$renderer->render_from_template('mod_amcquiz/noscript', []);

$PAGE->set_url('/mod/amcquiz/view.php', ['id' => $cm->id, 'current' => $current_view]);
$PAGE->requires->js_call_amd('mod_amcquiz/common', 'init', []);

echo $renderer->render_header($amcquiz, $cm);

if (!has_capability('mod/amcquiz:update', $viewcontext)) {
    $studentview = new \mod_amcquiz\output\view_student($amcquiz, $USER);
    echo $renderer->render_student_view($studentview);
} else {
    $tabs = new \mod_amcquiz\output\tabs($amcquiz, $context, $cm, $current_view);
    echo $renderer->render_tabs($tabs);

    if (isset($_POST['action'])) {
        $postmanager = new \mod_amcquiz\local\managers\postmanager();
        $postmanager->handle_post_request($_POST);
        // update amcquiz object after post actions
        $amcquiz = $service->amcquizmanager->get_amcquiz_record($amcquiz->id, $cm->id);
    }
    // render desired view with proper data
    switch ($current_view) {
        case 'questions':
            $PAGE->requires->js_call_amd('mod_amcquiz/questions', 'init', [$amcquiz->id, $course->id, $cm->id]);
            // additional data to pass to view_questions renderer
            $data = [
                'cmid' => $cm->id,
                'courseid' => $course->id,
                'pageurl' => '/mod/amcquiz/view.php?id=' . $cm->id . '&current=' . $current_view
            ];
            $content = new \mod_amcquiz\output\view_questions($amcquiz, $data);
            echo $renderer->render_questions_view($content);
            break;
        case 'subjects':
            echo '<h4>Documents</h4>';
            break;
        case 'sheets':
            echo '<h4>Dépot des copies</h4>';
            break;
        case 'associate':
            echo '<h4>Identification</h4>';
            break;
        case 'annotate':
            echo '<h4>Notes</h4>';
            break;
        case 'correction':
            echo '<h4>Correction</h4>';
            break;
        default:
            echo '<h4>Questions</h4>';
    }
}

echo $OUTPUT->footer();
