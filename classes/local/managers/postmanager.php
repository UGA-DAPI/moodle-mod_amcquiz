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
                  'message' => $success ? '' : 'error while adding group description question.',
                ];

                return $result;
            case ACTION_ADD_QUESTIONS:
                $success = $this->questionmanager->add_group_questions($post['group-id'], $post['question-ids']);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while adding question.',
                ];

                return $result;
            case ACTION_DELETE_GROUP_DESCRIPTION:
                $success = $this->groupmanager->delete_group_description($post['group-id']);
                if ($susccess) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while deleting group description question.',
                ];

                return $result;
            case ACTION_ADD_GROUP:
                $success = $this->groupmanager->add_group($this->amcquiz->id);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }

                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while adding group.',
                ];

                return $result;
            case ACTION_DELETE_GROUP:
                $success = $this->groupmanager->delete_group($this->amcquiz->id, $post['group-id']);
                if ($success) {
                    $success = $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while deleting group.',
                ];

                return $result;
            case ACTION_DELETE_QUESTION:
                $success = $this->questionmanager->delete_group_question($post['question-id']);
                if ($success) {
                    $this->amcquizmanager->set_timemodified($this->amcquiz->id);
                }
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while deleting question.',
                ];

                return $result;
            case ACTION_STUDENT_ACCESS:
                $success = $this->amcquizmanager->set_student_access($this->amcquiz->id, $post['studentcorrectionaccess'], $post['studentaanotatedaccess']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while setting student access.',
                ];

                return $result;
            case ACTION_SEND_NOTIFICATION:
                $success = $this->amcquizmanager->send_student_notification($this->amcquiz->id, $post['cmid']);
                $result = [
                  'status' => $success ? 200 : 400,
                  'message' => $success ? '' : 'error while sending student notification.',
                ];

                return $result;
            case ACTION_EXPORT_QUIZ:
                if ($this->amcquiz->uselatexfile) {
                    $result = $this->curlmanager->generate_documents($this->amcquiz);
                } else {
                    $result = $this->amcquizmanager->amcquiz_export($this->amcquiz->id);

                    if (!$result || (isset($result['errors']) && count($result['errors']) > 0)) {
                        return [
                        'status' => 400,
                        'message' => 'error while exporting amcquiz.',
                      ];
                    }

                    // send zip to API
                    $result = $this->curlmanager->send_zipped_amcquiz($this->amcquiz, $result['zipfile']);

                    if (!$result || (isset($result['status']) && 200 !== $result['status'])) {
                        return [
                        'status' => 400,
                        'message' => 'error while sending amcquiz zip file.',
                      ];
                    }
                }

                $this->amcquizmanager->set_documents_created($this->amcquiz->id);

                return $result;

            case ACTION_DELETE_UNRECOGNIZED_SHEETS:
                $result = $this->curlmanager->delete_unrecognized_sheets($this->amcquiz);
                // @TODO depends on what API will return...
                return [
                    'status' => $result['status'],
                    'message' => 'Unrecognized sheets deleted.',
                ];
            case ACTION_UPLOAD_SHEETS:
                if (!isset($files['sheets']) || null === $files['sheets'] || empty($files['sheets'])) {
                    return [
                      'status' => 400,
                      'message' => 'no file selected.',
                    ];
                }
                global $CFG;

                $uploaddir = $CFG->dataroot.'/temp/amcquiz/';
                $uploadfile = $uploaddir.basename($files['sheets']['name']);

                $moved = move_uploaded_file($files['sheets']['tmp_name'], $uploadfile);
                if (!$moved) {
                    return [
                    'status' => 400,
                    'message' => 'unable to move file.',
                  ];
                }

                $encoded = base64_encode(file_get_contents($uploadfile));
                $result = $this->curlmanager->upload_sheets($this->amcquiz, $encoded);
                $this->amcquizmanager->set_sheets_uploaded_time($this->amcquiz, time());
                unlink($uploadfile);

                return $result;
            case ACTION_DELETE_ALL_SHEETS:
                $result = $this->curlmanager->delete_all_sheets($this->amcquiz);
                $this->amcquizmanager->set_sheets_uploaded_time($this->amcquiz);

                return $result;
            case ACTION_LAUNCH_ASSOCIATION:

                $result = $this->curlmanager->launch_association($this->amcquiz);
                $this->amcquizmanager->set_association_time($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'hurray!',
                ];
            case ACTION_ASSOCIATE_MANUALLY:

                $filecode = $post['filecode'];
                $idnumber = $post['idnumber'];
                $result = $this->curlmanager->associate_sheet_manually($this->amcquiz, $filecode, $idnumber);

                return [
                  'status' => 200,
                  'message' => 'hurray!',
                ];
            case ACTION_LAUNCH_GRADING:
                $result = $this->curlmanager->launch_grade($this->amcquiz);
                $this->amcquizmanager->set_grading_time($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'hurray!',
                ];
            case ACTION_ANNOTATE_SHEETS:
                $result = $this->curlmanager->annotate($this->amcquiz);
                $this->amcquizmanager->set_annotated_at($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'hurray!',
                ];
            case ACTION_GET_SUBJECT_PDF:
                $result = $this->curlmanager->get_subject_pdf($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'PDF SUBJECT!',
                ];
            case ACTION_GET_CATALOG_PDF:
                $result = $this->curlmanager->get_catalog_pdf($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'PDF CATALOG!',
                ];
            case ACTION_GET_CORRECTION_PDF:
                $result = $this->curlmanager->get_correction_pdf($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'PDF CORRECTION!',
                ];
            case ACTION_GET_DOCUMENTS_ZIP:
                $result = $this->curlmanager->get_documents_zip($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'DOCUMENTS ZIP!',
                ];
            case ACTION_GET_GRADE_CSV:
                $result = $this->curlmanager->get_grade_csv($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'ACTION_GET_GRADE_CSV',
                ];
            case ACTION_GET_GRADE_ODS:
                $result = $this->curlmanager->get_grade_ods($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'ACTION_GET_GRADE_ODS!',
                ];
            case ACTION_GET_GRADE_APOGEE:
                $result = $this->curlmanager->get_grade_apogee($this->amcquiz);

                return [
                  'status' => 200,
                  'message' => 'ACTION_GET_GRADE_APOGEE',
                ];
        }
    }
}
