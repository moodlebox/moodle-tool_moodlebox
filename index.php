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
 * @link       https://github.com/moodlebox/moodle-tool_moodlebox
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot.'/admin/tool/moodlebox/forms.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/adminlib.php');
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
    // String $platform MUST contain 'rpi'.
    case 'A+':
    case 'B+':
        $platform = 'rpi1';
        break;
    case '2B':
        $platform = 'rpi2';
        break;
    case '3A+':
        $platform = 'rpi3aplus';
        break;
    case '3B':
        $platform = 'rpi3';
        break;
    case '3B+':
        $platform = 'rpi3bplus';
        break;
    case '4B':
        switch ($hardwaredata['memory']) {
            case '1 GB':
                $platform = 'rpi4onegb';
                break;
            case '2 GB':
                $platform = 'rpi4twogb';
                break;
            case '4 GB':
                $platform = 'rpi4fourgb';
                break;
            case '8 GB':
                $platform = 'rpi4eightgb';
                break;
        };
        break;
    case 'ZeroW':
        $platform = 'rpizerow';
        break;
    case '400':
        $platform = 'rpi400';
        break;
    default: // Anything else is not a RPi.
        $platform = 'unknownmodel';
}

if ( strpos($platform, 'rpi') !== false ) { // We are on a RPi.

    $systemtime = usergetdate(time())[0];
    $PAGE->requires->js_call_amd('tool_moodlebox/timediff', 'init', array($systemtime));

    // Get kernel version.
    $kernelversion = php_uname('s') . ' ' . php_uname('r') . ' ' .  php_uname('m');

    // Get Raspberry Pi OS distribution version.
    $releaseinfo = \tool_moodlebox\local\utils::parse_config_file('/etc/os-release');
    $rpiosversion = $releaseinfo['PRETTY_NAME'];

    // Get CPU load.
    $cpuload = sys_getloadavg();

    // Get DHCP leases.
    $moodleboxleasesfile = '/var/lib/misc/dnsmasq.leases';
    if ( file_exists($moodleboxleasesfile) ) {
        if ( filesize($moodleboxleasesfile) > 0 ) {
            $leases = explode(PHP_EOL, trim(file_get_contents($moodleboxleasesfile)));
        } else {
            $leases = array();
        }
        $dhcpclientnumber = count($leases);
    } else {
        $dhcpclientnumber = 0;
    }

    // Get local static IP address.
    $staticipaddress = getHostByName(getHostName());

    // Get CPU temperature.
    $cputemperature = intval(file_get_contents('/sys/class/thermal/thermal_zone0/temp')) / 1000 . ' °C';

    // Get CPU frequency.
    $cpufrequency = intval(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) / 1000 . ' MHz';

    // Get system uptime.
    $rawuptime = intval(file_get_contents('/proc/uptime'));
    $uptime = format_time($rawuptime);

    // Get SD card space and memory used.
    $sdcardtotalspace = disk_total_space('/');
    $sdcardfreespace = disk_free_space('/');

    // Check if PiJuice package is installed.
    $pijuicepackage = exec('dpkg-query -W -f=\'${Status}\' pijuice-base 2>/dev/null | grep -c "ok installed"');

    // Get PiJuice Status.
    if ($pijuicepackage) {
        $pijuicestatusjson = exec('bin/pijuicestatus.py');
        $pijuicestatus = json_decode($pijuicestatusjson, true);
    }

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
        $moodleboxinfo = get_string('infofileerror', 'tool_moodlebox');
    }

    // Get current Wi-Fi SSID, channel and password.
    $wifiinfo = \tool_moodlebox\local\utils::parse_config_file('/etc/hostapd/hostapd.conf', false, INI_SCANNER_RAW);

    $currentwifichannel = $wifiinfo['channel'];
    if ( array_key_exists('ssid', $wifiinfo) ) {
        $currentwifissid = $wifiinfo['ssid'];
    } else {
        $currentwifissid = $wifiinfo['ssid2'];
        // Convert $currentwifissid from hex {@link https://stackoverflow.com/a/46344675}.
        $currentwifissid = pack("H*", $currentwifissid);
    }
    $currentwifipassword = array_key_exists('wpa_passphrase', $wifiinfo) ? $wifiinfo['wpa_passphrase'] : null;
    $currentwificountry = $wifiinfo['country_code'];
    $currentwifissidhiddenstate = array_key_exists('ignore_broadcast_ssid', $wifiinfo) ? $wifiinfo['ignore_broadcast_ssid'] : '0';
    if ( $currentwifissidhiddenstate === '0') {
        // SSID is visible.
        $currentwifissidhiddenstate = 0;
    } else {
        // SSID is hidden.
        $currentwifissidhiddenstate = 1;
    }

    // Get ethernet addresses.
    if ( $ethernetaddresses = \tool_moodlebox\local\utils::get_ethernet_addresses() ) {
        $ipaddress = $ethernetaddresses['host'];
        $defaultgatewayaddress = $ethernetaddresses['gateway'];
    }

    // System information section.
    print_collapsible_region_start('moodleboxsysteminfo', 'moodleboxsysteminfo',
        get_string('moodleboxsysteminfo', 'tool_moodlebox') .
            $OUTPUT->help_icon('moodleboxsysteminfo', 'tool_moodlebox'), 'moodleboxsysteminfosection');
    echo $OUTPUT->box_start('generalbox');

    $table = new flexible_table('moodleboxstatus_table');
    $table->define_columns(array('parameter', 'information'));
    $table->define_headers(array(get_string('parameter', 'tool_moodlebox'), get_string('information', 'tool_moodlebox')));
    $table->define_baseurl($PAGE->url);
    $table->column_style_all('width', '50%');
    $table->set_attribute('id', 'moodleboxstatus');
    $table->set_attribute('class', 'admintable environmenttable generaltable');
    $table->setup();

    // Wireless info.
    $table->add_data(array(get_string('wifisettings', 'tool_moodlebox'), ''));
    $table->add_data(array(get_string('wifissid', 'tool_moodlebox'), $currentwifissid), 'subinfo');
    $table->add_data(array(get_string('wifissidhiddenstate', 'tool_moodlebox'),
            ($currentwifissidhiddenstate == 0) ?
                get_string('visible', 'tool_moodlebox') : get_string('hidden', 'tool_moodlebox')), 'subinfo');
    $table->add_data(array(get_string('wifichannel', 'tool_moodlebox'), $currentwifichannel), 'subinfo');
    $table->add_data(array(get_string('wificountry', 'tool_moodlebox'), $currentwificountry), 'subinfo');
    $table->add_data(array(get_string('wifipassword', 'tool_moodlebox'), $currentwifipassword), 'subinfo');
    $table->add_data(array(get_string('staticipaddress', 'tool_moodlebox'), $staticipaddress), 'subinfo');

    // DHCP client info.
    if ($dhcpclientnumber > 0) {
        $table->add_data(array(get_string('dhcpclients', 'tool_moodlebox') .
                ' (' . get_string('dhcpclientnumber', 'tool_moodlebox') . ': ' . $dhcpclientnumber . ')', ''));
        foreach ($leases as $row) {
            $item = explode(' ', $row);
            $table->add_data(array(get_string('dhcpclientinfo', 'tool_moodlebox'),
                    $item[2] . ' (' . $item[3] . ')'), 'subinfo');
        }
    }
    // Ethernet info.
    if ($ethernetaddresses) {
        $table->add_data(array(get_string('networkinterface', 'tool_moodlebox'), ''));
        $table->add_data(array(get_string('interfacename', 'tool_moodlebox'),
                \tool_moodlebox\local\utils::get_ethernet_interface_name()), 'subinfo');
        $table->add_data(array(get_string('dynamicipaddress', 'tool_moodlebox'), $ipaddress), 'subinfo');
        $table->add_data(array(get_string('defaultgateway', 'tool_moodlebox'), $defaultgatewayaddress), 'subinfo');
    } else {
        $table->add_data(array(get_string('networkinterface', 'tool_moodlebox'),
                get_string('ethernetdisconnected', 'tool_moodlebox')));
    }
    // PiJuice info.
    if ($pijuicepackage) {
        $table->add_data(array(get_string('pijuiceinfo', 'tool_moodlebox'), ''));
        if ($pijuicestatus['status_error'] == "NO_ERROR") {
            $table->add_data(array(get_string('pijuicebatterychargelevel', 'tool_moodlebox'),
                    $pijuicestatus['charge_level'] . '%'), 'subinfo');
            $table->add_data(array(get_string('pijuicebatterystatus', 'tool_moodlebox'),
                    $pijuicestatus['battery_status']), 'subinfo');
            $table->add_data(array(get_string('pijuicebatterytemp', 'tool_moodlebox'),
                    $pijuicestatus['battery_temp'] . '°C'), 'subinfo');
        } else {
            $table->add_data(array(get_string('pijuicestatuserror', 'tool_moodlebox'),
                    $pijuicestatus['status_error']), 'subinfo');
        }
        $table->add_data(array(get_string('pijuiceisfault', 'tool_moodlebox'),
                $pijuicestatus['is_fault']), 'subinfo');
    }
    // System info.
    $table->add_data(array(get_string('systeminfo', 'tool_moodlebox'), ''));
    $table->add_data(array(get_string('raspberryhardware', 'tool_moodlebox'),
            get_string($platform, 'tool_moodlebox')), 'subinfo');
    $table->add_data(array(get_string('sdcardavailablespace', 'tool_moodlebox'), display_size($sdcardfreespace) .
            ' (' . 100 * round($sdcardfreespace / $sdcardtotalspace, 3) . '%)'), 'subinfo');
    $table->add_data(array(get_string('cpuload', 'tool_moodlebox'),
            $cpuload[0] . ', ' . $cpuload[1] . ', ' . $cpuload[2]), 'subinfo');
    $table->add_data(array(get_string('cputemperature', 'tool_moodlebox'), $cputemperature), 'subinfo');
    $table->add_data(array(get_string('cpufrequency', 'tool_moodlebox'), $cpufrequency), 'subinfo');
    $table->add_data(array(get_string('uptime', 'tool_moodlebox'), $uptime), 'subinfo');
    // Software versions.
    $table->add_data(array(get_string('softwareversions', 'tool_moodlebox'), ''));
    $table->add_data(array(get_string('rpiosversion', 'tool_moodlebox'), $rpiosversion), 'subinfo');
    $table->add_data(array(get_string('kernelversion', 'tool_moodlebox'), $kernelversion), 'subinfo');
    $table->add_data(array(get_string('version', 'tool_moodlebox'), $moodleboxinfo), 'subinfo');
    $table->add_data(array(get_string('pluginversion', 'tool_moodlebox'), $moodleboxpluginversion), 'subinfo');
    $table->add_data(array(get_string('moodleversion'), $CFG->release), 'subinfo');

    $table->print_html();

    echo $OUTPUT->box_end();
    print_collapsible_region_end();

    // Time setting section.
    print_collapsible_region_start('datetimesetting', 'datetimesetting',
        get_string('datetimesetting', 'tool_moodlebox'), 'moodleboxdatetimesection');
    echo $OUTPUT->box_start('generalbox');

    $datetimetriggerfilename = '.set-server-datetime';

    if (file_exists($datetimetriggerfilename)) {
        $datetimesetform = new datetimeset_form();

        if ($data = $datetimesetform->get_data()) {
            if (!empty($data->submitbutton)) {
                $datecommand = "date +%s -s @$data->currentdatetime";
                file_put_contents($datetimetriggerfilename, "date +%s -s @$data->currentdatetime");
                \core\notification::warning(get_string('datetimemessage', 'tool_moodlebox'));
            }
        }
        $datetimesetform->display();
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();
    print_collapsible_region_end();

    // Change password section.
    print_collapsible_region_start('passwordsetting', 'passwordsetting',
        get_string('passwordsetting', 'tool_moodlebox') .
            $OUTPUT->help_icon('passwordsetting', 'tool_moodlebox'), 'moodleboxpasswordsection');
    echo $OUTPUT->box_start('generalbox');

    $changepasswordtriggerfilename = '.newpassword';

    if (file_exists($changepasswordtriggerfilename)) {
        $changepasswordform = new changepassword_form();

        if ($data = $changepasswordform->get_data()) {
            if (!empty($data->submitbutton)) {
                file_put_contents($changepasswordtriggerfilename, $data->newpassword1);
                \core\notification::warning(get_string('changepasswordmessage', 'tool_moodlebox'));
            }
        }
        $changepasswordform->display();
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();
    print_collapsible_region_end();

    // Wi-Fi configuration section.
    print_collapsible_region_start('wifisettings', 'wifisettings',
        get_string('wifisettings', 'tool_moodlebox'), 'moodleboxwifisection');
    echo $OUTPUT->box_start('generalbox');

    $wifisettingstriggerfilename = '.wifisettings';

    if (file_exists($wifisettingstriggerfilename)) {
        $wifisettingsform = new wifisettings_form();

        if ($data = $wifisettingsform->get_data()) {
            if (!empty($data->submitbutton)) {
                if (!isset($data->wifipasswordon)) {
                    $data->wifipasswordon = 0;
                }
                if (!isset($data->wifipassword)) {
                    $data->wifipassword = null;
                }
                if (!isset($data->wifissidhiddenstate)) {
                    $data->wifissidhiddenstate = 0;
                }
                if (!isset($data->staticipaddress)) {
                    $data->staticipaddress = $staticipaddress;
                }
                // Convert $data->wifissid to hex {@link https://stackoverflow.com/a/46344675}.
                $data->wifissid = implode(unpack("H*", $data->wifissid));
                file_put_contents($wifisettingstriggerfilename,
                                  "channel=" . $data->wifichannel . "\n" .
                                  "country=" . $data->wificountry . "\n" .
                                  "password=" . $data->wifipassword . "\n" .
                                  "ssid=" . $data->wifissid . "\n" .
                                  "ssidhiddenstate=" . $data->wifissidhiddenstate . "\n" .
                                  "passwordprotected=" . $data->wifipasswordon . "\n" .
                                  "ipaddress=" . $data->staticipaddress . "\n");
                \core\notification::warning(get_string('wifisettingsmessage', 'tool_moodlebox'));
            }
        }
        $wifisettingsform->display();
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();
    print_collapsible_region_end();

    // Resize partition section.
    // We display this section only when enough free size is present on the SD card.
    $unallocatedfreespace = \tool_moodlebox\local\utils::unallocated_free_space();

    if ($unallocatedfreespace) {
        print_collapsible_region_start('resizepartition', 'resizepartition',
            get_string('resizepartition', 'tool_moodlebox') .
                $OUTPUT->help_icon('resizepartition', 'tool_moodlebox'), 'moodleboxresizepartitionsection');
        echo $OUTPUT->box_start('generalbox');

        $resizepartitiontriggerfilename = '.resize-partition';

        if (file_exists($resizepartitiontriggerfilename)) {
            $resizepartitionform = new resizepartition_form(null, null, 'post', '', array('id' => 'formresizepartition'));

            if ($data = $resizepartitionform->get_data()) {
                if (!empty($data->resizepartitionbutton)) {
                    file_put_contents($resizepartitiontriggerfilename, 'Resize partition');
                    \core\notification::warning(get_string('resizepartitionmessage', 'tool_moodlebox'));
                }
            }
            $resizepartitionform->display();
        } else {
            echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
        }

        echo $OUTPUT->box_end();
        print_collapsible_region_end();
    }

    // Restart-shutdown section.
    print_collapsible_region_start('restartstop', 'restartstop',
        get_string('restartstop', 'tool_moodlebox') .
            $OUTPUT->help_icon('restartstop', 'tool_moodlebox'), 'moodleboxrestartstopsection');
    echo $OUTPUT->box_start('generalbox');

    $reboottriggerfilename = '.reboot-server';
    $shutdowntriggerfilename = '.shutdown-server';

    if (file_exists($reboottriggerfilename) and file_exists($shutdowntriggerfilename)) {
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
        $restartshutdownform->display();
    } else {
        echo $OUTPUT->notification(get_string('missingconfigurationerror', 'tool_moodlebox'));
    }

    echo $OUTPUT->box_end();
    print_collapsible_region_end();
} else { // We're not on a Raspberry Pi.
    \core\notification::error(get_string('unsupportedhardware', 'tool_moodlebox'));
}

echo $OUTPUT->footer();
