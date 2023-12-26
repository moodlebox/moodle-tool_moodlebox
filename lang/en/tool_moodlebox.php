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
 * Strings for component 'tool_moodlebox', language 'en' (English)
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['changepassworderror'] = 'The MoodleBox password was not changed. The passwords given don\'t match.';
$string['changepasswordmessage'] = 'The main password of the MoodleBox (Unix account) and of the database server were successfully changed.<br /><br />Warning! The password of the default Moodle user <b>was not changed</b>. To change it, please use the preferences page of this user.';
$string['changewifisettings'] = 'Change Wi-Fi settings';
$string['configuration'] = 'MoodleBox settings';
$string['cpufrequency'] = 'CPU frequency';
$string['cpuload'] = 'CPU load';
$string['cputemperature'] = 'CPU temperature';
$string['dashboard'] = 'MoodleBox dashboard';
$string['datetime'] = 'Date and time';
$string['datetime_help'] = 'If the MoodleBox is not connected to the Internet, it will not be on time. It can be set manually using this setting.';
$string['datetimemessage'] = 'The clock of the MoodleBox was set. To get the most accuracy, it\'s recommended to connect the MoodleBox to an Internet connected network via an ethernet cable.';
$string['datetimeset'] = 'Set date and time';
$string['datetimesetmessage'] = 'The clock of the MoodleBox isn\'t on time. It\'s highly recommended to set the date and time to the current time.';
$string['datetimesetting'] = 'Date and time';
$string['defaultgateway'] = 'Default gateway';
$string['dhcpclientinfo'] = 'Client IP and MAC addresses';
$string['dhcpclientnumber'] = 'number of clients';
$string['dhcpclients'] = 'DHCP clients';
$string['documentation'] = 'MoodleBox documentation';
$string['documentation_desc'] = '<p>For support questions, browse the complete <a href="https://moodlebox.net/en/help/" title="MoodleBox documentation" target="_blank">MoodleBox documentation</a>.</p>';
$string['dynamicipaddress'] = 'IP address (dynamic)';
$string['ethernetdisconnected'] = 'Ethernet disconnected';
$string['forum'] = 'MoodleBox support forum';
$string['forum_desc'] = '<p>If you can\'t find an answer to your question in the <a href="https://moodlebox.net/en/help/" title="MoodleBox documentation" target="_blank">MoodleBox documentation</a>, search the <a href="https://discuss.moodlebox.net/" title="MoodleBox forum" target="_blank">MoodleBox support forum</a> to see if your question has already been answered. Otherwise, feel free to open a new discussion.</p>';
$string['hardwareinfo'] = 'Hardware information';
$string['hidden'] = 'Hidden';
$string['infofileerror'] = 'Information not available';
$string['infoheading'] = 'MoodleBox support information';
$string['information'] = 'Information';
$string['ihavedonated'] = 'I have donated! 🎉';
$string['ihavedonated_desc'] = 'Check this box if <a href="https://moodlebox.net/en/donate/" title="Make a donation" target="_blank">you\'ve donated</a> to the MoodleBox project.<br />This setting has no effect at all. It simply allows you to show your pride in having contributed to the <a href="https://moodlebox.net/en/" title="MoodleBox website" target="_blank">MoodleBox project</a>. Many thanks!';
$string['interfacename'] = 'Interface name';
$string['kernelversion'] = 'Kernel version';
$string['missingconfigurationerror'] = 'This section isn\'t available. The plugin installation is not complete, so that the setting cannot be handled by the MoodleBox. Please read the <a href="https://github.com/moodlebox/moodle-tool_moodlebox/blob/master/README.md" target="_blank">installation documentation</a> to fix this error.';
$string['moodlebox:viewbuttonsinfooter'] = 'View restart and shutdown buttons in footer';
$string['moodleboxsysteminfo'] = 'MoodleBox information';
$string['moodleboxsysteminfo_help'] = 'The MoodleBox information dashboard displays several important data about the MoodleBox. This info includes:

