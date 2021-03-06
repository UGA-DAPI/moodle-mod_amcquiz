<?php
/**
 * Internal library of functions for module amcquiz.
 *
 * All the amcquiz specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once dirname(dirname(__DIR__)).'/config.php';
require_once $CFG->libdir.'/formslib.php';
require_once $CFG->libdir.'/questionlib.php';
require_once __DIR__.'/lib.php';

define('AMC_QUESTIONS_TYPES', ['multichoice', 'truefalse']);
define('AMC_QUESTIONS_GROUP_TYPE', 'description');
define('AMC_TARGET_QUESTION', 'question');
define('ALLOWED_TARGETS', ['group', 'question']);

// ACTIONS
define('ACTION_LOAD_CATEGORIES', 'load-categories');
define('ACTION_LOAD_QUESTIONS', 'load-questions');
define('ACTION_ADD_GROUP', 'add-group');
define('ACTION_DELETE_GROUP', 'delete-group');
define('ACTION_UPDATE_GROUP_NAME', 'update-group-name');
define('ACTION_DELETE_GROUP_DESCRIPTION', 'delete-group-description');
define('ACTION_DELETE_QUESTION', 'delete-question');
define('ACTION_UPDATE_QUESTION_SCORE', 'update-question-score');
define('ACTION_REORDER_GROUP_QUESTIONS', 'reorder-group-questions');
define('ACTION_REORDER_GROUPS', 'reorder-groups');
define('ACTION_ADD_QUESTIONS', 'add-questions');
define('ACTION_ADD_DESCRIPTION', 'add-description');
define('ACTION_STUDENT_ACCESS', 'set-student-access');
define('ACTION_SEND_NOTIFICATION', 'send-notification');
define('ACTION_EXPORT_QUIZ', 'export');
define('ACTION_DELETE_UNRECOGNIZED_SHEETS', 'delete-unrecognized-sheets');
define('ACTION_DELETE_ALL_SHEETS', 'delete-all-sheets');
define('ACTION_LAUNCH_ASSOCIATION', 'launch-association');
define('ACTION_ASSOCIATE_MANUALLY', 'associate-manually');
define('ACTION_LAUNCH_GRADING_DOCS_GENERATION', 'launch-grading-docs-generation');
define('ACTION_ANNOTATE_SHEETS', 'annotate-sheets');
define('ACTION_GET_SUBJECT_PDF', 'get-subject-pdf');
define('ACTION_GET_CATALOG_PDF', 'get-catalog-pdf');
define('ACTION_GET_CORRECTION_PDF', 'get-correction-pdf');
define('ACTION_GET_DOCUMENTS_ZIP', 'get-documents-zip');
define('ACTION_RECORD_GRADE_BOOK', 'record-grade-book');
// this one is specific since it will be used in url parameters
define('ACTION_UPLOAD_SHEETS', 'sheet-upload');

define('ACTION_GET_GRADE_CSV', 'get-grade-csv');
define('ACTION_GET_GRADE_ODS', 'get-grade-ods');

defined('MOODLE_INTERNAL') || die();

function amcquiz_list_cat_and_context_questions(string $catid, string $contextid, string $target, array $excludeids = [])
{
    global $DB, $OUTPUT;
    $sql = 'SELECT q.id as id, q.name as name, q.qtype AS type, q.timemodified as qmodified ';
    $sql .= 'FROM {question} q JOIN {question_categories} qc ON q.category = qc.id ';
    $sql .= 'WHERE q.hidden = 0 ';
    if (AMC_TARGET_QUESTION === $target) {
        // list multichoice truefalse questions
        $sql .= 'AND q.qtype IN ("'.implode('","', AMC_QUESTIONS_TYPES).'") ';
    } else {
        // list description questions
        $sql .= 'AND q.qtype = "'.AMC_QUESTIONS_GROUP_TYPE.'"';
    }

    $sql .= 'AND qc.id = '.$catid.' ';
    $sql .= 'AND qc.contextid = '.$contextid.' ';
    // Also need to exclude questions already associated with the amcquiz instance
    if (count($excludeids) > 0) {
        $sql .= 'AND q.id NOT IN ('.implode(',', $excludeids).') ';
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
            'icon' => $icon,
        ];
    }, $records);

    return $questions;
}

function amcquiz_list_categories_options($courseid, $cmid, $target, $excludeids = [])
{
    $contexts = [
        context_system::instance(),
        context_course::instance($courseid),
        context_module::instance($cmid),
        context_coursecat::instance($courseid),
    ];
    // rebuild moodle questionlib.php method with a little change that will allow us to only get relevant question types
    $result = amcquiz_question_category_options_filtered($contexts, $target, $excludeids);

    return $result;
}

/**
 * Output an array of question categories.
 * This was cloned from moodle/lib/questionlib.php->question_category_options to suit our needs
 * Basically we only need to filter relevant question types while fetching categories and question counts by category.
 */
