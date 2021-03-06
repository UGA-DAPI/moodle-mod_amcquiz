<?php


/* FRONT CTRL */

require_once __DIR__.'/locallib.php';

global $OUTPUT, $PAGE, $USER;

$service = new \mod_amcquiz\shared_service();

$service->parse_request();
$cm = $service->cm;
$course = $service->course;
$amcquiz = $service->amcquiz;
$current_view = $service->current_view;

$PAGE->set_url('/mod/amcquiz/view.php', ['id' => $cm->id, 'current' => $current_view]);

$context = context_module::instance($cm->id);
require_capability('mod/amcquiz:view', $context);
require_login($course, true, $cm);

$renderer = $PAGE->get_renderer('mod_amcquiz');
$renderer->render_from_template('mod_amcquiz/noscript', []);
$PAGE->requires->js_call_amd('mod_amcquiz/common', 'init', []);

// we want to know if documents notation and correction should be regenerated
$shouldupdatedocuments = $service->should_update_documents($amcquiz);

// get api url value in order to inject it in javascripts when needed
$apiurl = get_config('mod_amcquiz', 'apiurl');
$apikey = $amcquiz->apikey;

echo $renderer->render_header($amcquiz, $context);

if (!has_capability('mod/amcquiz:update', $context)) {
    $studentview = new \mod_amcquiz\output\view_student($amcquiz, $USER);
    echo $renderer->render_student_view($studentview);
} else {
    $result = false;
    if (isset($_POST['action'])) {
        $postmanager = new \mod_amcquiz\local\managers\postmanager($amcquiz);
        $result = $postmanager->handle_post_request($_POST, $_FILES);
        // update amcquiz object after post actions
        $amcquiz = $service->amcquizmanager->get_full_amcquiz_record($amcquiz->id, $cm->id);
    }

    // prepare-source should be recreated and sent
    if ($amcquiz->associated_at && $amcquiz->scale_updated_at && $amcquiz->scale_updated_at > $amcquiz->associated_at) {
        $base64latex = $service->amcquizmanager->amcquiz_export($amcquiz->id, true);
        $amcquiz = $service->amcquizmanager->send_latex_file($amcquiz, $base64latex);
    }

    // POST USER MESSAGES... diplayed above amcquiz title...
    if ($result && isset($result['status']) && 200 === $result['status'] && isset($result['message']) && '' !== $result['message']) {
        \core\notification::info($result['message']);
    } elseif ($result && isset($result['status']) && 200 !== $result['status'] && isset($result['message']) && '' !== $result['message']) {
        \core\notification::error($result['message']);
    }

    // TABS
    $disabledtabs = $service->get_disabled_tabs($amcquiz);
    $view = $service->check_current_tab($current_view, $disabledtabs);
    $tabs = new \mod_amcquiz\output\tabs($amcquiz, $cm, $view);
    echo $renderer->render_tabs($tabs);

    // render desired view with proper data
    switch ($view) {
        case 'questions':
            $PAGE->requires->js_call_amd('mod_amcquiz/questions', 'init', [$amcquiz->id, $course->id, $cm->id]);
            // additional data to pass to view_questions renderer
            $data = [
                'cmid' => $cm->id,
                'courseid' => $course->id,
                'pageurl' => '/mod/amcquiz/view.php?id='.$cm->id.'&current='.$view,
            ];
            $content = new \mod_amcquiz\output\view_questions($amcquiz, $data);
            echo $renderer->render_questions_view($content);
            break;
        case 'documents':
            if ($shouldupdatedocuments) {
                \core\notification::warning(get_string('usermessage_documents_outdated', 'mod_amcquiz'));
            }
            $PAGE->requires->js_call_amd('mod_amcquiz/documents', 'init', []);
            // additional data to pass to view_documents renderer
            $data = [
              'cmid' => $cm->id,
            ];
            $content = new \mod_amcquiz\output\view_documents($amcquiz, $data);
            echo $renderer->render_documents_view($content);
            break;
        case 'sheets':
            if ($shouldupdatedocuments) {
                \core\notification::warning(get_string('usermessage_documents_outdated', 'mod_amcquiz'));
            }
            $PAGE->requires->js_call_amd('mod_amcquiz/sheets', 'init', []);
            // additional data to pass to view_sheets renderer
            $data = [
              'cmid' => $cm->id,
            ];
            $content = new \mod_amcquiz\output\view_sheets($amcquiz, $data);
            echo $renderer->render_sheets_view($content);
            break;
        case 'associate':
            $PAGE->requires->js_call_amd('mod_amcquiz/associate', 'init', []);
            // additional data to pass to view_associate renderer
            $data = [
              'cm' => $cm,
            ];
            $content = new \mod_amcquiz\output\view_associate($amcquiz, $data);
            echo $renderer->render_associate_view($content);
            break;
        case 'grade':
            if ($shouldupdatedocuments) {
                \core\notification::warning(get_string('usermessage_documents_outdated', 'mod_amcquiz'));
            }
            // additional data to pass to view_annotate renderer
            $data = [
              'cm' => $cm,
            ];
            $content = new \mod_amcquiz\output\view_grade($amcquiz, $data);
            echo $renderer->render_grade_view($content);
            break;
        case 'correction':
            if ($shouldupdatedocuments) {
                \core\notification::warning(get_string('usermessage_documents_outdated', 'mod_amcquiz'));
            }
            // additional data to pass to view_correction renderer
            $data = [
              'cm' => $cm,
            ];
            $content = new \mod_amcquiz\output\view_correction($amcquiz, $data);
            echo $renderer->render_correction_view($content);
            break;
      }
}

echo $OUTPUT->footer();
