<?php

/**
 * Library of interface functions and constants for module amcquiz.
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the amcquiz specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/* @var $DB moodle_database */

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature.
 *
 * @see plugin_supports() in lib/moodlelib.php
 *
 * @param string $feature FEATURE_xx constant for requested feature
 *
 * @return mixed true if the feature is supported, null if unknown
 */
function amcquiz_supports($feature)
{
    switch ($feature) {
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_MOD_INTRO:
            return false;

        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_GRADE_HAS_GRADE:
            return true;

        default:
            return null;
    }
}

/**
 * Saves a new instance of the amcquiz into the database
 * Only amcquiz and parameters are concerned.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id of the new instance.
 *
 * @param stdClass             $formdata an object representing an amcquiz from the form in mod_form.php
 * @param mod_amcquiz_mod_form $mform    a form extending moodleform_mod
 *
 * @return int The id of the newly inserted amcquiz record
 */
function amcquiz_add_instance(stdClass $formdata, mod_amcquiz_mod_form $mform)
{
    $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();

    // after this action amcquiz will have an id and a key for api
    $amcquiz = $amcquizmanager->create_quiz_from_form($formdata);

    // init project in API
    $curlmanager = new \mod_amcquiz\local\managers\curlmanager();
    $curlmanager->init_amcquiz($amcquiz);

    if (isset($formdata->uselatexfile) && true === (bool) $formdata->uselatexfile) {
        // mform is required only for file upload handling
        $amcquiz = $amcquizmanager->send_latex_file($amcquiz, $formdata, $mform);
        $amcquiz->uselatexfile = true;
        $amcquizmanager->save($amcquiz);
    } else {
        $amcquiz->uselatexfile = false;
        $amcquizmanager->save($amcquiz);
    }

    // handle parameters (at this time no groups nore questions are associated to instance)
    $amcquizmanager->create_amcquiz_parameters($amcquiz, $formdata->parameters);

    amcquiz_grade_item_update($amcquiz);

    return $amcquiz->id;
}

/**
 * Updates an instance of the amcquiz in the database
 * Only amcquiz and parameters are concerned.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass             $formdata An object from the form in mod_form.php
 * @param mod_amcquiz_mod_form $mform    the form extending moodleform_mod
 *
 * @return bool Success/Fail
 */
function amcquiz_update_instance(stdClass $formdata, mod_amcquiz_mod_form $mform)
{
    global $DB;

    $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    $groupmanager = new \mod_amcquiz\local\managers\groupmanager();

    // get old value for comparison purposes
    $oldamcquiz = $amcquizmanager->get_amcquiz_record($formdata->instance);

    $amcquiz = $amcquizmanager->update_quiz_from_form($formdata);

    if (isset($formdata->uselatexfile) && true === (bool) $formdata->uselatexfile) {
        // mform is required only for file upload handling
        $amcquiz = $amcquizmanager->send_latex_file($amcquiz, $formdata, $mform, true);
        $amcquiz->uselatexfile = true;
        $amcquizmanager->save($amcquiz);
        // if the previous instance was not using a latex file for its definition
        if (!$oldamcquiz->uselatexfile) {
            // should delete all group / questions related to amcquiz
            $amcquizmanager->delete_group_and_questions($formdata->instance);
        }
    } else {
        $amcquiz->uselatexfile = false;
        if ($oldamcquiz->uselatexfile) {
            // should delete all group / questions related to amcquiz
            $amcquiz->groups[] = $groupmanager->add_group($oldamcquiz->id);
        }
        $amcquizmanager->save($amcquiz);
    }

    $amcquizmanager->update_amcquiz_parameters($amcquiz, $formdata->parameters);

    amcquiz_grade_item_update($amcquiz);

    return true;
}

/**
 * Removes an instance of the amcquiz from the database.
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * In order to work it needs the "trash" option to be disabled...
 *
 * @TODO call API in order to delete every files
 *
 * @param int $id Id of the module instance
 *
 * @return bool Success/Failure
 */
