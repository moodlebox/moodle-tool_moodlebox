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

    global $CFG;

    // Check that logged in user has admin or manager role.
    if ( has_capability('tool/moodlebox:viewbuttonsinfooter', context_system::instance()) ) {
        // Get throttled state and print warning if throttling is active or has occurred.
        if ( $throttledstate = \tool_moodlebox\local\utils::get_throttled_state() ) {
            if ( $throttledstate['undervoltagedetected'] || $throttledstate['undervoltageoccurred'] ) {
                \core\notification::error(get_string('badpowersupply', 'tool_moodlebox'));
            }
        }
    }

    // Check that logged in user has admin or manager role and option is enabled.
    if (has_capability('tool/moodlebox:viewbuttonsinfooter', context_system::instance()) &&
            get_config('tool_moodlebox', 'buttonsinfooter')) {

        $thisplugindir = $CFG->dirroot . '/admin/tool/moodlebox/';
        $reboottriggerfilename = $thisplugindir . '.reboot-server';
        $shutdowntriggerfilename = $thisplugindir . '.shutdown-server';

        $restartshutdownform = new restartshutdown_form(null, null, 'post', '', array('id' => 'formrestartstop'));

        if ($data = $restartshutdownform->get_data()) {
            if (!empty($data->restartbutton)) {
                file_put_contents($reboottriggerfilename, 'reboot');
                \core\notification::warning(get_string('restartmessage', 'tool_moodlebox'));
            }
            if (!empty($data->shutdownbutton)) {
                file_put_contents($shutdowntriggerfilename, 'shutdown');
                \core\notification::warning(get_string('shutdownmessage', 'tool_moodlebox'));
            }
        }

        $output = $restartshutdownform->render();

        return $output;
    }

}
