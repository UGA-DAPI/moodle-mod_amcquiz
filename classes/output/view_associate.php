<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_associate implements \renderable, \templatable
{

    /**
     * The auto multiple choice questionnaire.
     *
     * @var stdClass
     */
    protected $amcquiz;

    /**
     *
     * @var array a set of usefull data
     */
    protected $data;

    /**
     * Construct
     *
     * @param stdClass $amcquiz A questionnaire
     * @param array $data A set of usefull data
     */
    public function __construct(\stdClass $amcquiz, array $data)
    {
        $this->amcquiz = $amcquiz;
        $this->data = $data;
    }

    /**
     * Prepare data for template
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        global $CFG;
        $content = [
          'amcquiz' => $this->amcquiz,
          'associationmodes' => $this->get_association_modes(),
          'usermodes' => $this->get_user_modes(),
          'usersdata' => [],
          'students' => []
        ];
        return $content;
    }


    public function get_association_modes()
    {
        return [
            ['value' => 'unknown', 'label' => get_string('unknown', 'mod_amcquiz'), 'selected' => false],
            ['value' => 'manual', 'label' => get_string('manual', 'mod_amcquiz'), 'selected' => false],
            ['value' => 'auto', 'label' => get_string('auto', 'mod_amcquiz'), 'selected' => false],
            ['value' => 'all', 'label' => get_string('all'), 'selected' => true]
        ];
    }

    public function get_user_modes()
    {
        return [
            ['value' => 'without', 'label' => get_string('without', 'mod_amcquiz'), 'selected' => false],
            ['value' => 'all', 'label' => get_string('all'), 'selected' => true]
        ];
    }
}