function amcquiz_question_category_options_filtered($contexts, $target, $excludeids)
{
    $pcontexts = [];
    foreach ($contexts as $context) {
        $pcontexts[] = $context->id;
    }
    $contextslist = join($pcontexts, ', ');

    $categories = amcquiz_get_categories_for_contexts_and_target($contextslist, $target, $excludeids);

    // from questionlib.php
    $categories = question_add_context_in_key($categories);
    // from questionlib.php
    $categories = add_indented_names($categories, -1);

    // sort cats out into different contexts
    $categoriesarray = [];
    foreach ($pcontexts as $contextid) {
        $context = context::instance_by_id($contextid);
        $contextstring = $context->get_context_name(true, true);
        foreach ($categories as $category) {
            if ($category->contextid == $contextid) {
                $cid = $category->id;
                $countstring = !empty($category->questioncount) ? "($category->questioncount)" : '';
                $categoriesarray[$contextstring][$cid] = format_string($category->indentedname, true, ['context' => $context]).$countstring;
            }
        }
    }

    return $categoriesarray;
}

function amcquiz_get_categories_for_contexts_and_target($contexts, $target, $excludeids)
{
    global $DB;
    $sql = 'SELECT c.*, ';
    $sql .= '(SELECT count(1) FROM {question} q ';
    $sql .= 'WHERE c.id = q.category AND q.hidden=0 AND q.parent=0 ';
    if (AMC_TARGET_QUESTION === $target) {
        $sql .= 'AND q.qtype IN ("'.implode('","', AMC_QUESTIONS_TYPES).'") ';
    } else {
        $sql .= 'AND q.qtype = "'.AMC_QUESTIONS_GROUP_TYPE.'" ';
    }

    if (count($excludeids) > 0) {
        $sql .= 'AND q.id NOT IN ('.implode(',', $excludeids).') ';
    }

    $sql .= ') AS questioncount ';
    $sql .= 'FROM {question_categories} c ';
    $sql .= ' WHERE c.contextid IN ('.$contexts.') ';
    $sql .= ' ORDER BY parent, sortorder, name ASC';

    return $DB->get_records_sql($sql);
}

/**
 * Parses the config setting 'scoringrules' to convert it into an array.
 * It is used in mod_form.php.
 *
 * @return array
 */
