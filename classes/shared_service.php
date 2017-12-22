<?php

namespace mod_amcquiz;

defined('MOODLE_INTERNAL') || die();

class shared_service
{
    /**
     * @var \mod_amcquiz\entity\amcquiz
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
     * @var StdClass
     */
    public $course;

    public function __construct() {
        $this->amcquizmanager = new \mod_amcquiz\managers\amcquizmanager();
    }

    /**
     * Parse the parameters "a" and "id".
     *
     * @global moodle_database $DB
     */
    public function parseRequest() {
        global $DB;
        $id = required_param('id', 0, PARAM_INT); // course_module ID

        // pourquoi l'un et l'autre ? suivre la règle de moodle (ce que moodle utilise pour rediriger sur la page du plugin)
        // IE $id
        if ($id) {
            $this->cm = \get_coursemodule_from_id('amcquiz', $id, 0, false, MUST_EXIST);
            $this->course = $DB->get_record('course', array('id' => $this->cm->course), '*', MUST_EXIST);
            $amcquizrecord = $this->amcquizmanager->get_amcquiz_record($this->cm->instance);
            $this->amcquiz = $this->amcquizmanager->build_from_record($amcquizrecord); //\mod_amcquiz\local\models\amcquiz::buildFromRecord($amcquizrecord);
        } else {
            print_error('You must specify a course_module ID');
        }
    }
}
