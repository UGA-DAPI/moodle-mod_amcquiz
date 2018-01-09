<?php

namespace mod_amcquiz\local\entity;

class amcquiz
{

    const FORMATS = [
      'PLAIN' => 2,
      'HTML' => 1
    ];

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
    public $uselatexfile = false;

    /**
     * [public description]
     * @var string
     */
    public $latexfile;

    /**
     * [public description]
     * @var string
     */
    public $instructionstop;

    /**
     * [public description]
     * @var integer
     */
    public $instructionstopformat = FORMATS['PLAIN'];


    //public $comment;

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
     * [public description]
     * @var boolean
     */
    public $locked = false;

    /**
     * [public description]
     * @var integer
     */
    public $timecreated;

    /**
     * [public description]
     * @var integer
     */
    public $timemodified;


    public $groups;


}
