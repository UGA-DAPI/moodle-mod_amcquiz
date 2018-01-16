<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_student implements \renderable, \templatable {
    /**
     * The auto multiple choice questionnaire.
     *
     * @var \stdClass
     */
    protected $amcquiz;

    /**
     * Moodle User.
     *
     * @var moodle_user
     */
    protected $user;

    /**
     * Contruct
     *
     * @param \stdClass $amcquiz An AMC quiz
     * @param moodle_user $quiz A quiz
     */
    public function __construct($amcquiz, $user) {
        $this->user = $user;
        $this->amcquiz = $amcquiz;
    }
    /**
     * Prepare data for use in a template
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $anotatedfile = '';
        $content = ['title' => $this->amcquiz->name];
        if ($this->amcquiz->studentaccess && $anotatedfile) {
            $content['corrected'] = '#'; //$this->process->getFileActionUrl($anotatedfile);
            if ($this->amcquiz->corrigeaccess) {
                $corrige = 'Link to corrected file'; //$this->process->normalizeFilename('corrige');
                $content['correction'] = '#';//$this->process->getFileActionUrl($corrige);
            }
        }
        return $content;
    }
}
