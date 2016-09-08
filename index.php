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
 * Provides a dashboard of some hardware settings of the MoodleBox,
 * a way to set the date of the MoodleBox and to restart and shutdown
 * the MoodleBox from inside Moodle.
 *
 * @seek       https://github.com/martignoni/moodlebox-plugin/
 * @package    tool
 * @subpackage moodlebox
 * @copyright  2016 Nicolas Martignoni <nicolas@martignoni.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin = new stdClass();

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__).'/version.php');

admin_externalpage_setup('tool_moodlebox');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'tool_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

echo $OUTPUT->header();

$hardware = exec('awk \'/^Hardware/{print $3}\' /proc/cpuinfo');
switch ( $hardware ) {
    case 'BCM2708':
        $platform = 'rpi1';
        break;
    case 'BCM2709':
        $revision = exec('awk \'/^Revision/{print $3}\' /proc/cpuinfo');
        if ( $revision === 'a02082' || $revision === 'a22082' ) {
            $platform = 'rpi3';
        } else {
            $platform = 'rpi2';
        }
        break;
    default:
        $platform = 'unknown';
}

if ( strpos($platform, 'rpi') !== false ) { // We are on a RPi

    $PAGE->requires->js('/admin/tool/moodlebox/checktime.js', false);
    $systemtime = usergetdate(time())[0];
    $PAGE->requires->js_init_call('checktime', array($systemtime), false);

    $kernelversion = php_uname('s') . ' ' . php_uname('r') . ' ' .  php_uname('m');
    $raspbianversion = exec('lsb_release -ds');
    $cpuload = sys_getloadavg();
    exec('cat /var/lib/misc/dnsmasq.leases', $leases);
    $dhcpclientnumber = count($leases);
    $cputemperature = exec('awk \'{print $1/1000" °C"}\' /sys/class/thermal/thermal_zone0/temp');
    $cpufrequency = exec('awk \'{print $1/1000" Mhz"}\' /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq');
    $uptime = exec('uptime -p');
    $sdcardtotalspace = disk_total_space('/');
    $sdcardfreespace = disk_free_space('/');
    $moodleboxversion = $plugin->release . ' (' . $plugin->version . ')';

    class datetimeset_form extends moodleform {
        public function definition() {
            $mform = $this->_form;
            $mform->addElement('date_time_selector', 'currentdatetime', get_string('datetime', 'tool_moodlebox'),
                                array(
                                    'startyear' => date("Y") - 2,
                                    'stopyear'  => date("Y") + 2,
                                    'timezone'  => 99,
                                    'step'      => 1,
                                    'optional'  => true)
                                );
            $this->add_action_buttons(false, get_string('datetimeset', 'tool_moodlebox'));
        }
    }

    class restartshutdown_form extends moodleform {
        public function definition() {
            $mform = $this->_form;
            $buttonarray = array();
            $buttonarray[] = & $mform->createElement('submit', 'restartbutton',
                                                      get_string('restart', 'tool_moodlebox'));
            $buttonarray[] = & $mform->createElement('submit', 'shutdownbutton',
                                                      get_string('shutdown', 'tool_moodlebox'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
        }
    }

    // System information section
    echo $OUTPUT->heading(get_string('systeminfo', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $table = new flexible_table('moodleboxstatus_table');
    $table->define_columns(array('parameter', 'information'));
    $table->define_headers(array(get_string('parameter', 'tool_moodlebox'), get_string('information', 'tool_moodlebox')));
    $table->define_baseurl($PAGE->url);
    $table->column_style_all('width', '50%');
    $table->set_attribute('id', 'moodleboxstatus');
    $table->set_attribute('class', 'admintable environmenttable generaltable');
    $table->setup();

    $table->add_data(array(get_string('sdcardavailablespace', 'tool_moodlebox'), display_size($sdcardfreespace) . ' (' . 100*round($sdcardfreespace/$sdcardtotalspace, 3) . '%)'));
    $table->add_data(array(get_string('cpuload', 'tool_moodlebox'), $cpuload[0] . ', ' . $cpuload[1] . ', ' . $cpuload[2]));
    $table->add_data(array(get_string('cputemperature', 'tool_moodlebox'), $cputemperature));
    $table->add_data(array(get_string('cpufrequency', 'tool_moodlebox'), $cpufrequency));
    $table->add_data(array(get_string('uptime', 'tool_moodlebox'), $uptime));
    $table->add_data(array(get_string('dhcpclientnumber', 'tool_moodlebox'), $dhcpclientnumber));
    if ($dhcpclientnumber > 0) {
        foreach($leases as $row) {
            $item = explode(' ', $row);
            $table->add_data(array(get_string('dhcpclientinfo', 'tool_moodlebox'), $item[2] . ' (' . $item[3] . ')'), 'dhcpclientinfo');
        }
    }
    $table->add_data(array(get_string('raspberryhardware', 'tool_moodlebox'), get_string($platform, 'tool_moodlebox')));
    $table->add_data(array(get_string('raspbianversion', 'tool_moodlebox'), $raspbianversion));
    $table->add_data(array(get_string('kernelversion', 'tool_moodlebox'), $kernelversion));
    $table->add_data(array(get_string('moodleboxpluginversion', 'tool_moodlebox'), $moodleboxversion));

    $table->print_html();

    echo $OUTPUT->box_end();

    // Time setting section
    echo $OUTPUT->heading(get_string('datetimesetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $datetimesetform = new datetimeset_form();
    $datetimesetform->display();

    if ($data = $datetimesetform->get_data()) {
        if (!empty($data->submitbutton)) {
            $datecommand = "date +%s -s @$data->currentdatetime";
            exec("echo $datecommand > .set-server-datetime");
            \core\notification::warning(get_string('datetimemessage', 'tool_moodlebox'));
        }
    }

    echo $OUTPUT->box_end();

    // Restart-shutdown section
    echo $OUTPUT->heading(get_string('restartstop', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $restartshutdownform = new restartshutdown_form();
    $restartshutdownform->display();

    if ($data = $restartshutdownform->get_data()) {
    // idea from http://stackoverflow.com/questions/5226728/how-to-shutdown-ubuntu-with-exec-php
    // adapted for use with incron
        if (!empty($data->restartbutton)) {
            exec('touch .reboot-server');
            \core\notification::warning(get_string('restartmessage', 'tool_moodlebox'));
        }
        if (!empty($data->shutdownbutton)) {
            exec('touch .shutdown-server');
            \core\notification::warning(get_string('shutdownmessage', 'tool_moodlebox'));
        }
    }

    echo $OUTPUT->box_end();
} else { // We're not on a Raspberry Pi
    \core\notification::error(get_string('unsupportedhardware', 'tool_moodlebox'));
}

echo $OUTPUT->footer();
