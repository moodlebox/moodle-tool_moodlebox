<?php
// This file is part of Moodle - https://moodle.org/
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
 * Renderer for tool_moodlebox.
 *
 * @package    tool_moodlebox
 * @copyright  2021 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * MoodleBox plugin tool_moodlebox renderer.
 *
 * @copyright  2021 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param dashboard $page
     * @return string html for the page
     */
    public function render_dashboard($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_moodlebox/dashboard', $data);
    }

    /**
     * Render the "not validated" alert message.
     *
     * @return string
     * @throws \coding_exception
     */
    public function notvalidatedalert(): string {
        return \html_writer::div(get_string('notvalidated', manager::PLUGINNAME), '', ['class' => 'alert alert-primary']);
    }

}
