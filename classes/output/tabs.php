<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class tabs implements \renderable, \templatable
{
    /**
     * The auto multiple choice quiz.
     *
     * @var StdClass
     */
    protected $amcquiz;
    /**
     * Moodle course module.
     *
     * @var moodle_cm
     */
    protected $cm;
    /**
     * Selected tab.
     *
     * @var string
     */
    protected $selected;

    /**
     * Contruct.
     */
    public function __construct($amcquiz, $cm, $selected)
    {
        $this->amcquiz = $amcquiz;
        $this->cm = $cm;
        $this->selected = $selected;
    }

    /**
     * Prepare data for use in a template.
     *
     * @param \renderer_base $output
     *
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        global $CFG;
        $service = new \mod_amcquiz\shared_service();
        $disabled_tabs = $service->get_disabled_tabs($this->amcquiz);
        $tabs = [];

        $questions = [
            'active' => 'questions' === $this->selected,
            'inactive' => in_array('questions', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=questions"),
            ],
            'title' => get_string('questions', 'question'),
            'text' => '1. '.get_string('questions', 'question'),
        ];

        $documents = [
            'active' => 'documents' === $this->selected,
            'inactive' => in_array('documents', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=documents"),
            ],
            'title' => get_string('tab_documents', 'mod_amcquiz'),
            'text' => '2. '.get_string('tab_documents', 'mod_amcquiz'),
        ];

        $sheets = [
            'active' => 'sheets' === $this->selected,
            'inactive' => in_array('sheets', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=sheets"),
            ],
            'title' => get_string('tab_sheets', 'mod_amcquiz'),
            'text' => '3. '.get_string('tab_sheets', 'mod_amcquiz'),
        ];

        $associate = [
            'active' => 'associate' === $this->selected,
            'inactive' => in_array('associate', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=associate"),
            ],
            'title' => get_string('tab_associate', 'mod_amcquiz'),
            'text' => '4. '.get_string('tab_associate', 'mod_amcquiz'),
        ];

        $grade = [
            'active' => 'grade' === $this->selected,
            'inactive' => in_array('grade', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=grade"),
            ],
            'title' => get_string('tab_grade', 'mod_amcquiz'),
            'text' => '5. '.get_string('tab_grade', 'mod_amcquiz'),
        ];

        $correction = [
            'active' => 'correction' === $this->selected,
            'inactive' => in_array('correction', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=correction"),
            ],
            'title' => get_string('tab_correction', 'mod_amcquiz'),
            'text' => '6. '.get_string('tab_correction', 'mod_amcquiz'),
        ];

        array_push(
            $tabs,
            $questions,
            $documents,
            $sheets,
            $associate,
            $grade,
            $correction
        );

        return $tabs;
    }
}
