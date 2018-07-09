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
 * Provides a dashboard of some hardware settings of the MoodleBox, and
 * a GUI for several tools to manage the MoodleBox:
 *  - set the date and time,
 *  - set the main password,
 *  - set the WLAN settings, including SSID and password,
 *  - resize the microSD card partition,
 *  - restart and shutdown.
 *
 * @see        https://github.com/moodlebox/moodle-tool_moodlebox
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

$hardwaredata = \tool_moodlebox\local\utils::get_hardware_model();

switch ( $hardwaredata['model'] ) {
    case 'A+':
    case 'B+':
        $platform = 'rpi1';
        break;
    case '2B':
        $platform = 'rpi2';
        break;
    case '3B':
        $platform = 'rpi3';
        break;
    case '3B+':
        $platform = 'rpi3bplus';
        break;
    case 'ZeroW':
        $platform = 'rpizerow';
        break;
    default: // Anything else is not a RPi.
        $platform = 'unknownmodel';
}

if ( strpos($platform, 'rpi') !== false ) { // We are on a RPi.

    $systemtime = usergetdate(time())[0];
    $PAGE->requires->js_call_amd('tool_moodlebox/timediff', 'init', array($systemtime));

    // Get kernel version.
    $kernelversion = php_uname('s') . ' ' . php_uname('r') . ' ' .  php_uname('m');

    // Get Raspbian distribution version.
    $releaseinfo = \tool_moodlebox\local\utils::parse_config_file('/etc/os-release');
    $raspbianversion = $releaseinfo['PRETTY_NAME'];

    // Get CPU load.
    $cpuload = sys_getloadavg();

    // Get DHCP leases.
    if (filesize('/var/lib/misc/dnsmasq.leases') > 0) {
        $leases = explode(PHP_EOL, trim(file_get_contents('/var/lib/misc/dnsmasq.leases')));
    } else {
        $leases = null;
    }
    $dhcpclientnumber = count($leases);

    // Get CPU temperature.
    $cputemperature = file_get_contents('/sys/class/thermal/thermal_zone0/temp') / 1000 . ' °C';

    // Get CPU frequency.
    $cpufrequency = file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq') / 1000 . ' MHz';

    // Get system uptime.
    $rawuptime = intval(file_get_contents('/proc/uptime'));
    $uptime = format_time($rawuptime);

    // Get SD card space and memory used.
    $sdcardtotalspace = disk_total_space('/');
    $sdcardfreespace = disk_free_space('/');

    // Get plugin version.
    $moodleboxpluginversion = $plugin->release . ' (' . $plugin->version . ')';

    // Get MoodleBox image version and date.
    $moodleboxinfo = null;
    $moodleboxinfofile = '/etc/moodlebox-info';
    if ( file_exists($moodleboxinfofile) ) {
        $moodleboxinfo = file($moodleboxinfofile);
        if ( preg_match_all('/^.*version ((\d+\.)+(.*|\d+)), (\d{4}-\d{2}-\d{2})$/i',
                $moodleboxinfo[0], $moodleboxinfomatch) > 0 ) {
            $moodleboxinfo = $moodleboxinfomatch[1][0] . ' (' . $moodleboxinfomatch[4][0] . ')';
        }
    } else {
        $moodleboxinfo = get_string('moodleboxinfofileerror', 'tool_moodlebox');
    }

    // Get current Wi-Fi SSID, channel and password.
    $wifiinfo = \tool_moodlebox\local\utils::parse_config_file('/etc/hostapd/hostapd.conf', false, INI_SCANNER_RAW);

    $currentwifichannel = $wifiinfo['channel'];
    if ( array_key_exists('ssid', $wifiinfo) ) {
        $currentwifissid = $wifiinfo['ssid'];
    } else {
        $currentwifissid = $wifiinfo['ssid2'];
        // Convert $currentwifissid from hex. See https://stackoverflow.com/a/46344675.
        $currentwifissid = pack("H*", $currentwifissid);
    }
    $currentwifipassword = array_key_exists('wpa_passphrase', $wifiinfo) ? $wifiinfo['wpa_passphrase'] : null;
    $currentwificountry = $wifiinfo['country_code'];

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
            $mform->setType('newpassword1', PARAM_RAW_TRIMMED);

            $mform->addElement('passwordunmask', 'newpassword2', get_string('newpassword').' ('.get_string('again').')');
            $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
            $mform->setType('newpassword2', PARAM_RAW_TRIMMED);

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
     * Class wifisettings_form
     *
     * Form class to change MoodleBox Wi-Fi settings.
     *
     * @package    tool_moodlebox
     * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class wifisettings_form extends moodleform {

        /**
         * Define the form.
         */
        public function definition() {
            global $currentwifissid;
            global $currentwifichannel;
            global $currentwifipassword;
            global $currentwificountry;

            $mform = $this->_form;

            $mform->addElement('text', 'wifissid', get_string('wifissid', 'tool_moodlebox'));
            $mform->addRule('wifissid', get_string('required'), 'required', null, 'client');
            $mform->setType('wifissid', PARAM_RAW_TRIMMED);
            $mform->setDefault('wifissid', $currentwifissid);

            if ($currentwificountry == 'US' or $currentwificountry == 'CA') {
                $wifichannelrange = range(1, 11);
            } else {
                $wifichannelrange = range(1, 13);
            }
            $mform->addElement('select', 'wifichannel', get_string('wifichannel', 'tool_moodlebox'),
                    array_combine($wifichannelrange, $wifichannelrange));
            $mform->addRule('wifichannel', get_string('required'), 'required', null, 'client');
            $mform->setType('wifichannel', PARAM_INT);
            $mform->setDefault('wifichannel', $currentwifichannel);

            $mform->addElement('checkbox', 'wifipasswordon', get_string('wifipasswordon', 'tool_moodlebox'),
                ' ' . get_string('wifipasswordonhelp', 'tool_moodlebox'));
            $mform->setDefault('wifipasswordon', ($currentwifipassword == null) ? 0 : 1);
            $mform->setType('wifipasswordon', PARAM_INT);

            $mform->addElement('text', 'wifipassword', get_string('wifipassword', 'tool_moodlebox'));
            $mform->disabledIf('wifipassword', 'wifipasswordon');
            $mform->setType('wifipassword', PARAM_RAW_TRIMMED);
            $mform->setDefault('wifipassword', ($currentwifipassword == null) ? 'moodlebox' : $currentwifipassword);

            $this->add_action_buttons(false, get_string('changewifisettings', 'tool_moodlebox'));
        }

    }

    /**
     * Class resizepartition_form
     *
     * Form class to resize the partition of the MoodleBox.
     *
     * @package    tool_moodlebox
     * @copyright  2018 onwards Nicolas Martignoni <nicolas@martignoni.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class resizepartition_form extends moodleform {

        /**
         * Define the form.
         */
        public function definition() {
            $mform = $this->_form;
            $buttonarray = array();
            $buttonarray[] = & $mform->createElement('submit', 'resizepartitionbutton',
                                                      get_string('resizepartition', 'tool_moodlebox'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
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
    $table->add_data(array(get_string('wifisettings', 'tool_moodlebox'), ''));
    $table->add_data(array(get_string('wifissid', 'tool_moodlebox'), $currentwifissid), 'subinfo');
    $table->add_data(array(get_string('wifichannel', 'tool_moodlebox'), $currentwifichannel), 'subinfo');
    $table->add_data(array(get_string('wificountry', 'tool_moodlebox'), $currentwificountry), 'subinfo');
    $table->add_data(array(get_string('wifipassword', 'tool_moodlebox'), $currentwifipassword), 'subinfo');
    if ($dhcpclientnumber > 0) {
        $table->add_data(array(get_string('dhcpclients', 'tool_moodlebox') .
                ' (' . get_string('dhcpclientnumber', 'tool_moodlebox') . ': ' . $dhcpclientnumber . ')', ''));
        foreach ($leases as $row) {
            $item = explode(' ', $row);
            $table->add_data(array(get_string('dhcpclientinfo', 'tool_moodlebox'),
                    $item[2] . ' (' . $item[3] . ')'), 'subinfo');
        }
    }
    $table->add_data(array(get_string('raspberryhardware', 'tool_moodlebox'), get_string($platform, 'tool_moodlebox')));
    $table->add_data(array(get_string('raspbianversion', 'tool_moodlebox'), $raspbianversion));
    $table->add_data(array(get_string('kernelversion', 'tool_moodlebox'), $kernelversion));
    $table->add_data(array(get_string('moodleboxinfo', 'tool_moodlebox'), $moodleboxinfo));
    $table->add_data(array(get_string('moodleboxpluginversion', 'tool_moodlebox'), $moodleboxpluginversion));

    $table->print_html();

    echo $OUTPUT->box_end();

    // Time setting section.
    echo $OUTPUT->heading(get_string('datetimesetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $datetimetriggerfilename = '.set-server-datetime';

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
    echo $OUTPUT->heading(get_string('passwordsetting', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $changepasswordtriggerfilename = '.newpassword';

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

    // Wi-Fi configuration section.
    echo $OUTPUT->heading(get_string('wifisettings', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $wifipasswordtriggerfilename = '.wifisettings';

    if (file_exists($wifipasswordtriggerfilename)) {
        $wifisettingsform = new wifisettings_form();
        $wifisettingsform->display();

        if ($data = $wifisettingsform->get_data()) {
            if (!empty($data->submitbutton)) {
                if (!isset($data->wifipasswordon)) {
                    $data->wifipasswordon = 0;
                }
                if (!isset($data->wifipassword)) {
                    $data->wifipassword = null;
                }
                // Convert $data->wifissid to hex. See https://stackoverflow.com/a/46344675.
                $data->wifissid = implode(unpack("H*", $data->wifissid));
                file_put_contents($wifipasswordtriggerfilename,
                                  "channel=" . $data->wifichannel . "\n" .
                                  "password=" . $data->wifipassword . "\n" .
                                  "ssid=" . $data->wifissid . "\n" .
                                  "passwordprotected=" . $data->wifipasswordon . "\n");
                \core\notification::warning(get_string('wifisettingsmessage', 'tool_moodlebox'));
            }
        }
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();

    // Resize partition section
    echo $OUTPUT->heading(get_string('resizepartition', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $resizepartitiontriggerfilename = '.resize-partition';

    if (file_exists($resizepartitiontriggerfilename)) {
        $resizepartitionform = new resizepartition_form(null, null, 'post', '', array('id' => 'formresizepartition'));
        $resizepartitionform->display();

        if ($data = $resizepartitionform->get_data()) {
            if (!empty($data->resizepartitionbutton)) {
                file_put_contents($resizepartitiontriggerfilename, 'Resize partition');
                \core\notification::warning(get_string('resizepartitionmessage', 'tool_moodlebox'));
            }
        }
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();

    // Restart-shutdown section.
    echo $OUTPUT->heading(get_string('restartstop', 'tool_moodlebox'));
    echo $OUTPUT->box_start('generalbox');

    $reboottriggerfilename = '.reboot-server';
    $shutdowntriggerfilename = '.shutdown-server';

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
