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
 * MoodleBox.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @author     2018 Adrian Perez Rodriguez {@link mailto:p.adrian@gmx.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/admin/tool/moodlebox/forms.php');

/**
 * Callback to add footer elements.
 *
 * @return string HTML footer content
 */
function tool_moodlebox_standard_footer_html() {

    if ( has_capability('moodle/site:config', context_system::instance()) ) {
        // Get throttled state and print warning if throttling is active or has occurred.
        if ( $throttledstate = \tool_moodlebox\local\utils::get_throttled_state() ) {
            if ( $throttledstate['undervoltagedetected'] || $throttledstate['undervoltageoccurred'] ) {
                \core\notification::error(get_string('badpowersupply', 'tool_moodlebox'));
            }
        } else {
            // TODO: print error message.
        }
    }

    if (has_capability('moodle/site:config', context_system::instance()) && get_config('tool_moodlebox', 'buttonsinfooter')) {
        $restartshutdownform = new restartshutdown_form('/admin/tool/moodlebox/index.php',
                null, 'post', '', array('id' => 'formrestartstop'));
        $output = $restartshutdownform->render();

        return $output;
    }

}
