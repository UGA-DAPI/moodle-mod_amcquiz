<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_grade implements \renderable, \templatable
{
    /**
     * The auto multiple choice questionnaire.
     *
     * @var stdClass
     */
    protected $amcquiz;

    /**
     * @var array a set of usefull data
     */
    protected $data;

    /**
     * Construct.
     *
     * @param stdClass $amcquiz A questionnaire
     * @param array    $data    A set of usefull data
     */
    public function __construct(\stdClass $amcquiz, array $data)
    {
        $this->amcquiz = $amcquiz;
        $this->data = $data;
    }

    /**
     * Prepare data for template.
     *
     * @param \renderer_base $output
     *
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        global $CFG;

        $grades = $this->amcquiz->grades;
        $stats = $grades['stats']['data'];
        $files = $grades['files']['data'];

        $content = [
          'amcquiz' => $this->amcquiz,
          'stats' => $stats,
          'files' => $files,
          'cm' => $this->data['cm'],
        ];

        return $content;
    }
}
