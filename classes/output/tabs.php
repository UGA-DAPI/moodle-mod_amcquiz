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
     * Moodle context.
     *
     * @var moodle_context
     */
    protected $context;
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
     * Contruct
     *
     */
    public function __construct($amcquiz, $context, $cm, $selected)
    {
        $this->amcquiz = $amcquiz;
        $this->context = $context;
        $this->cm = $cm;
        $this->selected = $selected;
    }
    /**
     * Prepare data for use in a template
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        global $CFG;
        $disabled_tabs = $this->get_disabled_tabs($this->amcquiz, $this->context, $this->selected);
        $tabs = [];

        $questions = [
            'active' => $this->selected === 'questions',
            'inactive' => in_array('questions', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=questions")
            ],
            'title' => get_string('questions', 'question'),
            'text' => '1. ' . get_string('questions', 'question'),
        ];

        $subjects = [
            'active' => $this->selected === 'subjects',
            'inactive' => in_array('subjects', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=subjects"),
            ],
            'title' => get_string('tab_subjects', 'mod_amcquiz'),
            'text' => '2. ' . get_string('tab_subjects', 'mod_amcquiz'),
        ];

        $sheets = [
            'active' => $this->selected === 'sheets',
            'inactive' => in_array('sheets', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=sheets"),
            ],
            'title' => get_string('tab_sheets', 'mod_amcquiz'),
            'text' => '3. ' . get_string('tab_sheets', 'mod_amcquiz'),
        ];

        $associate = [
            'active' => $this->selected === 'associate',
            'inactive' => in_array('associate', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=associate"),
            ],
            'title' => get_string('tab_associate', 'mod_amcquiz'),
            'text' => '4. ' . get_string('tab_associate', 'mod_amcquiz'),
        ];

        $annotate = [
            'active' => $this->selected === 'grade',
            'inactive' => in_array('grade', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=grade"),
            ],
            'title' => get_string('tab_grade', 'mod_amcquiz'),
            'text' => '5. ' . get_string('tab_grade', 'mod_amcquiz'),
        ];

        $correction = [
            'active' => $this->selected === 'correction',
            'inactive' => in_array('correction', $disabled_tabs),
            'link' => [
                'link' => new \moodle_url("{$CFG->wwwroot}/mod/amcquiz/view.php?id={$this->cm->id}&current=correction"),
            ],
            'title' => get_string('tab_correction', 'mod_amcquiz'),
            'text' => '6. ' . get_string('tab_correction', 'mod_amcquiz'),
        ];


        array_push(
            $tabs,
            $questions,
            $subjects,
            $sheets,
            $associate,
            $annotate,
            $correction
        );

        return $tabs;
    }

    public function get_disabled_tabs($amcquiz, $context, $selected)
    {
        $disabled = [];
        /*if (!$amcquiz->uselatexfile && empty($amcquiz->questions)) {
            $disabled = array('subjects', 'sheets', 'associate', 'annotate', 'correction');
        } else if ($quiz->uselatexfile) {
            $disabled = array('questions');
        } else if (!empty($quiz->errors) || !$quiz->isLocked()) {
            $disabled = array('uploadscans', 'associating', 'grading', 'annotating');
        } else if (!$quiz->hasScans()) {
            $disabled = array('associating', 'grading', 'annotating');
        }
        if ($quiz->isLocked()) {
            $disabled[] = 'questions';
        }
        if (has_students($context) === 0) {
            $disabled = array('associating');
        }*/

        return $disabled;
    }
}
