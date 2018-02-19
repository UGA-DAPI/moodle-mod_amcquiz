<?php


class mod_amcquiz_renderer extends \plugin_renderer_base
{
    /**
     * @var stdClass
     */
    public $amcquiz;

    /**
     * @var stdClass
     */
    public $cm;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target)
    {
        $page->requires->jquery();
        $page->requires->jquery_plugin('ui-css');
        $page->requires->jquery_plugin('bootstrap');
        $page->requires->jquery_plugin('bootstrap-css');
        $page->requires->css(
            new moodle_url('/mod/amcquiz/style/styles.css')
        );

        parent::__construct($page, $target);
    }

    public function render_header($amcquiz, $viewcontext)
    {
        $activityname = format_string($amcquiz->name, true, $amcquiz->course_id);
        $title = $this->page->course->shortname . " — " . $activityname;

        //$context = context_module::instance($cm->id);
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);
        $this->page->set_context($viewcontext);

        $output = $this->output->header();

        $output .= $this->output->heading($activityname);

        return $output;
    }

    public function render_tabs(\templatable $page)
    {
        $data['tabs'] = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/tabs', $data);
    }

    public function render_student_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/studentview', $data);
    }

    public function render_questions_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/questions', $data);
    }

    public function render_documents_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/documents', $data);
    }

    public function render_sheets_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/sheets', $data);
    }

    public function render_associate_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/associate', $data);
    }

    public function render_annotate_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/annotate', $data);
    }

    public function render_correction_view(\templatable $page)
    {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_amcquiz/correction', $data);
    }
}
