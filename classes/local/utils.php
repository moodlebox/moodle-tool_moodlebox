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
 * Utilities for tool_moodlebox.
 *
 * @package    tool_moodlebox
 * @copyright  2018 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Utilities for tool_moodlebox
 *
 * @copyright  2018 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Get Raspberry Pi hardware model
     *
     * Revision field in /proc/cpuinfo. The bit fields are as follows.
     * @link https://github.com/raspberrypi/documentation/tree/master/hardware/raspberrypi/revision-codes.
     * @link https://github.com/AndrewFromMelbourne/raspberry_pi_revision/.
     *
     * +----+----+----+----+----+----+----+----+
     * |FEDC|BA98|7654|3210|FEDC|BA98|7654|3210|
     * +----+----+----+----+----+----+----+----+
     * |    |    |    |    |    |    |    |AAAA|
     * |    |    |    |    |    |BBBB|BBBB|    |
     * |    |    |    |    |CCCC|    |    |    |
     * |    |    |    |DDDD|    |    |    |    |
     * |    |    | EEE|    |    |    |    |    |
     * |    |    |F   |    |    |    |    |    |
     * |    |   G|    |    |    |    |    |    |
     * |    |  H |    |    |    |    |    |    |
     * |   I|II  |    |    |    |    |    |    |
     * |  J |    |    |    |    |    |    |    |
     * | K  |    |    |    |    |    |    |    |
     * |L   |    |    |    |    |    |    |    |
     * +----+----+----+----+----+----+----+----+
     * |1098|7654|3210|9876|5432|1098|7654|3210|
     * +----+----+----+----+----+----+----+----+
     *
     * +---+-------+-------------–-+----------------------------------------------------+
     * | # | bits  | contains      | values                                             |
     * +---+-------+------------–--+----------------------------------------------------+
     * | A | 00-03 | PCB Revision  | The PCB revision number                            |
     * | B | 04-11 | Model name    | A, B, A+, B+, 2B, Alpha, CM1, unknown, 3B, Zero,   |
     * |   |       |               | CM3, unknown, Zero W, 3B+, 3A+, internal, CM3+, 4B |
     * | C | 12-15 | Processor     | BCM2835, BCM2836, BCM2837, BCM2711                 |
     * | D | 16-19 | Manufacturer  | Sony UK, Egoman, Embest, Sony Japan,               |
     * |   |       |               | Embest, Stadium                                    |
     * | E | 20-22 | Memory size   | 256 MB, 512 MB, 1 GB, 2 GB, 4 GB, 8 GB             |
     * | F | 23-23 | Revision flag | If set, new-style revision                         |
     * | G | 24-24 | Unused        |                                                    |
     * | H | 25-25 | Warranty bit  | If set, warranty void by overclocking (post Pi2)   |
     * | I | 26-28 | Unused        |                                                    |
     * | J | 29-29 | OTP Read      | If set, OTP reading disallowed                     |
     * | K | 30-30 | OTP Program   | If set, OTP programming disallowed                 |
     * | L | 31-31 | Overvoltage   | If set, Overvoltage disallowed                     |
     * +---+-------+---------------+----------------------------------------------------+
     *
     * @return associative array of parameters, value or false if unsupported hardware.
     */
    public static function get_hardware_model() {
        $revisionnumber = null;

        // Read revision number from device.
        if ( $cpuinfo = @file_get_contents('/proc/cpuinfo') ) {
            if ( preg_match_all('/^Revision.*/m', $cpuinfo, $revisionmatch) > 0 ) {
                $revisionnumber = explode(' ', $revisionmatch[0][0]);
                $revisionnumber = end($revisionnumber);
            }
        }
        $revisionnumber = hexdec($revisionnumber);

        // Define arrays of various hardware parameter values.
        $memorysizes = array('256 MB', '512 MB', '1 GB', '2 GB', '4 GB', '8 GB');
        $models = array('A', 'B', 'A+', 'B+', '2B', 'Alpha', 'CM1', 'Unknown',
                '3B', 'Zero', 'CM3', 'Unknown', 'ZeroW', '3B+', '3A+', 'Internal use',
                'CM3+', '4B');
        $processors = array('BCM2835', 'BCM2836', 'BCM2837', 'BCM2711');
        $manufacturers = array('Sony UK', 'Egoman', 'Embest', 'Sony Japan',
                'Embest', 'Stadium');

        // Get raw values of hardware parameters using bitwise operations.
        $rawrevision = ($revisionnumber & 0xf);
        $rawmodel = ($revisionnumber & 0xff0) >> 4;
        $rawprocessor = ($revisionnumber & 0xf000) >> 12;
        $rawmanufacturer = ($revisionnumber & 0xf0000) >> 16;
        $rawmemory = ($revisionnumber & 0x700000) >> 20;
        $rawversionflag = ($revisionnumber & 0x800000) >> 23;

        // If recent hardware present, return associative array of parameters, value.
        // Return false otherwise.
        if ($rawversionflag) {
            $revision = '1.' . $rawrevision;
            $model = $models[$rawmodel];
            $processor = $processors[$rawprocessor];
            $manufacturer = $manufacturers[$rawmanufacturer];
            $memorysize = $memorysizes[$rawmemory];

            return array(
                'revision' => $revision,
                'model' => $model,
                'processor' => $processor,
                'manufacturer' => $manufacturer,
                'memory' => $memorysize
            );
        } else {
            return false;
        }
    }

    /**
     * Parse config files with "setting=value" syntax, ignoring commented lines
     * beginnning with a hash (#).
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param file $file to parse
     * @param bool $mode (optional)
     * @param int $scannermode (optional)
     * @return associative array of parameters, value
     */
    public static function parse_config_file($file, $mode = false, $scannermode = INI_SCANNER_NORMAL) {
        return parse_ini_string(preg_replace('/^#.*\\n/m', '', @file_get_contents($file)), $mode, $scannermode);
    }

    /**
     * Get ethernet interface name. Usually 'eth0'.
     *
     * @return string containing interface name
     */
    public static function get_ethernet_interface_name() {
        $path = realpath('/sys/class/net');

        $iter = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS +
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $iter = new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::CHILD_FIRST);
        $iter = new \RegexIterator($iter, '|^.*/device$|i', \RecursiveRegexIterator::GET_MATCH);
        $iter->setMaxDepth(2);
        $matches = array_values(preg_grep('#^.*/(eth|en).*$#i', array_keys(iterator_to_array($iter))))[0];

        return explode('/', $matches)[4];
    }

    /**
     * Get wireless interface name. Usually 'wlan0'.
     *
     * @return string containing interface name
     */
    public static function get_wireless_interface_name() {
        $path = realpath('/sys/class/net');

        $iter = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS +
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $iter = new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::CHILD_FIRST);
        $iter = new \RegexIterator($iter, '|^.*/wireless$|i', \RecursiveRegexIterator::GET_MATCH);
        $iter->setMaxDepth(2);

        return explode('/', array_keys(iterator_to_array($iter))[0])[4];
    }

    /**
     * Get default host and gateway addresses.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return associative array of parameters, value or false if ethernet not connected.
     */
    public static function get_ethernet_addresses() {
        $iface = self::get_ethernet_interface_name();

        $command = "ip route show 0.0.0.0/0 dev " . $iface;
        if ( $ethernetaddresses = exec($command, $out) ) {
            $array = explode(' ', $ethernetaddresses);
            return array(
                'host' => $array[6],
                'gateway' => $array[2]
            );
        } else {
            return false;
        }
    }

    /**
     * Convert string with hexadecimal code to unicode string.
     * @link https://stackoverflow.com/a/12083180.
     *
     * @param string $string to convert
     * @return string converted
     */
    public static function convert_hex_string($string) {
        return preg_replace_callback('#\\\\x([[:xdigit:]]{2})#ism', function($matches) {
            return chr(hexdec($matches[1]));
        }, $string);
    }

    /**
     * Find unallocated space on SD card.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return float value if unallocated space, in MB.
     */
    public static function unallocated_free_space() {
        // @codingStandardsIgnoreLine
        $command = "sudo parted /dev/mmcblk0 unit MB print free | tail -n2 | grep 'Free Space' | awk '{print $3}' | sed -e 's/MB$//'";
        $unallocatedfreespace = exec($command, $out);
        return (float)$unallocatedfreespace;
    }

    /**
     * Get Raspberry Pi throttled state
     * @link https://github.com/raspberrypi/documentation/blob/master/raspbian/applications/vcgencmd.md.
     * @link https://www.raspberrypi.org/forums/viewtopic.php?f=63&t=147781&start=50#p972790.
     *
     * +----+----+----+----+----+
     * |3210|FEDC|BA98|7654|3210|
     * +----+----+----+----+----+
     * |    |    |    |    |   A|
     * |    |    |    |    |  B |
     * |    |    |    |    | C  |
     * |    |    |    |    |D   |
     * |   E|    |    |    |    |
     * |  F |    |    |    |    |
     * | G  |    |    |    |    |
     * |H   |    |    |    |    |
     * +----+----+----+----+----+
     * |9876|5432|1098|7654|3210|
     * +----+----+----+----+----+
     *
     * +---+------+-------------–-----------------------+
     * | # | bits | contains                            |
     * +---+------+------------–------------------------+
     * | A |  01  | Under voltage detected              |
     * | B |  02  | Arm frequency capped                |
     * | C |  03  | Currently throttled                 |
     * | D |  04  | Soft temperature limit active       |
     * |   |      |                                     |
     * | E |  16  | Under voltage has occurred          |
     * | F |  17  | Arm frequency capped has occurred   |
     * | G |  18  | Throttling has occurred             |
     * | H |  19  | Soft temperature limit has occurred |
     * +---+------+-------------------------------------+
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return associative array of parameters, value or false if unsupported hardware.
     */
    public static function get_throttled_state() {
        $throttledstate = null;

        $command = "sudo vcgencmd get_throttled | awk -F'=' '{print $2}'";
        // Get bit pattern from device.
        if ( $throttledstate = exec($command, $out) ) {
            $throttledstate = hexdec($throttledstate);

            // Get raw values using bitwise operations.
            $undervoltagedetected = ($throttledstate & 0x1);
            $armfreqcapped = ($throttledstate & 0x2) >> 1;
            $currentlythrottled = ($throttledstate & 0x4) >> 2;
            $templimitactive = ($throttledstate & 0x8) >> 3;
            $undervoltageoccurred = ($throttledstate & 0x10000) >> 16;
            $armfreqwascapped = ($throttledstate & 0x20000) >> 17;
            $throttlingoccurred = ($throttledstate & 0x40000) >> 18;
            $templimitoccurred = ($throttledstate & 0x80000) >> 19;

            return array(
                'undervoltagedetected' => ($undervoltagedetected == 1),
                'armfrequencycapped' => ($armfreqcapped == 1),
                'currentlythrottled' => ($currentlythrottled == 1),
                'templimitactive' => ($templimitactive == 1),
                'undervoltageoccurred' => ($undervoltageoccurred == 1),
                'armfrequencycappedoccurred' => ($armfreqwascapped == 1),
                'throttlingoccurred' => ($throttlingoccurred == 1),
                'templimitoccurred' => ($templimitoccurred == 1),
            );
        } else {
            return false;
        }
    }

    /**
     * Get survey data.
     *
     * @return associative array of parameters, value
     */
    public static function get_survey_data() {
        require(dirname(dirname(dirname(__FILE__))).'/version.php');

        // Read serial number from device.
        if ( $cpuinfo = @file_get_contents('/proc/cpuinfo') ) {
            if ( preg_match_all('/^Serial.*/m', $cpuinfo, $serialmatch) > 0 ) {
                $serialnumber = explode(' ', $serialmatch[0][0]);
                $serialnumber = end($serialnumber);
            }
        }
        // Compute device identifier.
        $deviceid = hash('md5', $serialnumber);

        // Get hardware model.
        $hardware = self::get_hardware_model();

        // Get MoodleBox image version.
        if ( file_exists('/etc/moodlebox-info') ) {
            $moodleboxinfo = file('/etc/moodlebox-info');
            if ( preg_match_all('/^.*version ((\d+\.)+(.*|\d+)), (\d{4}-\d{2}-\d{2})$/i',
                    $moodleboxinfo[0], $moodleboxinfomatch) > 0 ) {
                $moodleboxinfo = $moodleboxinfomatch[1][0] . ' (' . $moodleboxinfomatch[4][0] . ')';
            }
        }

        $surveydata = array(
            'deviceid' => $deviceid,
            'osrelease' => self::parse_config_file('/etc/os-release')['PRETTY_NAME'],
            'kernel' => php_uname('s') . ' ' . php_uname('r') . ' ' .  php_uname('m'),
            'hardware' => $hardware['model'] . ' ' . $hardware['memory'],
            'moodleboxversion' => $moodleboxinfo,
            'pluginversion' => $plugin->release . ' (' . $plugin->version . ')',
            'sdsize' => disk_total_space('/'),
        );

        return $surveydata;
    }

}
