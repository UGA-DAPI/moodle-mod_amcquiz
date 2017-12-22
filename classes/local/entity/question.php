<?php

namespace mod_amcquiz\entity;

class question
{
    /**
     * [public description]
     * @var integer
     */
    public $id;

    /**
     * Group quiz id
     * @var integer
     */
    public $amcquiz_id;

    /**
     * Group name
     * @var string
     */
    public $name;

    /**
     * Question position / order
     * @var integer
     */
    public $position;

    /**
     * A group can contains one and only one question of type description
     * @var integer
     */
    public $description_question_id;

    public function __construct($quiz_id)
    {
        $this->amcquiz_id = $quiz_id;
    }

}
