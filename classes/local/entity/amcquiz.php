<?php

namespace mod_amcquiz\local\entity;

class amcquiz
{
    /**
     * [public description]
     * @var integer
     */
    public $id;

    /**
     * [public description]
     * @var integer
     */
    public $course_id;

    /**
     * [public description]
     * @var string
     */
    public $name;

    /**
     * [public description]
     * @var boolean
     */
    public $uselatexfile;

    /**
     * [public description]
     * @var string
     */
    public $latexfile;

    /**
     * [public description]
     * @var integer
     */
    public $author_id;

    /**
     * Allow each student to access the whole correction (answers annotated and solutions)
     * @var boolean
     */
    public $studentcorrectionaccess;

    /**
     * Allow each student to access its annotated answers
     * @var boolean
     */
    public $studentannotatedaccess;

    /**
     * If the questionnaire is locked ...
     * @var boolean
     */
    public $locked;

    /**
     * [public description]
     * @var boolean
     */
    public $anonymous;

    /**
     * Timestamp
     * @var integer
     */
    public $timecreated;

    /**
     * Timestamp
     * @var integer
     */
    public $timemodified;

}
