#!/usr/bin/env php
<?php
// This script is part of MoodleBox plugin for moodlebox
// Copyright (C) 2021 onwards Nicolas Martignoni
//
// This script is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This script  is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this script.  If not, see <https://www.gnu.org/licenses/>.
//
// This script MUST be run as root.
if (posix_getuid() !== 0){
  echo "This script must be run as root\n";
  exit(1);
}

function file_replace_line($file_name, $parameter_to_change, $parameter_to_replace) {
    $file = explode("\n", rtrim(file_get_contents($file_name)));
    $file = preg_replace('/^' . $parameter_to_change . '$/', $parameter_to_replace, $file);
    $file = implode("\n", $file);
    file_put_contents($file_name, $file);
}

function file_insert_line_after($file_name, $line_to_detect, $line_to_insert) {
    $file = explode("\n", rtrim(file_get_contents($file_name)));
    $file = preg_replace('/^' . $line_to_detect . '$/', '\0' . "\n" . $line_to_insert, $file);
    $file = implode("\n", $file);
    file_put_contents($file_name, $file);
}

function is_regex_in_file($file_name, $regex) {
    $file = explode("\n", rtrim(file_get_contents($file_name)));
    return count(preg_filter($regex, '\0', $file)) !== 0;
}

// Configuration.

// Get directory of this script.
$this_dir = dirname(__FILE__);

// Path of file containing the new settings (plain text).
$settings_file = dirname($this_dir) . "/.wifisettings";

// Path of various config files.
$hostapd_conf_file = "/etc/hostapd/hostapd.conf";
$dhcpcd_conf_file = "/etc/dhcpcd.conf";
$dnsmasq_conf_file = "/etc/dnsmasq.conf";
$hosts_file = "/etc/hosts";
$nodogsplash_conf_file = "/etc/nodogsplash/nodogsplash.conf";

// New values taken from $settings_file.
$settings = parse_ini_file($settings_file);
$new_channel = $settings['channel'];
$new_country = $settings['country'];
$new_password = $settings['password'];
$new_ssid = $settings['ssid'];
$password_protected = $settings['passwordprotected'];
$ssid_hidden = $settings['ssidhiddenstate'];
$new_static_ip = $settings['ipaddress'];

// Valid country codes {@link https://www.iso.org/iso-3166-country-codes.html}.
$allowed_countries = "AD AE AF AG AI AL AM AO AQ AR AS AT AU AW AX AZ BA BB BD BE BF BG BH BI BJ BL BM BN BO BQ BR BS BT BV BW BY BZ CA CC CD CF CG CH CI CK CL CM CN CO CR CU CV CW CX CY CZ DE DJ DK DM DO DZ EC EE EG EH ER ES ET FI FJ FK FM FO FR GA GB GD GE GF GG GH GI GL GM GN GP GQ GR GS GT GU GW GY HK HM HN HR HT HU ID IE IL IM IN IO IQ IR IS IT JE JM JO JP KE KG KH KI KM KN KP KR KW KY KZ LA LB LC LI LK LR LS LT LU LV LY MA MC MD ME MF MG MH MK ML MM MN MO MP MQ MR MS MT MU MV MW MX MY MZ NA NC NE NF NG NI NL NO NP NR NU NZ OM PA PE PF PG PH PK PL PM PN PR PS PT PW PY QA RE RO RS RU RW SA SB SC SD SE SG SH SI SJ SK SL SM SN SO SR SS ST SV SX SY SZ TC TD TF TG TH TJ TK TL TM TN TO TR TT TV TW TZ UA UG UM US UY UZ VA VC VE VG VI VN VU WF WS YE YT ZA ZM ZW";

// Actions.

// Password setting.
// Validate password length and allowed chars.
// Password must have 8 to 63 characters. Each character must have an encoding in the
// range of 32 to 126, inclusive, see IEEE Std. 802.11i-2004, Annex H.4.1.
preg_match_all("/^[\x20-\x7e]{8,63}$/", $new_password, $results);
// Replace new password with 'moodlebox' if invalid.
if ( empty($results[0]) || $new_password != $results[0][0] ) {
    $new_password = 'moodlebox';
}
// New password is now valid; set it in config file.
file_replace_line($hostapd_conf_file, 'wpa_passphrase=.*', 'wpa_passphrase=' . $new_password);

// Country setting.
// Validate new country. Replace it with 'CH' if invalid.
if ( !strpos($allowed_countries, $new_country)) {
    $new_country = 'CH';
}
// New channel is now valid; set it in config file.
file_replace_line($hostapd_conf_file, 'country_code=.*', 'country_code=' . $new_country);

// Channel setting.
// Validate new channel. Replace it with 11 if invalid.
$new_channel = strval(intval($new_channel));
if ( intval($new_channel) < 1 || intval($new_channel) > 13 ) {
    $new_channel = '11';
}
// Channel 12 and 13 aren't valid in Canada and US.
if ( strpos('CA US', $new_country) !== false && intval($new_channel) > 11 ) {
    $new_channel = '11';
}
// New channel is now valid; set it in config file.
file_replace_line($hostapd_conf_file, 'channel=.*', 'channel=' . $new_channel);

