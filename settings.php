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

    // Add new admin top category 'moodlebox' in admin tree.
    $ADMIN->add('root', new admin_category('moodlebox', new lang_string('pluginname', 'tool_moodlebox')));

    // Add admin external page 'dashboard' to category 'moodlebox'.
    $moodleboxadminpage = new admin_externalpage('tool_moodlebox',
            new lang_string('dashboard', 'tool_moodlebox'),
            new moodle_url('/admin/tool/moodlebox/index.php'));
    $ADMIN->add('moodlebox', $moodleboxadminpage);

    // Add admin setting page to category 'moodlebox'.
    $moodleboxsettingpage = new admin_settingpage('tool_moodlebox_settings',
            new lang_string('configuration', 'tool_moodlebox'));

    if ($ADMIN->fulltree) {
        // Add info.
        $moodleboxsettingpage->add(new admin_setting_heading('moodlebox_infoheading',
                new lang_string('infoheading', 'tool_moodlebox'), new lang_string('projectinfo', 'tool_moodlebox')));
        $moodleboxsettingpage->add(new admin_setting_description('moodlebox_documentation',
                new lang_string('documentation', 'tool_moodlebox'),
                new lang_string('documentation_desc', 'tool_moodlebox')));
        $moodleboxsettingpage->add(new admin_setting_description('moodlebox_forum',
                new lang_string('forum', 'tool_moodlebox'),
                new lang_string('forum_desc', 'tool_moodlebox')));

        // Add settings.
        $moodleboxsettingpage->add(new admin_setting_heading('moodlebox_settingheading',
                new lang_string('configuration', 'tool_moodlebox'), ''));
        $moodleboxsettingpage->add(new admin_setting_configcheckbox('tool_moodlebox/datetimebuttonsinfooter',
                new lang_string('showdatetimebuttonsinfooter', 'tool_moodlebox'),
                new lang_string('showdatetimebuttonsinfooter_desc', 'tool_moodlebox'), 0));
        $moodleboxsettingpage->add(new admin_setting_configcheckbox('tool_moodlebox/restartshutdownbuttonsinfooter',
                new lang_string('showrestartshutdownbuttonsinfooter', 'tool_moodlebox'),
                new lang_string('showrestartshutdownbuttonsinfooter_desc', 'tool_moodlebox'), 0));
        $moodleboxsettingpage->add(new admin_setting_configcheckbox('tool_moodlebox/ihavedonated',
                new lang_string('ihavedonated', 'tool_moodlebox'),
                new lang_string('ihavedonated_desc', 'tool_moodlebox'), 0));
    }
    $ADMIN->add('moodlebox', $moodleboxsettingpage);

    // Workaround Moodle insisting having a subcategory in top level admin tree category.
    // We add a dummy hidden sub-category with a dummy settingpage.
    $moodleboxsettingpagedummy = new admin_settingpage('tool_moodlebox_dummy', 'dummy');
    $ADMIN->add('moodlebox', new admin_category('moodlebox_dummy', 'hidden', true));
    $ADMIN->add('moodlebox_dummy', $moodleboxsettingpagedummy);

}
