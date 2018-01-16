<?php
/**
 * Internal library of functions for module amcquiz
 *
 * All the amcquiz specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_amcquiz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB,$CFG;
require_once dirname(dirname(__DIR__)) . '/config.php';
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once __DIR__ . '/lib.php';

define('AMC_QUESTIONS_TYPES', ['multichoice', 'truefalse']);
define('AMC_QUESTIONS_GROUP_TYPE', 'description');
define('AMC_TARGET_QUESTION', 'question');

defined('MOODLE_INTERNAL') || die();

/* @global $DB \moodle_database */
global $DB;

function amcquiz_list_cat_and_context_questions(string $catid, string $contextid, string $target, array $excludeids = []) {
    global $DB, $OUTPUT;
    $sql =  'SELECT q.id as id, q.name as name, q.qtype AS type, q.timemodified as qmodified ';
    $sql .= 'FROM {question} q JOIN {question_categories} qc ON q.category = qc.id ';
    $sql .= 'WHERE q.hidden = 0 ';
    if ($target === 'question') {
        // list multichoice truefalse questions
        $sql .= 'AND q.qtype IN ("' . implode('","', AMC_QUESTIONS_TYPES) . '") ';
    } else {
        // list description questions
        $sql .= 'AND q.qtype = "'. AMC_QUESTIONS_GROUP_TYPE .'"';
    }

    $sql .= 'AND qc.id = ' . $catid . ' ';
    $sql .= 'AND qc.contextid = ' . $contextid . ' ';
    // Also need to exclude questions already associated with the amcquiz instance
    if (count($excludeids) > 0) {
        $sql .= 'AND q.id NOT IN (' . implode(',', $excludeids) . ') ';
    }
    $sql .= 'ORDER BY qc.sortorder, q.name';

    $records = $DB->get_records_sql($sql);

    $questions = array_map(function ($q) use ($OUTPUT) {

        $qtype = \question_bank::get_qtype($q->type, false);
        $namestr = $qtype->local_name();
        // use renderer for icon... would have prefer to only get appropriate icon url
        $icon = $OUTPUT->image_icon('icon', $namestr, $qtype->plugin_name(), array('title' => $namestr));

        return [
            'id' => $q->id,
            'name' => $q->name,
            'icon' => $icon
        ];
    }, $records);

    return $questions;
}

// ici on a un souci si on veut filtrer les catégories de question concernées... ie pour les groupes on ne veut ajouter que des questions de type description
function amcquiz_list_categories_options($courseid, $cmid) {
    $contexts = [
        context_system::instance(),
        context_course::instance($courseid),
        context_module::instance($cmid),
        context_coursecat::instance($courseid)
    ];
    $result = question_category_options($contexts);
    return $result;
}


/**
 * Parses the config setting 'scoringrules' to convert it into an array.
 * It is used in mod_form.php
 * @return array
 */
function parse_scoring_rules() {
    $rawdata = get_config('mod_amcquiz', 'scoringrules');
    if (!$rawdata) {
        return array();
    }
    $splitted = preg_split('/\n-{3,}\s*\n/s', $rawdata, -1, PREG_SPLIT_NO_EMPTY);
    $instructions = [];
    foreach ($splitted as $split) {
        $lines = explode("\n", $split, 2);
        $title = trim($lines[0]);
        $details = trim($lines[1]);
        $instructions[] = $title;
    }
    return $instructions;
}

/**
 * Get available grade rounding strategies
 * @return array available grade rounding strategies
 */
function get_grade_rounding_strategies()
{
    return [
        'n' => get_string('grade_rounding_strategy_nearest', 'mod_amcquiz'),
        'i' => get_string('grade_rounding_strategy_lower', 'mod_amcquiz'),
        's' => get_string('grade_rounding_strategy_upper', 'mod_amcquiz')
    ];
}

/**
 * Return a user record.
 *
 * @todo Optimize? One query per user is doable, the difficulty is to sort results according to prefix order.
 *
 * @global \moodle_database $DB
 * @param string $idn
 * @return object Record from the user table.
 */
function getStudentByIdNumber($idn) {
    global $DB;
    $prefixestxt = get_config('mod_amcquiz', 'idnumberprefixes');
    $prefixes = array_filter(array_map('trim', preg_split('/\R/', $prefixestxt)));
    $prefixes[] = "";
    foreach ($prefixes as $p) {
        $user = $DB->get_record('user', array('idnumber' => $p . $idn, 'confirmed' => 1, 'deleted' => 0));
        if ($user) {
            return $user;
        }
    }
    return null;
}
/**
 * Return a user record.
 *
 *
 * @global \moodle_database $DB
 * @param context if
 * @return int count student user.
 */
