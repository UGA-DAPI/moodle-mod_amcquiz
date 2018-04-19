<?php

namespace mod_amcquiz;

defined('MOODLE_INTERNAL') || die();

class shared_service
{
    /**
     * @var StdClass
     */
    public $amcquiz;

    /**
     * Quiz manager.
     *
     * @var \mod_amcquiz\managers\amcquizmanager
     */
    public $amcquizmanager;

    /**
     * Course module.
     *
     * @var StdClass
     */
    public $cm;

    /**
     * Course.
     *
     * @var stdClass
     */
    public $course;

    /**
     * current view.
     *
     * @var string
     */
    public $current_view;

    /**
     * current action.
     *
     * @var string
     */
    public $current_action;

    public function __construct()
    {
        $this->amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    }

    /**
     * Parse the get parameters and set some data.
     *
     * @global moodle_database $DB
     */
    public function parse_request()
    {
        global $DB;

        $id = required_param('id', PARAM_INT); // course_module ID
        $this->current_view = optional_param('current', 'questions', PARAM_ALPHA);
        $this->current_action = optional_param('action', '', PARAM_ALPHA);

        if ($id) {
            $this->cm = \get_coursemodule_from_id('amcquiz', $id, 0, false, MUST_EXIST);
            $this->course = $DB->get_record('course', array('id' => $this->cm->course), '*', MUST_EXIST);
            $this->amcquiz = $this->amcquizmanager->get_full_amcquiz_record($this->cm->instance, $this->cm->id);
        } else {
            print_error('You must specify a course_module ID');
        }
    }

    /**
     * Let user know if documents should be updated
     * True if any update (question added / deleted - question score updated - group added / deleted ...) occured after documents generation.
     *
     * @param stdClass $context
     *
     * @return bool
     */
    public function should_update_documents(\stdClass $amcquiz)
    {
        // compares to timestamps
        if ($amcquiz->documents_created_at) {
            return  $amcquiz->documents_created_at < $amcquiz->timemodified;
        }

        return false;
    }

    /**
     * Get disabled tabs based on amcquiz state.
     *
     * @param stdClass $amcquiz
     *
     * @return array array of disabled tabs
     */
    public function get_disabled_tabs(\stdClass $amcquiz)
    {
        $disabled = [];

        if (!$amcquiz->documents_created_at) {
            $disabled = ['sheets', 'associate', 'grade', 'correction'];
        } elseif (!$amcquiz->sheets_uploaded_at) {
            $disabled = ['associate', 'grade', 'correction'];
        } elseif (!$amcquiz->associated_at) {
            $disabled = ['grade', 'correction'];
        } elseif (!$amcquiz->graded_at) {
            $disabled[] = 'correction';
        }

        if ($amcquiz->uselatexfile) {
            $disabled[] = 'questions';
        }

        return $disabled;
    }

    /**
     * Check if the tab set in url is reachable and if not set a default view.
     *
     * @param string $current  current asked view
     * @param array  $disabled disabled tabs
     *
     * @return string the view to display
     */
    public function check_current_tab(string $current, array $disabled)
    {
        $valid_tabs = [
            'questions',
            'documents',
            'sheets',
            'associate',
            'grade',
            'correction',
        ];

        // check current asked tab is not invalid
        if (in_array($current, $disabled) || !in_array($current, $valid_tabs)) {
            return $this->amcquiz->uselatexfile ? 'documents' : 'questions';
        }

        return $current;
    }
}
