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

use tool_moodlebox\helper;

/**
 * Callback to add footer elements.
 *
 * @return string HTML footer content
 */
function tool_moodlebox_standard_footer_html() {
    global $PAGE;

    if (has_capability('moodle/site:config', context_system::instance())) {

        $output = \html_writer::start_tag('form', ['id' => 'footerrestartstop',
                'action' => new \moodle_url('/admin/tool/moodlebox/index.php')]);

        $output .= \html_writer::empty_tag('input', ['type' => 'hidden', 'id' => 'restartstopvalue', 'name' => 'init', 'value' => 0]);

        $output .= \html_writer::empty_tag('input', ['type' => 'button', 'id' => 'rebootbox', 'name' => 'reboot',
                'value' => get_string('restart', 'tool_moodlebox'), 'class' => 'btn btn-secondary m-t-1']);
        $output .= \html_writer::empty_tag('input', ['type' => 'button', 'id' => 'shutdownbox', 'name' => 'shutdown',
                'value' => get_string('shutdown', 'tool_moodlebox'), 'class' => 'btn btn-secondary m-t-1']);

        $output .= "</form>";

        $PAGE->requires->js_call_amd('tool_moodlebox/footerform', 'init');

        return $output;
    }
}
