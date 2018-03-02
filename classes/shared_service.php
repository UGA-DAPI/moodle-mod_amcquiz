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
            $this->amcquiz = $this->amcquizmanager->get_amcquiz_record($this->cm->instance, $this->cm->id);
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
            return  $amcquiz->documents_created_at > $amcquiz->timemodified;
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
        if ($amcquiz->uselatexfile || $amcquiz->locked) {
            $disabled[] = 'questions';
        }

        if ($amcquiz->locked) {
            $disabled[] = 'documents';
        } else {
            array_push($disabled, 'sheets', 'associate', 'grade', 'correction');
        }

        return $disabled;
    }

    /**
     * Check if the tab set in url is reachable and if not set a default view.
     *
     * @param bool   $locked
     * @param string $current
     * @param array  $disabled
     *
     * @return string the view to display
     */
    public function check_current_tab(bool $locked, string $current, array $disabled)
    {
        // here we have a problem
        if (in_array($current, $disabled)) {
            // should return a tab based on the current status of the amcquiz
            if ($amcquiz->uselatexfile) {
                return $locked ? 'sheets' : 'documents';
            }

            return $locked ? 'sheets' : 'questions';
        }

        return $current;
    }
}
