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
        // get url parameters
        $mode = optional_param('mode', 'all', PARAM_ALPHA);
        $usermode = optional_param('usermode', 'without', PARAM_ALPHA);
        $idnumber = optional_param('idnumber', '', PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 20, PARAM_INT);

        $cm = $this->data['cm'];

        // filter data
        $auto = array_filter($this->amcquiz->associations['data'], function ($data) {
            return 'auto' === $data['type'];
        });

        $manual = array_filter($this->amcquiz->associations['data'], function ($data) {
            return 'manual' === $data['type'];
        });

        $none = array_filter($this->amcquiz->associations['data'], function ($data) {
            return 'none' === $data['type'];
        });

        $display = $this->amcquiz->associations['data'];
        if ('unknown' === $mode) {
            $display = $none;
        } elseif ('manual' === $mode) {
            $display = $manual;
        } elseif ('auto' === $mode) {
            $display = $auto;
        }
        $total = count($display);

        // slice data to display depending on pagers data
        $display = array_slice($display, $page * $perpage, $perpage);

        $showpager = $total > $perpage;
        $pagingbar = new \paging_bar(
            $total,
            $page,
            $perpage,
            new \moodle_url('view.php', ['id' => $cm->id, 'current' => 'associate'])
        );

        // users to exclude from dropdown
        $excludeusers = [];
        if ('without' === $usermode) {
            $excludeusers = array_map(function ($data) {
                return $data['idnumber'];
            }, array_merge($manual, $auto));
        }

        // build data
        $usersdata = array_map(function ($data) use ($cm, $excludeusers) {
            $filename = pathinfo($data['url'])['filename'];
            $filenamearray = explode('-', $filename);
            $filecode = $filenamearray[1].'_'.$filenamearray[2];
            $groupid = '';

            return [
              'url' => $data['url'],
              'filecode' => $filecode,
              'students' => amcquiz_get_users_for_select_element($cm, $data['idnumber'], $groupid, $excludeusers),
            ];
        }, $display);

        $content = [
          'amcquiz' => $this->amcquiz,
          'associationmodes' => $this->get_association_modes($mode),
          'usermodes' => $this->get_user_modes($usermode),
          'usersdata' => $usersdata,
          'cm' => $cm,
          'nbauto' => count($auto),
          'nbmanual' => count($manual),
          'nbunknown' => count($none),
          'pager' => $output->render($pagingbar),
          'showpager' => $showpager,
        ];

        return $content;
    }

    public function get_association_modes($current)
    {
        return [
            ['value' => 'unknown', 'label' => get_string('unknown', 'mod_amcquiz'), 'selected' => 'unknown' === $current],
            ['value' => 'manual', 'label' => get_string('manual', 'mod_amcquiz'), 'selected' => 'manual' === $current],
            ['value' => 'auto', 'label' => get_string('auto', 'mod_amcquiz'), 'selected' => 'auto' === $current],
            ['value' => 'all', 'label' => get_string('all'), 'selected' => 'all' === $current],
        ];
    }

    public function get_user_modes($current)
    {
        return [
            ['value' => 'without', 'label' => get_string('without', 'mod_amcquiz'), 'selected' => 'without' === $current],
            ['value' => 'all', 'label' => get_string('all'), 'selected' => 'all' === $current],
        ];
    }
}