// SSID setting.
// Validate new SSID. At this point, $new_ssid is a string of hex values,
// e.g. "74657374" for "test". We want to check that it is valid, and
// between 1 and 32 bytes.
preg_match_all("/^(?:[0-9a-fA-F]{2}){1,32}$/", $new_ssid, $results);
// Replace it with '4d6f6f646c65426f78' (meaning "MoodleBox") if invalid.
if ( empty($results[0]) || $new_ssid != $results[0][0] ) {
    $new_ssid = '4d6f6f646c65426f78';
}
// New SSID is now valid; set it in config file. We change ssid to ssid2 too.
file_replace_line($hostapd_conf_file, 'ssid2?=.*', 'ssid2=' . $new_ssid);
// Check if hostapd config file defines a 'utf8_ssid' key.
if ( !is_regex_in_file($hostapd_conf_file, '/^utf8_ssid=\b/') ) { // add utf8_ssid param.
    file_insert_line_after($hostapd_conf_file, 'ssid2=.*', 'utf8_ssid=1');
}

// SSID hiding setting.
// Validate SSID hiding setting. Replace it with 0 if invalid.
if ( $ssid_hidden != 0 && $ssid_hidden != 1 ) {
    $ssid_hidden = 0;
}
// SSID hiding setting is now valid; set it in config file.
// Check if line "ignore_broadcast_ssid=..." exist uncommented in config file.
if ( !is_regex_in_file($hostapd_conf_file, '/^ignore_broadcast_ssid=\b/') ) {
    file_put_contents($hostapd_conf_file, "\n# Show or hide SSID\nignore_broadcast_ssid=" . $ssid_hidden, FILE_APPEND);
} else {
    file_replace_line($hostapd_conf_file, 'ignore_broadcast_ssid=.*', 'ignore_broadcast_ssid=' . $ssid_hidden);
}

// Password protection setting.
// Validate and convert password protection setting to boolean. Set it to 'true' if invalid.
if ( $password_protected != 0 && $password_protected != 1 ) {
    $password_protected = true;
} else {
    $password_protected = ($password_protected === '1');
}
// Check if line "wpa_passphrase=..." exist uncommented in config file.
// If found, the Wi-Fi network is currently password protected.
$is_currently_protected = is_regex_in_file($hostapd_conf_file, '/^wpa_passphrase=\b/');
if (!$password_protected && $is_currently_protected) {
    file_replace_line($hostapd_conf_file, '^wpa_passphrase=.*', '# wpa_passphrase=moodlebox');
    file_replace_line($hostapd_conf_file, '^wpa=.*', '# wpa=2');
    file_replace_line($hostapd_conf_file, '^wpa_key_mgmt=.*', '# wpa_key_mgmt=WPA-PSK');
    file_replace_line($hostapd_conf_file, '^rsn_pairwise=.*', '# rsn_pairwise=CCMP');
} else if ($password_protected && !$is_currently_protected ) {
    file_replace_line($hostapd_conf_file, '^#.*wpa_passphrase=.*', 'wpa_passphrase=' . $new_password);
    file_replace_line($hostapd_conf_file, '^#.*wpa=.*', 'wpa=2');
    file_replace_line($hostapd_conf_file, '^#.*wpa_key_mgmt=.*', 'wpa_key_mgmt=WPA-PSK');
    file_replace_line($hostapd_conf_file, '^#.*rsn_pairwise=.*', 'rsn_pairwise=CCMP');
}

// Static IP setting.
// Validate IP address. RFC 1918 {@link https://datatracker.ietf.org/doc/html/rfc1918#section-3 }.
// Regex shamelessly taken from {@link https://stackoverflow.com/a/44333761/}.
preg_match_all("/^(?:10(\.(25[0-5]|2[0-4][0-9]|1[0-9]{1,2}|[0-9]{1,2})){3}|((172\.(1[6-9]|2[0-9]|3[01]))|192\.168)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{1,2}|[0-9]{1,2})){2})$/", $new_static_ip, $results);
// Replace it with '10.0.0.1' if invalid, reserved or public.
if ( empty($results[0]) || $new_static_ip != $results[0][0] ) {
    $new_static_ip = '10.0.0.1';
} else { // Ensure last segment of IP is 1.
    $ip = explode('.', $new_static_ip);
    array_pop($ip);
    array_push($ip, 1);
    $new_static_ip = implode('.', $ip);
}
// New static IP is now valid; compute DHCP range.
$ip = explode('.', $new_static_ip);
$subnet = implode('.', array_slice($ip, 0, count($ip)-1));
$min_range = $subnet . '.10';
$max_range = $subnet . '.254';
// Set IP and range in all needed files.
file_replace_line($dhcpcd_conf_file, 'static ip_address=.*', 'static ip_address=' . $new_static_ip . '/24');
file_replace_line($dnsmasq_conf_file, 'listen-address=(?!127).*', 'listen-address=' . $new_static_ip);
file_replace_line($dnsmasq_conf_file, 'address=\/home\/.*', 'address=/home/' . $new_static_ip);

/*
// End of actions.
//
// Restart hostapd service.
systemctl restart hostapd
// Restart again after 1 second; workaround some wifi driver bug.
sleep 1
systemctl restart hostapd

*/

// The end.