function amcquiz_delete_instance($id)
{
    global $DB, $CFG;

    $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    $amcquiz = $amcquizmanager->get_amcquiz_record($id);

    $curlmanager = new \mod_amcquiz\local\managers\curlmanager();
    $curlmanager->delete_amcquiz($amcquiz);

    return true;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course  the current course record
 * @param stdClass $user    the record of the user we are generating report for
 * @param cm_info  $mod     course module info
 * @param stdClass $amcquiz the module instance record
 */
function amcquiz_user_complete($course, $user, $mod, $amcquiz)
{
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in amcquiz activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return bool
 */
function amcquiz_print_recent_activity($course, $viewfullnames, $timestart)
{
    return false;
}

/**
 * Prepares the recent activity data.
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link amcquiz_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int   $index      the index in the $activities to use for the next record
 * @param int   $timestart  append activity since this time
 * @param int   $courseid   the id of the course we produce the report for
 * @param int   $cmid       course module id
 * @param int   $userid     check for a particular user's activity only, defaults to 0 (all users)
 * @param int   $groupid    check for a particular group's activity only, defaults to 0 (all groups)
 */
function amcquiz_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0)
{
}

/**
 * Prints single activity item prepared by {@see amcquiz_get_recent_mod_activity()}.
 */
function amcquiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames)
{
}

/**
 * Function to be run periodically according to the moodle cron.
 *
 * @return bool
 */
function amcquiz_cron()
{
    return false;
}

/**
 * Returns all other caps used in the module.
 *
 * @example return array('moodle/site:accessallgroups');
 *
 * @return array
 */
function amcquiz_get_extra_capabilities()
{
    return [];
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of amcquiz?
 *
 * This function returns if a scale is being used by one amcquiz
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $amcquizid ID of an instance of this module
 *
 * @return bool true if the scale is used by the given amcquiz instance
 */
function amcquiz_scale_used($amcquizid, $scaleid)
{
    return false;
}

/**
 * Checks if scale is being used by any instance of amcquiz.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 *
 * @return bool true if the scale is used by any amcquiz instance
 */
function amcquiz_scale_used_anywhere($scaleid)
{
    return false;
}

/**
 * Creates or updates grade item for the given amcquiz instance.
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $amcquiz instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 */
function amcquiz_grade_item_update(stdClass $amcquiz, $grades = null)
{
    global $CFG;
    require_once $CFG->libdir.'/gradelib.php';

    $params = [];
    $params['itemname'] = clean_param($amcquiz->name, PARAM_NOTAGS);
    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['grademax'] = $amcquiz->parameters->grademax;
    $params['grademin'] = 0;

    if ('reset' === $grades) {
        $params['reset'] = true;
        $grades = null;
    }
    // submit new or updated grades
    return grade_update(
        'mod/amcquiz',
        $amcquiz->course,
        'mod',
        'amcquiz',
        $amcquiz->id,
        0,
        $grades,
        $params
    );
}

/**
 * Update amcquiz grades in the gradebook.
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $amcquiz instance object with extra cmidnumber and modname property
 * @param int      $userid  update grade of specific user only, 0 means all participants
 */
function amcquiz_update_grades(stdClass $amcquiz, $userid = 0)
{
    global $CFG, $DB;
    require_once $CFG->libdir.'/gradelib.php';
    // SHOULD UPDATE GRADES ACCORDING TO NEW GRADE SETTINGS ?
    // GRADES ARE COMPUTED IN
    $amcquizmanager = new \mod_amcquiz\local\managers\amcquizmanager();
    $amcgradedata = $amcquizmanager->read_amc_csv($amcquiz);
    $grades = $amcquizmanager->get_grades($amcgradedata);
    if ($userid) {
        $grades = isset($grades[$userid]) ? $grades[$userid] : null;
    }

    amcquiz_grade_item_update($amcquiz, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 *
 * @return array of [(string)filearea] => (string)description
 */
function amcquiz_get_file_areas($course, $cm, $context)
{
    return [];
}

/**
 * File browsing support for amcquiz file areas.
 *
 * @category files
 *
 * @param file_browser $browser
 * @param array        $areas
 * @param stdClass     $course
 * @param stdClass     $cm
 * @param stdClass     $context
 * @param string       $filearea
 * @param int          $itemid
 * @param string       $filepath
 * @param string       $filename
 *
 * @return file_info instance or null if not found
 */
function amcquiz_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)
{
    return null;
}

/**
 * Serves the files from the amcquiz file areas.
 *
 * @category files
 *
 * @param stdClass $course        the course object
 * @param stdClass $cm            the course module object
 * @param stdClass $context       the amcquiz's context
 * @param string   $filearea      the name of the file area
 * @param array    $args          extra arguments (itemid, path)
 * @param bool     $forcedownload whether or not force download
 * @param array    $options       additional options affecting the file serving
 */
function amcquiz_file_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array())
{
    global $USER;
    send_file_not_found();
}

/**
 * Serve questiontext files in the question text when they are displayed.
 * ie A question being previewed outside an attempt/usage.
 *
 * @param context $previewcontext the quiz context
 * @param int     $questionid     the question id
 * @param context $filecontext    the file (question) context
 * @param string  $filecomponent  the component the file belongs to
 * @param string  $filearea       the file area
 * @param array   $args           remaining file args
 * @param bool    $forcedownload
 * @param array   $options        additional options affecting the file serving
 */
function amcquiz_question_preview_pluginfile(
    $previewcontext,
    $questionid,
    $filecontext,
    $filecomponent,
    $filearea,
         $args,
    $forcedownload,
    $options = array()
) {
    global $CFG;

    require_once $CFG->dirroot.'/lib/questionlib.php';

    list($context, $course, $cm) = get_context_info_array($previewcontext->id);
    require_login($course, false, $cm);

    // We assume that only trusted people can see this report. There is no real way to
    // validate questionid, because of the complexity of random questions.
    //require_capability('mod/amcquiz:viewreports', $context);

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$filecontext->id}/{$filecomponent}/{$filearea}/{$relativepath}";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding amcquiz nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the amcquiz module instance
 * @param stdClass        $course
 * @param stdClass        $module
 * @param cm_info         $cm
 */
function amcquiz_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm)
{
}

