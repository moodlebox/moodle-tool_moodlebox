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

namespace tool_moodlebox;
use core\hook\output\before_footer_html_generation;
use html_writer;

/**
 * Hook callbacks for tool_moodlebox.
 *
 * @package    tool_moodlebox
 * @copyright  2024 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Callback to add footer elements.
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $CFG;
        $context = \context_system::instance();

        // Check that logged in user has admin or manager role.
        if (has_capability('tool/moodlebox:viewbuttonsinfooter', $context)) {
            // Get throttled state and print warning if throttling is active or has occurred.
            if ( $throttledstate = \tool_moodlebox\local\utils::get_throttled_state() ) {
                if ( $throttledstate['undervoltagedetected'] ) {
                    \core\notification::error(get_string('undervoltagedetected', 'tool_moodlebox'));
                } else if ( $throttledstate['undervoltageoccurred'] ) {
                    \core\notification::warning(get_string('undervoltageoccurred', 'tool_moodlebox'));
                }
            }
        }

        $output = '';
        $thisplugindir = $CFG->dirroot . '/admin/tool/moodlebox/';

        if (has_capability('tool/moodlebox:viewbuttonsinfooter', $context) &&
                get_config('tool_moodlebox', 'datetimebuttonsinfooter')) {

            // Display date and time setting buttons.
            $datetimetriggerfile = $thisplugindir . '.set-server-datetime';
            $datetimesetform = new \tool_moodlebox\form\datetimeset_form();

            if ($data = $datetimesetform->get_data()) {
                if (!empty($data->submitbutton)) {
                    $datecommand = "date +%s -s @$data->currentdatetime";
                    file_put_contents($datetimetriggerfile, "#!/bin/sh\n" . $datecommand . "\nexit 0\n");
                    \core\notification::warning(get_string('datetimemessage', 'tool_moodlebox'));
                }
            }

            $output .= \core\html_writer::empty_tag("hr", ['id' => 'datetimesetbuttonsspacer']);
            $output .= \core\html_writer::div($datetimesetform->render(), "", ['id' => 'datetimesetbuttons']);
        }

        if (has_capability('tool/moodlebox:viewbuttonsinfooter', $context) &&
                get_config('tool_moodlebox', 'restartshutdownbuttonsinfooter')) {

            // Display restart and shutdown buttons.
            $reboottriggerfile = $thisplugindir . '.reboot-server';
            $shutdowntriggerfile = $thisplugindir . '.shutdown-server';
            $restartshutdownform = new \tool_moodlebox\form\restartshutdown_form(
                null,
                null,
                'post',
                '',
                ['id' => 'formrestartstop'],
            );

            if ($data = $restartshutdownform->get_data()) {
                if (!empty($data->restartbutton)) {
                    file_put_contents($reboottriggerfile, 'reboot');
                    \core\notification::warning(get_string('restartmessage', 'tool_moodlebox'));
                }
                if (!empty($data->shutdownbutton)) {
                    file_put_contents($shutdowntriggerfile, 'shutdown');
                    \core\notification::warning(get_string('shutdownmessage', 'tool_moodlebox'));
                }
            }

            $output .= html_writer::empty_tag("hr", ['id' => 'footerbuttonsspacer']);
            $output .= html_writer::div($restartshutdownform->render(), "", ['id' => 'footerbuttons']);

        }

        echo $output;
    }
}
