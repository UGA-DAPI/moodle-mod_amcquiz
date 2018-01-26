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
     * Quiz manager
     * @var \mod_amcquiz\managers\amcquizmanager
     */
    public $amcquizmanager;

    /**
     * Course module
     *
     * @var StdClass
     */
    public $cm;

    /**
     * Course
     *
     * @var stdClass
     */
    public $course;

    /**
     * current view
     * @var String
     */
    public $current_view;

    /**
     * current action
     * @var String
     */
    public $current_action;

    public function __construct() {
        $this->amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    }

    /**
     * Parse the get parameters and set some data
     *
     * @global moodle_database $DB
     */
    public function parse_request() {
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

}
