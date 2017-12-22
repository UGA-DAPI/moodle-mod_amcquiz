<?php

/**
 * Library of interface functions and constants for module amcquiz
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the amcquiz specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_amcquiz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* @var $DB moodle_database */

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
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
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $formdata an object representing an amcquiz from the form in mod_form.php
 * @return int The id of the newly inserted amcquiz record
 */
function amcquiz_add_instance(stdClass $formdata, mod_amcquiz_mod_form $mform)
{
    global $DB, $USER;


}

/**
 * Updates an instance of the amcquiz in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $formdata An object from the form in mod_form.php
 * @param mod_amcquiz_mod_form $mform the mod_form
 * @return boolean Success/Fail
 */
function amcquiz_update_instance(stdClass $formdata, mod_amcquiz_mod_form $mform)
{
    global $DB;
}

/**
 * Removes an instance of the amcquiz from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * In order to work it needs the "trash" option to be disabled...
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function amcquiz_delete_instance($id)
{
    global $DB;
    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * time = the time they did it
 * info = a short text description
 *
 * @return stdClass|null
 */
/*function amcquiz_user_outline($course, $user, $mod, $amcquiz)
{
    $data = new stdClass();
    $data->time = 0;
    $data->info = '';
    return $data;
}*/

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $amcquiz the module instance record
 * @return void, is supposed to echp directly
 */