* Critical MoodleBox operation details, such as remaining disk space on the SD card and processor load, temperature and frequency
* Current settings of Wi-Fi network supplied by the MoodleBox
* Number, IP and MAC addresses of all devices connected to the MoodleBox
* Raspberry Pi model and operating system
* MoodleBox version and MoodleBox plugin version
';
$string['networkinterface'] = 'Cabled network interface';
$string['parameter'] = 'Parameter';
$string['passwordprotected'] = 'Password protected';
$string['passwordsetting'] = 'MoodleBox password';
$string['passwordsetting_help'] = 'MoodleBox main password can be changed here. __It is strongly discouraged to keep the default password__. Your __must__ definitely change it as minimal security measure.';
$string['pijuicebatterychargelevel'] = 'PiJuice charge level';
$string['pijuicebatterystatus'] = 'PiJuice battery status';
$string['pijuicebatterytemp'] = 'PiJuice battery temperature';
$string['pijuiceinfo'] = 'PiJuice status info';
$string['pijuiceisfault'] = 'PiJuice fault';
$string['pijuicestatuserror'] = 'PiJuice status';
$string['pluginname'] = 'MoodleBox';
$string['pluginversion'] = 'MoodleBox plugin version';
$string['privacy:metadata'] = 'The MoodleBox plugin displays information from the Raspberry Pi and enables some configuration changes, but does not affect or store any personal data itself.';
$string['projectinfo'] = '<p>The <a href="https://moodlebox.net/en/" title="MoodleBox website" target="_blank">MoodleBox project</a> is a volunteer, non-profit and open source project carried out by <a href="https://blog.martignoni.net/a-propos/" title="Nicolas Martignoni" target="_blank">Nicolas Martignoni</a> in his spare time.</p><p>We thank you for using MoodleBox. You can show your appreciation and support this project by <a href="https://moodlebox.net/en/donate/" title="Make a donation" target="_blank">making a donation</a> ❤. Your donation will help fund the equipment needed to develop the MoodleBox and host its documentation.</p>';
$string['resizepartition'] = 'Resize SD card partition';
$string['resizepartition_help'] = 'Use this button to resize the SD card partition.';
$string['resizepartitionmessage'] = 'The SD card partition has been resized to its maximal size. The MoodleBox is restarting now. It will be online again in a moment.';
$string['resizepartitionsetting'] = 'SD card partition resizing';
$string['raspberryhardware'] = 'Raspberry Pi model';
$string['restart'] = 'Restart MoodleBox';
$string['restartmessage'] = 'The MoodleBox is restarting. It will be online again in a moment.';
$string['restartstop'] = 'Restart and shutdown';
$string['restartstop_help'] = 'Use these buttons to restart or turn off the MoodleBox. It is highly recommended that you do not unplug the power supply to shutdown the MoodleBox.';
$string['revision'] = 'Model revision';
$string['revisioncode'] = 'Model revision code';
$string['rpi1'] = 'Raspberry Pi 1';
$string['rpi2'] = 'Raspberry Pi 2B';
$string['rpi3aplus'] = 'Raspberry Pi 3A+';
$string['rpi3b'] = 'Raspberry Pi 3B';
$string['rpi3bplus'] = 'Raspberry Pi 3B+';
$string['rpi400'] = 'Raspberry Pi 400';
$string['rpi4onegb'] = 'Raspberry Pi 4B (1GB RAM)';
$string['rpi4twogb'] = 'Raspberry Pi 4B (2GB RAM)';
$string['rpi4fourgb'] = 'Raspberry Pi 4B (4GB RAM)';
$string['rpi4eightgb'] = 'Raspberry Pi 4B (8GB RAM)';
$string['rpi5fourgb'] = 'Raspberry Pi 5 (4GB RAM)';
$string['rpi5eightgb'] = 'Raspberry Pi 5 (8GB RAM)';
$string['rpiosversion'] = 'Raspberry Pi OS version';
$string['rpizero2w'] = 'Raspberry Pi Zero 2 W';
$string['rpizerow'] = 'Raspberry Pi Zero W';
$string['sdcardavailablespace'] = 'Free space on SD card';
$string['showdatetimebuttonsinfooter'] = 'Show date and time setting in footer';
$string['showdatetimebuttonsinfooter_desc'] = 'If enabled, the date and time setting are displayed in the footer of all pages of the site when logged in as an administrator or as a manager.';
$string['showrestartshutdownbuttonsinfooter'] = 'Show restart and shutdown buttons in footer';
$string['showrestartshutdownbuttonsinfooter_desc'] = 'If enabled, the restart and shutdown buttons are displayed in the footer of all pages of the site when logged in as an administrator or as a manager.';
$string['shutdown'] = 'Shutdown MoodleBox';
$string['shutdownmessage'] = 'The MoodleBox is shutting down. Please wait a few seconds before disconnecting the power supply.';
$string['softwareversions'] = 'Software versions';
$string['staticipaddress'] = 'IP address (static)';
$string['staticipaddress_help'] = 'This is the static IP address of the MoodleBox Wi-Fi access point. It also defines the DHCP range of IP addresses given to Wi-Fi clients. It must be a valid <strong>private</strong> IP address, e.g. "10.10.1.1", "172.23.222.1" or "192.168.222.1". Its last segment will be forced to 1.';
$string['staticipaddressinvalid'] = 'This IP address is invalid. It must be a valid <strong>private</strong> IP address, e.g. "10.10.1.1", "172.23.222.1" or "192.168.222.1".';
$string['systeminfo'] = 'System information';
$string['undervoltagedetected'] = '<p><b>Warning: under-voltage detected!</b> The power supply of the MoodleBox is inadequate, which can cause various problems, for example a limitation of the number of Wi-Fi clients or even an unexpected shutdown of the device.</p><p>It is strongly recommended to <b>change your power supply</b>, giving preference to the official <a href="https://www.raspberrypi.com/products/micro-usb-power-supply/" target="_blank">Raspberry Pi 12.5W Micro USB Power Supply</a> for Raspberry Pi 3A+, 3B, 3B+ and Zero 2 W, or <a href="https://www.raspberrypi.com/products/type-c-power-supply/" target="_blank">Raspberry Pi 15W USB-C Power Supply</a> for Raspberry Pi 4B, or <a href="https://www.raspberrypi.com/products/27w-power-supply/" target="_blank">Raspberry Pi 27W USB-C Power Supply</a> for Raspberry Pi 5.</p>';
$string['undervoltageoccurred'] = '<p>An under-voltage situation has occurred since last boot of the MoodleBox. This could indicate the power supply of the MoodleBox is inadequate, which can cause various problems, for example a limitation of the number of Wi-Fi clients or even an unexpected shutdown of the device.</p><p>It is strongly recommended to <b>change your power supply</b>, giving preference to the official <a href="https://www.raspberrypi.com/products/micro-usb-power-supply/" target="_blank">Raspberry Pi 12.5W Micro USB Power Supply</a> for Raspberry Pi 3A+, 3B, 3B+ and Zero 2 W, or <a href="https://www.raspberrypi.com/products/type-c-power-supply/" target="_blank">Raspberry Pi 15W USB-C Power Supply</a> for Raspberry Pi 4B, or <a href="https://www.raspberrypi.com/products/27w-power-supply/" target="_blank">Raspberry Pi 27W USB-C Power Supply</a> for Raspberry Pi 5.</p>';
$string['unknownmodel'] = 'Unknown or unsupported Raspberry Pi model';
$string['unsupportedhardware'] = 'Unsupported server hardware detected! This plugin does only work on Raspberry Pi';
$string['uptime'] = 'System uptime';
$string['version'] = 'MoodleBox version';
$string['visible'] = 'Visible';
$string['wifichannel'] = 'Wi-Fi channel';
$string['wifichannel_help'] = 'It is not necessary to change the Wi-Fi broadcast channel unless the performance is poor due to interference.';
$string['wificountry'] = 'Wi-Fi regulatory country';
$string['wificountry_help'] = 'For legal reasons, it is recommended to set your country as the Wi-Fi regulatory country.';
$string['wifipassword'] = 'Wi-Fi password';
$string['wifipassword_help'] = 'If you have chosen a password protected Wi-Fi network, to prevent intruders from using the MoodleBox Wi-Fi network, it is recommended to change its default password. The Wi-Fi network password must have between 8 and 63 printable ASCII characters (uppercase and lowercase letters, digits, punctuation marks and a few miscellaneous symbols).';
$string['wifipasswordinvalid'] = 'The Wi-Fi network password is invalid. It must have between 8 and 63 printable ASCII characters (uppercase and lowercase letters, digits, punctuation marks and a few miscellaneous symbols).';
$string['wifipasswordon'] = 'Wi-Fi network protection';
$string['wifipasswordon_help'] = 'If enabled, users have to type a password to connect to the MoodleBox Wi-Fi network.';
$string['wifisettings'] = 'Wi-Fi settings';
$string['wifisettingserror'] = 'The Wi-Fi settings were not changed. Some settings are not valid.';
$string['wifisettingsmessage'] = 'The Wi-Fi settings were changed. Don\'t forget to communicate the new SSID and password to your students.';
$string['wifissid'] = 'Wi-Fi network name';
$string['wifissid_help'] = 'The name of the Wi-Fi network (SSID) of the MoodleBox. It must be a string of at least 1 byte and at most 32 bytes. Remember that some characters, such as emojis, use more than one byte.';
$string['wifissidhidden'] = 'Hidden Wi-Fi network';
$string['wifissidhiddenstate'] = 'Wi-Fi SSID visibility';
$string['wifissidhiddenstate_help'] = 'If enabled, Wi-Fi SSID will be hidden from users, who won\'t know that there\'s a MoodleBox around. This will notably reduce the usability of the device, but improve slightly its security.';
$string['wifissidinvalid'] = 'The name of the Wi-Fi network (SSID) provided is invalid. It must be a string of at least 1 byte and at most 32 bytes.';

// Deprecated.
$string['ipaddress'] = 'IP address';
$string['showbuttonsinfooter'] = 'Show restart and shutdown buttons in footer';
$string['showbuttonsinfooter_desc'] = 'If enabled, the restart and shutdown buttons are displayed in the footer of all pages of the site when logged in as an administrator or as a manager.';
