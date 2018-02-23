<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_amcquiz questions_updated event.
 *
 * @package    mod_amcquiz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_amcquiz\event;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_amcquiz questions_updated event
 *
 * Called when question score / question order / group order is updated
 *
 * @package    mod_amcquiz
 * @since      Moodle 3.0
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_updated extends \core\event\base
{
    protected function init()
    {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name()
    {
        return get_string('event_question_deleted', 'mod_amcquiz');
    }
}
