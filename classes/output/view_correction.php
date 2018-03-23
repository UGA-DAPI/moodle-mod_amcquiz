<?php

namespace mod_amcquiz\output;

defined('MOODLE_INTERNAL') || die();

class view_correction implements \renderable, \templatable
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
        $currentgroup = optional_param('group', '0', PARAM_INT);
        $idnumber = optional_param('idnumber', '0', PARAM_INT);
        $details = optional_param('details', '0', PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 20, PARAM_INT);

        $showdetails = 0 !== (int) $details;
        $corrections = $this->amcquiz->corrections['data'];
        // map data
        $known = array_values(array_filter($corrections, function ($data) {
            return 'auto' === $data['type'] || 'manual' === $data['type'];
        }));

        $unknown = array_values(array_filter($corrections, function ($data) {
            return 'none' === $data['type'];
        }));

        $cm = $this->data['cm'];
        // Get groups used in module.
        $groupmode = groups_get_activity_groupmode($cm);

        $context = \context_module::instance($cm->id);
        $isseparategroups = (SEPARATEGROUPS === $cm->groupmode && !has_capability('moodle/site:accessallgroups', $context));

        $noenrol = 0 === $this->has_students($context);
        // if noenrol we should display name captions else we should display all students enroled in this quiz depending on selected group
        $list = [];
        if ($noenrol) {
            $list = $corrections;
        } elseif (0 === (int) $currentgroup) {
            $list = array_values(amcquiz_get_student_users($cm, true));
        } else {
            $list = array_values(amcquiz_get_student_users($cm, true, $currentgroup));
        }
        $total = count($list);
        $showpager = $total > $perpage;
        // slice data to display depending on pagers data
        $list = array_slice($list, $page * $perpage, $perpage);

        $allowedgroups = groups_get_activity_allowed_groups($cm);
        $groups = array_map(function ($group) use ($currentgroup) {
            return [
              'value' => $group->id,
              'label' => $group->name,
              'selected' => (int) $currentgroup === (int) $group->id,
            ];
        }, $allowedgroups);

        $pagingbar = new \paging_bar(
            $total,
            $page,
            $perpage,
            new \moodle_url('view.php', ['id' => $cm->id, 'current' => 'correction'])
        );

        $detailsdata = [];
        if ($showdetails) {
            // if $noenrol
            if ($noenrol) {
                $detailsdata = array_map(function ($caption) {
                    $caption['isknown'] = false;

                    return $caption;
                }, $unknown);
            } else {
                // search for correction in list (a name caption or a pdf)
                $index = array_search($idnumber, array_column($known, 'idnumber'));
                // if not found show a list of unknown sheets
                if (false === $index || null === $index) {
                    $detailsdata = array_map(function ($caption) {
                        $caption['isknown'] = false;

                        return $caption;
                    }, $unknown);
                } else {
                    $detailsdata = $known[$index];
                    $detailsdata['isknown'] = 'none' !== $detailsdata['type'];
                }
            }
        }

        $content = [
          'amcquiz' => $this->amcquiz,
          'cm' => $cm,
          'noenrol' => $noenrol,
          'currentgroup' => $currentgroup,
          'currentidnumber' => $idnumber,
          'groups' => array_values($groups),
          'students' => $noenrol ? [] : amcquiz_get_users_for_select_element($cm, $idnumber),
          'groupmode' => $isseparategroups ? get_string('groupsseparate', 'core') : get_string('groupsvisible', 'core'),
          'list' => $list,
          'showdetails' => $showdetails,
          'detailsdata' => $detailsdata,
          'pager' => $output->render($pagingbar),
          'showpager' => $showpager,
        ];

        return $content;
    }

    private function has_students($context)
    {
        global $DB;
        list($relatedctxsql, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');
        $countsql = "SELECT COUNT(DISTINCT(ra.userid))
            FROM {role_assignments} ra
            JOIN {user} u ON u.id = ra.userid
            WHERE ra.contextid  $relatedctxsql AND ra.roleid = 5";
        $totalcount = $DB->count_records_sql($countsql, $params);

        return $totalcount;
    }

    private function get_code($filename)
    {
        preg_match('/name-(?P<student>[0-9]+)[:-](?P<copy>[0-9]+).jpg$/', $filename, $res);

        return $res['student'].'_'.$res['copy'];
    }
}