function has_students($context) {
    global $DB;
    list($relatedctxsql, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');
    $countsql = "SELECT COUNT(DISTINCT(ra.userid))
        FROM {role_assignments} ra
        JOIN {user} u ON u.id = ra.userid
        WHERE ra.contextid  $relatedctxsql AND ra.roleid = 5";
    $totalcount = $DB->count_records_sql($countsql,$params);
    return $totalcount;

}


/**
 * Gets all the users assigned this role in this context or higher
 *
 * Note that moodle is based on capabilities and it is usually better
 * to check permissions than to check role ids as the capabilities
 * system is more flexible. If you really need, you can to use this
 * function but consider has_capability() as a possible substitute.
 *
 * The caller function is responsible for including all the
 * $sort fields in $fields param.
 *
 * If $roleid is an array or is empty (all roles) you need to set $fields
 * (and $sort by extension) params according to it, as the first field
 * returned by the database should be unique (ra.id is the best candidate).
 *
 * @param stdClass $cm mod_amcquiz instance
 * @param bool  $parent
 * @param string $group
 * @param bool $exclude
 * @return array
 */
function amc_get_student_users($cm, $parent = false, $group = '', $exclude = null) {
    global $DB;
    $codelength = get_config('mod_amcquiz', 'amccodelength');
    $allnames = get_all_user_name_fields(true, 'u');
    $fields = 'u.id, u.confirmed, u.username, '. $allnames . ', ' .'RIGHT(u.idnumber,'.$codelength.') as idnumber';
    $context = context_module::instance($cm->id);
    $roleid =array_keys( get_archetype_roles('student'));
    $parentcontexts = '';
    if ($parent) {
        $parentcontexts = substr($context->path, 1); // kill leading slash
        $parentcontexts = str_replace('/', ',', $parentcontexts);
        if ($parentcontexts !== '') {
            $parentcontexts = ' OR ra.contextid IN ('.$parentcontexts.' )';
        }
    }


     if ($roleid) {
        list($rids, $params) = $DB->get_in_or_equal($roleid, SQL_PARAMS_NAMED, 'r');
        $roleselect = "AND ra.roleid $rids";
    } else {
        $params = array();
        $roleselect = '';
    }
    if ($exclude) {
        list($idnumbers, $excludeparams) = $DB->get_in_or_equal($exclude, SQL_PARAMS_NAMED, 'excl', false);
        $idnumberselect = " AND RIGHT(u.idnumber,".$codelength.") $idnumbers ";
        $params = array_merge($params, $excludeparams);
    } else {
        $excludeparams = array();
        $idnumberselect = '';
    }

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0;
    }

    if ($group) {
        $groupjoin   = "JOIN {groups_members} gm ON gm.userid = u.id";
        $groupselect = " AND gm.groupid = :groupid ";
        $params['groupid'] = $group;
    } else {
        $groupjoin   = '';
        $groupselect = '';
    }

    $params['contextid'] = $context->id;
        list($sort, $sortparams) = users_order_by_sql('u');
        $params = array_merge($params, $sortparams);
        $ejoin = "JOIN {user_enrolments} ue ON ue.userid = u.id
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :ecourseid)";
        $params['ecourseid'] = $coursecontext->instanceid;

    $sql = "SELECT DISTINCT $fields, ra.roleid
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
               $idnumberselect
              JOIN {role} r ON ra.roleid = r.id
            $ejoin
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
        $groupjoin
             WHERE (ra.contextid = :contextid $parentcontexts)
                   $roleselect
                   $groupselect
          ORDER BY $sort";                  // join now so that we can just use fullname() later

    $availableusers = $DB->get_records_sql($sql, $params);
    $modinfo = get_fast_modinfo($cm->course);
    $info = new \core_availability\info_module($modinfo->get_cm($cm->id));
    $availableusers = $info->filter_user_list($availableusers);
    return $availableusers;
}

/**
 * Get course module users and return the result as an array usable in an HTML select element
 * @param  stdClass $cm       the course module (ie a amcquiz instance)
 * @param  string $idnumber a user id
 * @param  string $groupid  a group id
 * @param  Array $exclude  users to exclude
 * @return Array           an array usable in an HTML select element
 */
function amc_get_users_for_select_element($cm, $idnumber, $groupid, $exclude = null) {
    global $USER, $CFG;

    $codelength = get_config('mod_amcquiz', 'amccodelength');
    if (is_null($idnumber)) {
        $idnumber = $USER->idnumber;
    }
    if (count($idnumber)>$codelength) {
        $idnumber = substr($idnumber, -1*$codelength);//by security
    }

    if ($exclude && $idnumber) {
        $exclude = array_diff($exclude, array($idnumber));
    }
    $users = amc_get_student_users($cm, true, $groupid, $exclude);
    $label = get_string('selectuser', 'mod_amcquiz');
    $menu = [];
    foreach ($users as $user) {
        $userfullname = fullname($user);
        // In case of prefixed student number.
        $usernumber = substr($user->idnumber, -1*$codelength);
        $menu[] = [
          'value' => $user->idnumber,
          'label' => $userfullname,
          'selected' => intval($usernumber) === intval($idnumber)
        ];
    }

    return $menu;
}


function backup_source($file) {
    copy($file, $file.'.orig');
}

function restore_source($file) {
    copy($file, substr($file, -5));
}

function get_code($name) {
    preg_match('/name-(?P<student>[0-9]+)[:-](?P<copy>[0-9]+).jpg$/', $name, $res);
    return $res['student'].'_'.$res['copy'];
}

function get_list_row($list) {
    preg_match('/(?P<student>[0-9]+):(?P<copy>[0-9]+)\s*(?P<idnumber>[0-9]+)\s*\((?P<status>.*)\)/', $list, $res);
    return $res;
}
