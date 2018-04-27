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
     * @param array    $files   array of posted file
     */
    public function handle_post_request($post, $files = null)
    {
        switch ($post['action']) {
            case ACTION_ADD_DESCRIPTION:
                $success = $this->groupmanager->add_group_description($post['group-id'], $post['question-description-id']);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_add_group_description_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_ADD_QUESTIONS:
                $success = $this->questionmanager->add_group_questions($post['group-id'], $post['question-ids']);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_add_question_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_DELETE_GROUP_DESCRIPTION:
                $success = $this->groupmanager->delete_group_description($post['group-id']);
                if ($susccess) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_delete_group_description_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_ADD_GROUP:
                $success = $this->groupmanager->add_group($this->amcquiz->id);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }

                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_add_group_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_DELETE_GROUP:
                $success = $this->groupmanager->delete_group($this->amcquiz->id, $post['group-id']);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_delete_group_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_DELETE_QUESTION:
                $success = $this->questionmanager->delete_group_question($post['question-id']);
                if ($success) {
                    $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('question_delete_question_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_STUDENT_ACCESS:
                $success = $this->amcquizmanager->set_student_access($this->amcquiz->id, $post['studentcorrectionaccess'], $post['studentaanotatedaccess']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('correction_set_student_access_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_SEND_NOTIFICATION:
                $success = $this->amcquizmanager->send_student_notification($this->amcquiz->id, $post['cmid']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : get_string('correction_send_student_notification_error', 'mod_amcquiz'),
                ];

                return $result;
            case ACTION_EXPORT_QUIZ:
                if (!$this->amcquiz->uselatexfile) {
                    $result = $this->amcquizmanager->amcquiz_export($this->amcquiz->id);

                    if (!$result || (isset($result['errors']) && count($result['errors']) > 0)) {
                        return [
                          'status' => 400,
                          'message' => get_string('export_amcquiz_error', 'mod_amcquiz'),
                        ];
                    }

                    // send zip to API
                    $result = $this->curlmanager->send_zipped_amcquiz($this->amcquiz, $result['zipfile']);

                    if (!$result || (isset($result['status']) && 200 !== $result['status'])) {
                        return [
                          'status' => 400,
                          'message' => get_string('api_send_zipped_quiz_error', 'mod_amcquiz'),
                        ];
                    }
                }

                $result = $this->curlmanager->generate_documents($this->amcquiz);
                $this->amcquizmanager->set_documents_created($this->amcquiz->id);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_generate_documents_success', 'mod_amcquiz') : get_string('api_generate_documents_error', 'mod_amcquiz'),
                ];

            case ACTION_DELETE_UNRECOGNIZED_SHEETS:
                $result = $this->curlmanager->delete_unrecognized_sheets($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_delete_unrecognized_sheets_success', 'mod_amcquiz') : get_string('api_delete_unrecognized_sheets_error', 'mod_amcquiz'),
                ];
            case ACTION_DELETE_ALL_SHEETS:
                $result = $this->curlmanager->delete_all_sheets($this->amcquiz);
                $this->amcquizmanager->set_sheets_uploaded_time($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_delete_all_sheets_success', 'mod_amcquiz') : get_string('api_delete_all_sheets_error', 'mod_amcquiz'),
                ];
            case ACTION_LAUNCH_ASSOCIATION:
                $result = $this->curlmanager->launch_grade($this->amcquiz);
                if (200 !== $result['status']) {
                    return [
                    'status' => $result['status'],
                    'message' => get_string('api_launch_association_note_error', 'mod_amcquiz'),
                  ];
                }
                // generate students csv file
                $csv = $this->amcquizmanager->generate_students_csv($this->amcquiz);
                if (!$csv) {
                    return [
                    'status' => 500,
                    'message' => get_string('api_launch_association_csv_error', 'mod_amcquiz'),
                  ];
                }
                $result = $this->curlmanager->launch_association($this->amcquiz, $csv);
                if (200 === $result['status']) {
                    $this->amcquizmanager->set_association_time($this->amcquiz);
                }

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_launch_association_success', 'mod_amcquiz') : get_string('api_launch_association_error', 'mod_amcquiz'),
                ];
            case ACTION_ASSOCIATE_MANUALLY:

                $filecode = $post['filecode'];
                $idnumber = $post['idnumber'];
                $result = $this->curlmanager->associate_sheet_manually($this->amcquiz, $filecode, $idnumber);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_associate_sheet_manually_success', 'mod_amcquiz') : get_string('api_associate_sheet_manually_error', 'mod_amcquiz'),
                ];
            case ACTION_LAUNCH_GRADING_DOCS_GENERATION:
                $result = $this->curlmanager->launch_grade_docs_generation($this->amcquiz);
                $this->amcquizmanager->set_grading_time($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? 'hurray!' : ':(',
                ];
            case ACTION_RECORD_GRADE_BOOK:
              $result = $this->curlmanager->get_grade_json($this->amcquiz);
              if (200 !== $result['status']) {
                  return [
                  'status' => $result['status'],
                  'message' => 'problem while getting grades in json.',
                ];
              }
              $grades = $result['data'];
              $success = $this->amcquizmanager->record_grades($this->amcquiz, $grades);

              return [
                'status' => $success ? 200 : 500,
                'message' => $success ? 'notes where put in gradebook' : 'problem while recording notes in gradebook',
              ];
            case ACTION_ANNOTATE_SHEETS:
                $result = $this->curlmanager->annotate($this->amcquiz);
                $this->amcquizmanager->set_annotated_at($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => 200 === $result['status'] ? get_string('api_annotate_success', 'mod_amcquiz') : get_string('api_annotate_error', 'mod_amcquiz'),
                ];
            case ACTION_GET_SUBJECT_PDF:
                $result = $this->curlmanager->get_subject_pdf($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
            case ACTION_GET_CATALOG_PDF:
                $result = $this->curlmanager->get_catalog_pdf($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
            case ACTION_GET_CORRECTION_PDF:
                $result = $this->curlmanager->get_correction_pdf($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
            case ACTION_GET_DOCUMENTS_ZIP:
                $result = $this->curlmanager->get_documents_zip($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
            case ACTION_GET_GRADE_CSV:
                $result = $this->curlmanager->get_grade_csv($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
            case ACTION_GET_GRADE_ODS:
                $result = $this->curlmanager->get_grade_ods($this->amcquiz);

                return [
                  'status' => $result['status'],
                  'message' => '',
                ];
        }
    }
}