function amcquiz_parse_scoring_rules()
{
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
 * Get available grade rounding strategies.
 *
 * @return array available grade rounding strategies
 */
function amcquiz_get_grade_rounding_strategies()
{
    return [
        'n' => get_string('grade_rounding_strategy_nearest', 'mod_amcquiz'),
        'i' => get_string('grade_rounding_strategy_lower', 'mod_amcquiz'),
        's' => get_string('grade_rounding_strategy_upper', 'mod_amcquiz'),
    ];
}

/**
 * Return a user record.
 *
 * @todo Optimize? One query per user is doable, the difficulty is to sort results according to prefix order.
 *
 * @global \moodle_database $DB
 *
 * @param string $idn
 *
 * @return object record from the user table
 */
function amcquiz_get_student_by_idnumber($idn)
{
    global $DB;
    $prefixestxt = get_config('mod_amcquiz', 'idnumberprefixes');
    $prefixes = array_filter(array_map('trim', preg_split('/\R/', $prefixestxt)));
    $prefixes[] = '';
    foreach ($prefixes as $p) {
        $user = $DB->get_record('user', array('idnumber' => $p.$idn, 'confirmed' => 1, 'deleted' => 0));
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
 *
 * @param context if
 *
 * @return int count student user
 */
function amcquiz_has_students($context)
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

/**
 * Gets all the users in this context or higher.
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
 * @param stdClass $cm      mod_amcquiz instance
 * @param bool     $parent
 * @param string   $group
 * @param bool     $exclude
 *
 * @return array
 */
function amcquiz_get_student_users($cm, $parent = false, $group = '', $exclude = null)
{
    global $DB;
    $codelength = get_config('mod_amcquiz', 'amccodelength');
    $allnames = get_all_user_name_fields(true, 'u');
    $fields = 'u.id, u.confirmed, u.username, '.$allnames.', '.'RIGHT(u.idnumber,'.$codelength.') as idnumber';
    $context = context_module::instance($cm->id);
    $roleid = array_keys(get_archetype_roles('student'));
    $parentcontexts = '';
    if ($parent) {
        $parentcontexts = substr($context->path, 1); // kill leading slash
        $parentcontexts = str_replace('/', ',', $parentcontexts);
        if ('' !== $parentcontexts) {
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
        $idnumberselect = ' AND RIGHT(u.idnumber,'.$codelength.") $idnumbers ";
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
        $groupjoin = 'JOIN {groups_members} gm ON gm.userid = u.id';
        $groupselect = ' AND gm.groupid = :groupid ';
        $params['groupid'] = $group;
    } else {
        $groupjoin = '';
        $groupselect = '';
    }

    $params['contextid'] = $context->id;
    list($sort, $sortparams) = users_order_by_sql('u');
    $params = array_merge($params, $sortparams);
    $ejoin = 'JOIN {user_enrolments} ue ON ue.userid = u.id
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :ecourseid)';
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
 * Get course module users and return the result as an array usable in an HTML select element.
 *
 * @param stdClass $cm       the course module (ie a amcquiz instance)
 * @param string   $idnumber a user id
 * @param string   $groupid  a group id
 * @param array    $exclude  users to exclude
 *
 * @return array an array usable in an HTML select element
 */
function amcquiz_get_users_for_select_element($cm, $idnumber = null, $groupid = '', $exclude = null)
{
    global $USER, $CFG;

    $codelength = get_config('mod_amcquiz', 'amccodelength');
    if (is_null($idnumber)) {
        $idnumber = $USER->idnumber;
    }
    if (count($idnumber) > $codelength) {
        $idnumber = substr($idnumber, -1 * $codelength); //by security
    }

    if ($exclude && $idnumber) {
        $exclude = array_diff($exclude, array($idnumber));
    }
    $users = amcquiz_get_student_users($cm, true, $groupid, $exclude);
    $label = get_string('selectuser', 'mod_amcquiz');
    $menu = [];
    foreach ($users as $user) {
        $userfullname = fullname($user);
        // In case of prefixed student number.
        $usernumber = substr($user->idnumber, -1 * $codelength);
        $menu[] = [
          'value' => $user->idnumber,
          'label' => $userfullname,
          'selected' => intval($usernumber) === intval($idnumber),
        ];
    }

    return $menu;
}

function amcquiz_backup_source($file)
{
    copy($file, $file.'.orig');
}

function amcquiz_restore_source($file)
{
    copy($file, substr($file, -5));
}

function amcquiz_get_code($name)
{
    preg_match('/name-(?P<student>[0-9]+)[:-](?P<copy>[0-9]+).jpg$/', $name, $res);

    return $res['student'].'_'.$res['copy'];
}

function amcquiz_get_list_row($list)
{
    preg_match('/(?P<student>[0-9]+):(?P<copy>[0-9]+)\s*(?P<idnumber>[0-9]+)\s*\((?P<status>.*)\)/', $list, $res);

    return $res;
}
