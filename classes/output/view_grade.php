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
        $cm = $this->data['cm'];
        // Groups that are being used in module.
        $groupmode    = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);

        $context = \context_module::instance($cm->id);
        $isseparategroups = ($cm->groupmode === SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context));

        $noenrol = $this->has_students($context) === 0;
        if (!$noenrol) {
            //$users = amc_get_student_users($cm, true, $group);
            //$associateprocess->get_association();
            //$userscopy = array_flip(array_merge($associateprocess->copymanual, $associateprocess->copyauto));
        }

        $groups = array_map(function ($group) {
            return [
              'value' => $group->id,
              'label' => $group->name,
              'selected' => false
            ];
        }, groups_get_activity_allowed_groups($cm));

        $content = [
          'amcquiz' => $this->amcquiz,
          'cm' => $cm,
          'noenrol' => $noenrol,
          'groupmode' => $groupmode,
          'groups' => array_values($groups),
          'students' => $noenrol ? [] : amcquiz_get_users_for_select_element($cm, true),
          'groupmode' => $isseparategroups ? get_string('groupsseparate', 'core') : get_string('groupsvisible', 'core')
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
}
