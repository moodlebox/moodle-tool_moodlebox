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
 * A dashboard for the MoodleBox.
 *
 * Provides a dashboard of some hardware settings of the MoodleBox,
 * a way to set the date of the MoodleBox and to restart and shutdown
 * the MoodleBox from inside Moodle.
 *
 * @see        https://github.com/martignoni/moodle-tool_moodlebox
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once(dirname(__FILE__).'/version.php');

admin_externalpage_setup('tool_moodlebox');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'tool_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

echo $OUTPUT->header();

$cpuinfo = file_get_contents('/proc/cpuinfo');
preg_match_all('/^Hardware.*/m', $cpuinfo, $hardwarematch);
preg_match_all('/^Revision.*/m', $cpuinfo, $revisionmatch);
$hardware = explode(' ', $hardwarematch[0][0]);
$hardware = end($hardware);
$revision = explode(' ', $revisionmatch[0][0]);
$revision = end($revision);

switch ( $hardware ) {
    case 'BCM2708':
        $platform = 'rpi1';
        break;
    case 'BCM2709':
        if ( $revision === 'a02082' || $revision === 'a22082' ) {
            $platform = 'rpi3';
        } else {
            $platform = 'rpi2';
        }
        break;
    default:
        $platform = 'unknown';
}

if ( strpos($platform, 'rpi') !== false ) { // We are on a RPi.

    $PAGE->requires->js('/admin/tool/moodlebox/utils.js');
    $systemtime = usergetdate(time())[0];

    $PAGE->requires->js_init_call('checktime', array($systemtime));

    // Get kernel version
    $kernelversion = php_uname('s') . ' ' . php_uname('r') . ' ' .  php_uname('m');

    // Get Raspbian distribution version
    $releaseinfo = parse_ini_file('/etc/os-release');
    $raspbianversion = $releaseinfo['PRETTY_NAME'];

    // Get CPU load
    $cpuload = sys_getloadavg();

    // Get DHCP leases
    $leases = explode(PHP_EOL, trim(file_get_contents('/var/lib/misc/dnsmasq.leases')));
    $dhcpclientnumber = count($leases);

    // Get CPU temperature
    $cputemperature = file_get_contents('/sys/class/thermal/thermal_zone0/temp')/1000 . ' °C';

    // Get CPU frequency
    $cpufrequency = file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')/1000 . ' MHz';

    // Get system uptime
    $rawuptime = intval(file_get_contents('/proc/uptime'));
    $uptime = format_time($rawuptime);

    // Get SD card space and memory used
    $sdcardtotalspace = disk_total_space('/');
    $sdcardfreespace = disk_free_space('/');

    // Get plugin version
    $moodleboxversion = $plugin->release . ' (' . $plugin->version . ')';

    // Get current Wi-Fi WPA password
    $wifiinfo = parse_ini_file('/etc/hostapd/hostapd.conf');
    $currentwifipassword = $wifiinfo['wpa_passphrase'];

    /**
     * Class datetimeset_form
     *
     * Form class to set time and date.
     *
     * @package    tool_moodlebox
     * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class datetimeset_form extends moodleform {

        /**
         * Define the form.
         */
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

    /**
     * Class changepassword_form
     *
     * Form class to change MoodleBox password.
     *
     * @package    tool_moodlebox
     * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class changepassword_form extends moodleform {

        /**
         * Define the form.
         */
        public function definition() {
            $mform = $this->_form;

            $mform->addElement('passwordunmask', 'newpassword1', get_string('newpassword'));
            $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
            $mform->setType('newpassword1', PARAM_RAW);

            $mform->addElement('passwordunmask', 'newpassword2', get_string('newpassword').' ('.get_string('again').')');
            $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
            $mform->setType('newpassword2', PARAM_RAW);

            $this->add_action_buttons(false, get_string('changepassword'));
        }

        /**
         * Validate the form.
         * @param array $data submitted data
         * @param array $files not used
         * @return array errors
         */
        public function validation($data, $files) {
            $errors = array();

            if ($data['newpassword1'] <> $data['newpassword2']) {
                $errors['newpassword1'] = get_string('passwordsdiffer');
                $errors['newpassword2'] = get_string('passwordsdiffer');
            }

            return $errors;
        }
    }

    /**
     * Class wifipassword_form
     *
     * Form class to change MoodleBox Wi-Fi password.
     *
     * @package    tool_moodlebox
     * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class wifipassword_form extends moodleform {

        /**
         * Define the form.
         */
        public function definition() {
            global $currentwifipassword;
            $mform = $this->_form;

            $mform->addElement('static', 'currentwifipassword',
                    get_string('currentwifipassword', 'tool_moodlebox'), $currentwifipassword);
            $mform->addElement('text', 'wifipassword', get_string('newwifipassword', 'tool_moodlebox'));
            $mform->addRule('wifipassword', get_string('required'), 'required', null, 'client');
            $mform->addRule('wifipassword', get_string('wifipassworderror', 'tool_moodlebox'),
                    'rangelength', array(8, 63), 'client');
            $mform->setType('wifipassword', PARAM_RAW);
            $mform->setDefault('wifipassword', $currentwifipassword);

            $this->add_action_buttons(false, get_string('changewifipassword', 'tool_moodlebox'));
        }

    }

    /**
     * Class restartshutdown_form
     *
     * Form class to restart and shutdown the MoodleBox.
     *
     * @package    tool_moodlebox
     * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class restartshutdown_form extends moodleform {

        /**
         * Define the form.
         */
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

    // System information section.
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

    $table->add_data(array(get_string('sdcardavailablespace', 'tool_moodlebox'), display_size($sdcardfreespace) .
            ' (' . 100 * round($sdcardfreespace / $sdcardtotalspace, 3) . '%)'));
    $table->add_data(array(get_string('cpuload', 'tool_moodlebox'),
            $cpuload[0] . ', ' . $cpuload[1] . ', ' . $cpuload[2]));
    $table->add_data(array(get_string('cputemperature', 'tool_moodlebox'), $cputemperature));
    $table->add_data(array(get_string('cpufrequency', 'tool_moodlebox'), $cpufrequency));
    $table->add_data(array(get_string('uptime', 'tool_moodlebox'), $uptime));
    $table->add_data(array(get_string('currentwifipassword', 'tool_moodlebox'), $currentwifipassword));
    $table->add_data(array(get_string('dhcpclientnumber', 'tool_moodlebox'), $dhcpclientnumber));
    if ($dhcpclientnumber > 0) {
        foreach ($leases as $row) {
            $item = explode(' ', $row);
            $table->add_data(array(get_string('dhcpclientinfo', 'tool_moodlebox'),
                    $item[2] . ' (' . $item[3] . ')'), 'dhcpclientinfo');
        }
    }
    $table->add_data(array(get_string('raspberryhardware', 'tool_moodlebox'), get_string($platform, 'tool_moodlebox')));
    $table->add_data(array(get_string('raspbianversion', 'tool_moodlebox'), $raspbianversion));
    $table->add_data(array(get_string('kernelversion', 'tool_moodlebox'), $kernelversion));
    $table->add_data(array(get_string('moodleboxpluginversion', 'tool_moodlebox'), $moodleboxversion));

    $table->print_html();

    echo $OUTPUT->box_end();

    // Time setting section.
    echo $OUTPUT->heading(get_string('datetimesetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $datetimetriggerfilename = ".set-server-datetime";

    if (file_exists($datetimetriggerfilename)) {
        $datetimesetform = new datetimeset_form();
        $datetimesetform->display();

        if ($data = $datetimesetform->get_data()) {
            if (!empty($data->submitbutton)) {
                $datecommand = "date +%s -s @$data->currentdatetime";
                file_put_contents($datetimetriggerfilename, "date +%s -s @$data->currentdatetime");
                \core\notification::warning(get_string('datetimemessage', 'tool_moodlebox'));
            }
        }
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();

    // Change password section.
    echo $OUTPUT->heading(get_string('changepasswordsetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $changepasswordtriggerfilename = ".newpassword";

    if (file_exists($changepasswordtriggerfilename)) {
        $changepasswordform = new changepassword_form();
        $changepasswordform->display();

        if ($data = $changepasswordform->get_data()) {
            if (!empty($data->submitbutton)) {
                file_put_contents($changepasswordtriggerfilename, $data->newpassword1);
                \core\notification::warning(get_string('changepasswordmessage', 'tool_moodlebox'));
            }
        } else if ($changepasswordform->is_submitted()) { // Validation failed.
            \core\notification::error(get_string('changepassworderror', 'tool_moodlebox'));
        }
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();

    // Wi-Fi password section.
    echo $OUTPUT->heading(get_string('wifipasswordsetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $wifipasswordtriggerfilename = ".wifipassword";

    if (file_exists($wifipasswordtriggerfilename)) {
        $wifipasswordform = new wifipassword_form();
        $wifipasswordform->display();

        if ($data = $wifipasswordform->get_data()) {
            if (!empty($data->submitbutton)) {
                file_put_contents($wifipasswordtriggerfilename, $data->wifipassword);
                \core\notification::warning(get_string('wifipasswordmessage', 'tool_moodlebox'));
            }
        }
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();

    // Restart-shutdown section.
    echo $OUTPUT->heading(get_string('restartstop', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $reboottriggerfilename = ".reboot-server";
    $shutdowntriggerfilename = ".shutdown-server";

    if (file_exists($reboottriggerfilename) and file_exists($shutdowntriggerfilename)) {
        $restartshutdownform = new restartshutdown_form(null, null, 'post', '', array('id' => 'formrestartstop'));
        $restartshutdownform->display();

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
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();
} else { // We're not on a Raspberry Pi.
    \core\notification::error(get_string('unsupportedhardware', 'tool_moodlebox'));
}

echo $OUTPUT->footer();