/**
 * Extends the settings navigation with the amcquiz settings.
 *
 * This function is called when the context for the page is a amcquiz module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node     $amcquiznode {@link navigation_node}
 */
function amcquiz_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $amcquiznode = null)
{
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the data.
 *
 * @param $mform form passed by reference
 */
function amcquiz_reset_course_form_definition(&$mform)
{
    $mform->addElement('header', 'dataheader', get_string('modulenameplural', 'amcquiz'));
    $mform->addElement('checkbox', 'reset_amcquiz', get_string('deleteallentries', 'amcquiz'));

    $mform->addElement('checkbox', 'reset_amcquiz_documents', get_string('deletenotenrolled', 'amcquiz'));
    $mform->disabledIf('reset_amcquiz_notenrolled', 'reset_amcquiz', 'checked');

    $mform->addElement('checkbox', 'reset_amcquiz_scans', get_string('deleteallratings'));
    $mform->disabledIf('reset_amcquiz_ratings', 'reset_amcquiz', 'checked');

    $mform->addElement('checkbox', 'reset_amcquiz_log', get_string('deleteallcomments'));
    $mform->disabledIf('reset_amcquiz_log', 'reset_amcquiz', 'checked');
}

/**
 * Course reset form defaults.
 *
 * @return array
 */
function amcquiz_reset_course_form_defaults($course)
{
    return array(
        'reset_amcquiz' => 0,
        'reset_amcquiz_scans' => 1,
        'reset_amcquiz_log' => 1,
        'reset_damcquiz_douments' => 0,
    );
}

/**
 * Removes all grades from gradebook.
 *
 * @global object
 * @global object
 *
 * @param int    $courseid
 * @param string $type     optional type
 */
function amcquiz_reset_gradebook($courseid, $type = '')
{
    global $CFG, $DB;

    $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
              FROM {amcquiz} a, {course_modules} cm, {modules} m
             WHERE m.name='amcquiz' AND m.id=cm.module AND cm.instance=a.id AND d.course=?";

    if ($datas = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($datas as $data) {
            amcquiz_grade_item_update($data, 'reset');
        }
    }
}
