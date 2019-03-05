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
 * Add page to admin menu.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // Speedup for non-admins.

    // Add new admin sub-category to category 'server' in admin tree.
    $ADMIN->add('server', new admin_category('moodlebox', get_string('pluginname', 'tool_moodlebox')));

    // Add admin external page 'dashboard' to sub-category 'moodlebox'.
    $moodleboxadminpage = new admin_externalpage('tool_moodlebox',
            get_string('dashboard', 'tool_moodlebox'),
            new moodle_url('/admin/tool/moodlebox/index.php'));
    $ADMIN->add('moodlebox', $moodleboxadminpage);

    // Add admin setting page to sub-category 'moodlebox'.
    $moodleboxsettingpage = new admin_settingpage('tool_moodlebox_settings',
            get_string('configuration', 'tool_moodlebox'));

    // Add info.
    $moodleboxsettingpage->add(new admin_setting_heading('moodlebox_infoheading',
            get_string('infoheading', 'tool_moodlebox'), get_string('projectinfo', 'tool_moodlebox')));
    $moodleboxsettingpage->add(new admin_setting_description('moodlebox_documentation',
            get_string('documentation', 'tool_moodlebox'),
            get_string('documentation_desc', 'tool_moodlebox')));
    $moodleboxsettingpage->add(new admin_setting_description('moodlebox_forum',
            get_string('forum', 'tool_moodlebox'),
            get_string('forum_desc', 'tool_moodlebox')));

    // Add settings.
    $moodleboxsettingpage->add(new admin_setting_heading('moodlebox_settingheading',
            get_string('configuration', 'tool_moodlebox'), ''));
    $moodleboxsettingpage->add(new admin_setting_configcheckbox('tool_moodlebox/buttonsinfooter',
            get_string('showbuttonsinfooter', 'tool_moodlebox'),
            get_string('showbuttonsinfooter_desc', 'tool_moodlebox'), 0));
    $ADMIN->add('moodlebox', $moodleboxsettingpage);

}
