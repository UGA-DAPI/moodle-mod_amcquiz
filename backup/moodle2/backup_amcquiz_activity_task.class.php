<?php

require_once $CFG->dirroot.'/mod/amcquiz/backup/moodle2/backup_amcquiz_stepslib.php';
require_once $CFG->dirroot.'/mod/amcquiz/backup/moodle2/backup_amcquiz_settingslib.php';

/**
 * choice backup task that provides all the settings and steps to perform one
 * complete backup of the activity.
 */
class backup_amcquiz_activity_task extends backup_activity_task
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
        $this->add_step(new backup_amcquiz_activity_structure_step('amcquiz_structure', 'amcquiz.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links.
     */
    public static function encode_content_links($content)
    {
        return $content;
    }
}