function amcquiz_user_complete($course, $user, $mod, $amcquiz)
{
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in amcquiz activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function amcquiz_print_recent_activity($course, $viewfullnames, $timestart)
{
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link amcquiz_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function amcquiz_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0)
{
}

/**
 * Prints single activity item prepared by {@see amcquiz_get_recent_mod_activity()}
 * @return void
 */
function amcquiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames)
{
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * @return boolean
 */
function amcquiz_cron()
{
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function amcquiz_get_extra_capabilities()
{
    return array();
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
 * @return boolean true if the scale is used by any amcquiz instance
 */
function amcquiz_scale_used_anywhere($scaleid)
{
    return false;
}

/**
 * Creates or updates grade item for the given amcquiz instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $amcquiz instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return void
 */
function amcquiz_grade_item_update(stdClass $amcquiz, $grades = null)
{
    global $CFG;
    /*require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($amcquiz->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $amcquiz->score;
    $item['grademin']  = 0;

    if ($grades  === 'reset') {
        $item['reset'] = true;
        $grades = null;
    }

    grade_update(
        'mod/amcquiz',
        $amcquiz->course,
        'mod',
        'amcquiz',
        $amcquiz->id,
        0,
        $grades,
        $item
    );*/
}

/**
 * Update amcquiz grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $amcquiz instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function amcquiz_update_grades(stdClass $amcquiz, $userid = 0)
{
    global $CFG;
    /*require_once($CFG->libdir.'/gradelib.php');
    require_once __DIR__ . '/models/AmcProcessGrade.php';

    $quiz = \mod_amcquiz\local\models\quiz::buildFromRecord($amcquiz);
    $process = new \mod_amcquiz\local\amc\process($quiz);
    $grades = $process->getMarks();
    if ($userid) {
        $grades = isset($grades[$userid]) ? $grades[$userid] : null;
    }

    amcquiz_grade_item_update($amcquiz, $grades);*/
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function amcquiz_get_file_areas($course, $cm, $context)
{
    return array();
}

/**
 * File browsing support for amcquiz file areas
 *
 * @package mod_amcquiz
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function amcquiz_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)
{
    return null;
}

/**
 * Serves the files from the amcquiz file areas
 *
 * @package mod_amcquiz
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the amcquiz's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function amcquiz_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array())
{
    global $USER;
    //require_once __DIR__ . '/models/AmcProcessExport.php';
    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    $filename = rawurldecode(array_pop($args));
    $quizrecord = \mod_amcquiz\local\models\quiz::findById($cm->instance);
    $quiz = \mod_amcquiz\local\models\quiz::buildFromRecord($quizrecord);
    $process = new \mod_amcquiz\local\amc\export($quiz);

    // First, the student use case: to download anotated answer sheet correction-0123456789-Surname.pdf
    // and corrigé
    if (preg_match('/^cr-[0-9]*\.pdf$/', $filename)) {
        $target = $process->workdir . '/cr/corrections/pdf/' . $filename;
        if (!file_exists($target)) {
            send_file_not_found();
        }
        if (has_capability('mod/amcquiz:update', $context)
            || (  $quiz->studentaccess && $USER->id.".pdf" === basename($filename))
            ) {
            send_file($target, $filename, 10, 0, false, false, 'application/pdf') ;
            return true;
        }
    }
    if (preg_match('/^page-[0-9]*-[0-9]*-[0-9]*\.jpg$/', $filename)) {
        $target = $process->workdir . '/cr/corrections/jpg/' . $filename;
        if (!file_exists($target)) {
            send_file_not_found();
        }
        if (has_capability('mod/amcquiz:update', $context)
            || (  $quiz->studentaccess && $USER->id.".jpg" === basename($filename))
            ) {
            send_file($target, $filename, 10, 0, false, false, 'application/jpg') ;
            return true;
        }
    }
    if (preg_match('/^corrige-.*\.pdf$/', $filename)) {
        if ($quiz->corrigeaccess && file_exists("cr-".$USER->id.".pdf")) {
            send_file($process->workdir .'/'. $filename, $filename, 10, 0, false, false, 'application/pdf') ;
            return true;
        }
    }

    // Then teacher only use cases
    require_capability('mod/amcquiz:update', $context);

    // whitelist security
    if (preg_match('/^(sujet|catalog)-.*\.pdf$/', $filename)) {
        $ret = $process->amcCreatePdf('latex');
        if ($ret) {
             send_file($process->workdir .'/'. $filename, $filename, 10, 0, false, false, 'application/pdf') ;
        }
        return $res;
    } elseif (preg_match('/^corriges-.*\.pdf$/', $filename)) {
        $ret = $process->amcCreateCorrection();
        if ($ret) {
             send_file($process->workdir .'/'. $filename, $filename, 10, 0, false, false, 'application/pdf') ;
        }
        return $res;
    } elseif (preg_match('/^failed-.*\.pdf$/', $filename)) {
        $ret = $process->makeFailedPdf();
        if ($ret) {
            send_file($process->workdir . '/' . $filename, $filename, 10, 0, false, false, 'application/pdf') ;
        }
        return $ret;
    } elseif (preg_match('/^sujets-.*\.zip$/', $filename)) {
        $ret = $process->amcImprime() &&  $process->zip();
        if ($ret) {
            send_file($process->workdir .'/'. $filename, $filename, 10, 0, false, false, 'application/zip') ;
        }
            return $ret;
    } elseif (preg_match('/^corrections-.*\.pdf$/', $filename)) {
        $ret = $process->amcAnnotePdf();
        if ($ret) {
            send_file($process->workdir . '/' . $filename, $filename, 10, 0, false, false, 'application/pdf') ;
        }
        return $res;
    } elseif (preg_match('/^cr-[0-9]*\.pdf$/', $filename)) {
        send_file($process->workdir . '/cr/corrections/pdf/' . $filename, $filename, 10, 0, false, false, 'application/pdf') ;
        return true;
    } elseif (preg_match('/grades\.csv$/', $filename)) {
        $ret = $process->amcExport('csv');
        if ($ret) {
            send_file($process->workdir . '/exports/' . $filename, $filename, 10, 0, false, false, 'text/csv') ;
        }
        return $ret;
    } elseif (preg_match('/apogee\.csv$/', $filename)) {
        $ret = $process->writeFileApogeeCsv();
        if ($ret) {
            send_file($process->workdir . '/exports/' . $filename, $filename, 10, 0, false, false, 'text/csv') ;
        }
        return $ret;
    } elseif (preg_match('/^name-[0-9]*_[0-9]*\.jpg$/', $filename)) {
        $filename = preg_replace('/(^name-[0-9]+)_([0-9]*\.jpg$)/', '\1-\2', $filename);
        send_file($process->workdir . '/cr/' . $filename, $filename, 10, 0, false, false, 'application/jpg') ;
        return true;
    } elseif (preg_match('/^page-[0-9]*-[0-9]*-[0-9]*\.jpg$/', $filename)) {
        send_file($process->workdir . '/cr/corrections/jpg/' . $filename, $filename, 10, 0, false, false, 'application/jpg') ;
        return true;
    } elseif (preg_match('/\.ods$/', $filename)) {
        $ret = $process->amcExport('ods');
        if ($ret) {
            send_file($process->workdir . '/exports/' . $filename, $filename, 10, 0, false, false, 'application/vnd.oasis.opendocument.spreadsheet') ;
        }
        return $ret;
    } elseif (preg_match('/\.ppm$/', $filename)) {
        send_file($process->workdir . '/scans/' . $filename, $filename, 10, 0, false, false, 'image/x-portable-pixmap') ;
        return true;
    } elseif (preg_match('/\.pbm$/', $filename)) {
        send_file($process->workdir . '/scans/' . $filename, $filename, 10, 0, false, false, 'image/x-portable-bitmap') ;
        return true;
    } elseif (preg_match('/\.tif[f]*$/', $filename)) {
        send_file($process->workdir . '/scans/' . $filename, $filename, 10, 0, false, false, 'image/tiff') ;
        return true;
    } elseif (preg_match('/\.png$/', $filename)) {
        send_file($process->workdir . '/scans/' . $filename, $filename, 10, 0, false, false, 'image/png') ;
        return true;
    } elseif (preg_match('/\.jp(e|)g$/', $filename)) {
        send_file($process->workdir . '/scans/' . $filename, $filename, 10, 0, false, false, 'image/jpeg') ;
        return true;
    }
    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding amcquiz nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the amcquiz module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function amcquiz_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm)
{
}

/**
 * Extends the settings navigation with the amcquiz settings
 *
 * This function is called when the context for the page is a amcquiz module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $amcquiznode {@link navigation_node}
 */
function amcquiz_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $amcquiznode = null)
{
}

function amcquiz_questions_in_use($questionids)
{
    global $DB;
    /*$records = $DB->get_recordset('amcquiz');
    foreach ($records as $record) {
        $quiz = \mod_amcquiz\local\models\quiz::buildFromRecord($record);
        if ($quiz->questions->contains($questionids)) {
            return true;
        }
    }
    return false;*/
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
 * @return array
 */
function amcquiz_reset_course_form_defaults($course)
{
    return array(
        'reset_amcquiz' => 0,
        'reset_amcquiz_scans' => 1,
        'reset_amcquiz_log' => 1,
        'reset_damcquiz_douments' => 0
    );
}

/**
 * Removes all grades from gradebook
 *
 * @global object
 * @global object
 * @param int $courseid
 * @param string $type optional type
 */
function amcquiz_reset_gradebook($courseid, $type = '')
{
    global $CFG, $DB;

    /*$sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
              FROM {amcquiz} a, {course_modules} cm, {modules} m
             WHERE m.name='amcquiz' AND m.id=cm.module AND cm.instance=a.id AND d.course=?";

    if ($datas = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($datas as $data) {
            amcquiz_grade_item_update($data, 'reset');
        }
    }*/
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * data responses for course $data->courseid.
 *
 * @global object
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function amcquiz_reset_userdata($data)
{
    global $CFG, $DB;
    /*require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/rating/lib.php');

    $componentstr = get_string('modulenameplural', 'data');
    $status = array();

    $allrecordssql = "SELECT al.id
                        FROM {amcquiz_log} al
                             INNER JOIN {amcquiz} a ON al.instanceid = a.id
                       WHERE a.course = ?";

    $alldatassql = "SELECT a.id
                      FROM {amcquiz} a
                     WHERE a.course=?";

    $rm = new rating_manager();
    $ratingdeloptions = new stdClass;
    $ratingdeloptions->component = 'mod_data';
    $ratingdeloptions->ratingarea = 'entry';

    // Set the file storage - may need it to remove files later.
    $fs = get_file_storage();

    // delete entries if requested
    if (!empty($data->reset_amcquiz)) {
        $DB->delete_records_select('comments', "itemid IN ($allrecordssql) AND commentarea='database_entry'", array($data->courseid));
        $DB->delete_records_select('data_content', "recordid IN ($allrecordssql)", array($data->courseid));
        $DB->delete_records_select('data_records', "dataid IN ($alldatassql)", array($data->courseid));

        if ($datas = $DB->get_records_sql($alldatassql, array($data->courseid))) {
            foreach ($datas as $dataid => $unused) {
                if (!$cm = get_coursemodule_from_instance('data', $dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);

                // Delete any files that may exist.
                $fs->delete_area_files($datacontext->id, 'mod_data', 'content');

                $ratingdeloptions->contextid = $datacontext->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallentries', 'data'), 'error'=>false);
    }

    // remove entries by users not enrolled into course
    if (!empty($data->reset_amcquiz_documents)) {
        $recordssql = "SELECT r.id, r.userid, r.dataid, u.id AS userexists, u.deleted AS userdeleted
                         FROM {data_records} r
                              JOIN {data} d ON r.dataid = d.id
                              LEFT JOIN {user} u ON r.userid = u.id
                        WHERE d.course = ? AND r.userid > 0";

        $course_context = context_course::instance($data->courseid);
        $notenrolled = array();
        $fields = array();
        $rs = $DB->get_recordset_sql($recordssql, array($data->courseid));
        foreach ($rs as $record) {
            if (array_key_exists($record->userid, $notenrolled) or !$record->userexists or $record->userdeleted
              or !is_enrolled($course_context, $record->userid)) {
                //delete ratings
                if (!$cm = get_coursemodule_from_instance('data', $record->dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);
                $ratingdeloptions->contextid = $datacontext->id;
                $ratingdeloptions->itemid = $record->id;
                $rm->delete_ratings($ratingdeloptions);

                // Delete any files that may exist.
                if ($contents = $DB->get_records('data_content', array('recordid' => $record->id), '', 'id')) {
                    foreach ($contents as $content) {
                        $fs->delete_area_files($datacontext->id, 'mod_data', 'content', $content->id);
                    }
                }
                $notenrolled[$record->userid] = true;

                $DB->delete_records('comments', array('itemid' => $record->id, 'commentarea' => 'database_entry'));
                $DB->delete_records('data_content', array('recordid' => $record->id));
                $DB->delete_records('data_records', array('id' => $record->id));
            }
        }
        $rs->close();
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'data'), 'error'=>false);
    }

    // remove all ratings
    if (!empty($data->reset_amcquiz_scans)) {
        if ($datas = $DB->get_records_sql($alldatassql, array($data->courseid))) {
            foreach ($datas as $dataid => $unused) {
                if (!$cm = get_coursemodule_from_instance('data', $dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);

                $ratingdeloptions->contextid = $datacontext->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }

        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove all comments
    if (!empty($data->reset_data_comments)) {
        $DB->delete_records_select('comments', "itemid IN ($allrecordssql) AND commentarea='database_entry'", array($data->courseid));
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallcomments'), 'error'=>false);
    }

    // updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('data', array('timeavailablefrom', 'timeavailableto', 'timeviewfrom', 'timeviewto'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;*/
}
