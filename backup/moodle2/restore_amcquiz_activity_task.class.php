<?php

require_once $CFG->dirroot.'/mod/amcquiz/backup/moodle2/restore_amcquiz_stepslib.php';

/**
 * choice backup task that provides all the settings and steps to perform one
 * complete backup of the activity.
 */
class restore_amcquiz_activity_task extends restore_activity_task
{
    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings()
    {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps()
    {
        $this->add_step(new restore_amcquiz_activity_structure_step('amcquiz_structure', 'amcquiz.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links.
     */
    public static function define_decode_contents()
    {
        return [];
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     */
    public static function define_decode_rules()
    {
        $rules = array();

        $rules[] = new restore_decode_rule('ALTERNATIVEVIEWBYID', '/mod/amcquiz/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('ALTERNATIVEINDEX', '/mod/amcquiz/index.php?id=$1', 'course');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * amcquiz logs. It must return one array
     * of {@link restore_log_rule} objects.
     *
     * But we do not have any "official moodle log" for this module
     */
    public static function define_restore_log_rules()
    {
        $rules = array();
        $rules[] = new restore_log_rule('amcquiz', 'view', 'view.php?id={course_module}', '{amcquiz}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects.
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course()
    {
        $rules = array();
        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('amcquiz', 'view all', 'index?id={course}', null, null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('amcquiz', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
