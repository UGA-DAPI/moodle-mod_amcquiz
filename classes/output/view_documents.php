<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_documents implements \renderable, \templatable
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
        $content = [
          'amcquiz' => $this->amcquiz,
          'cmid' => $this->data['cmid'],
          'settingsurl' => $CFG->wwwroot.'/course/modedit.php?update='.$this->data['cmid'].'&return=1',
        ];

        return $content;
    }
}
