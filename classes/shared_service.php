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
    public function should_update_documents(\stdClass $context)
    {
        $documents_created_event = \mod_amcquiz\event\documents_created::create([
            'context' => $context,
        ]);

        if ($documents_created_event) {
            $documents_created_event_data = $documents_created_event->get_data();
            $amcquiz_updated_event = \mod_amcquiz\event\amcquiz_updated::create([
                'context' => $context,
            ]);
            $amcquiz_updated_event_data = $amcquiz_updated_event->get_data();

            return $amcquiz_updated_event_data && ($amcquiz_updated_event_data['timecreated'] > $documents_created_event_data['timecreated']);
        }

        return false;
    }

    /**
     * Get disabled tabs based on settings on existing events.
     *
     * @param stdClass $amcquiz
     * @param bool     $locked
     * @param stdClass $context
     *
     * @return array array of disabled tabs
     */
    public function get_disabled_tabs(\stdClass $amcquiz, bool $locked, \stdClass $context)
    {
        $disabled = [];
        if ($amcquiz->uselatexfile || $locked) {
            $disabled[] = 'questions';
        }

        if ($locked) {
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

    /**
     * True if a documents_created event exist.
     *
     * @param stdClass $context
     *
     * @return bool
     */
    private function amcquiz_has_documents(\stdClass $context)
    {
        $documents_created_event = \mod_amcquiz\event\documents_created::create([
            'context' => $context,
        ]);

        return $documents_created_event;
    }

    /**
     * True if a sheets_created event exist and has been created after any sheets_deleted event.
     *
     * @param stdClass $context
     *
     * @return bool
     */
    private function amcquiz_has_sheets(\stdClass $context)
    {
        $sheets_created_event = \mod_amcquiz\event\sheets_created::create([
            'context' => $context,
        ]);

        $sheets_deleted_event = \mod_amcquiz\event\sheets_deleted::create([
            'context' => $context,
        ]);

        if ($sheets_deleted_event) {
            $sheets_created_event_data = $sheets_created_event->get_data();
            $sheets_deleted_event_data = $sheets_deleted_event->get_data();

            return $sheets_created_event_data['timecrated'] > $sheets_deleted_event_data['timecrated'];
        }

        return $sheets_created_event;
    }

    /**
     * Tells if the quiz is locked.
     *
     * @param stdClass $context
     *
     * @return bool
     */
    public function amcquiz_is_locked(\stdClass $context)
    {
        $quiz_locked_event = \mod_amcquiz\event\quiz_locked::create([
            'context' => $context,
        ]);

        $quiz_unlocked_event = \mod_amcquiz\event\quiz_unlocked::create([
            'context' => $context,
        ]);

        if ($quiz_unlocked_event) {
            $quiz_locked_event_data = $quiz_locked_event->get_data();
            $quiz_unlocked_event_data = $quiz_unlocked_event->get_data();

            return $quiz_locked_event_data['timecrated'] > $quiz_unlocked_event_data['timecrated'];
        }

        return $quiz_locked_event;
    }
}
