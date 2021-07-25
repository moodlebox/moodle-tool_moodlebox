#!/usr/bin/env python3
"""
This script is part of MoodleBox plugin for moodlebox
Copyright (C) 2021 onwards Nicolas Martignoni

This script is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This script  is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this script.  If not, see <https://www.gnu.org/licenses/>.
"""
import os, sys, re, configparser, fileinput, ipaddress, subprocess

# This script MUST be run as root.
if not os.geteuid() == 0:
    sys.exit('This script must be run as root')

def file_replace_line(file_name, search_pattern, replace_pattern):
    for line in fileinput.input(file_name, inplace=True):
        line = line.rstrip('\r\n')
        line = re.sub(search_pattern, replace_pattern, line)
        print(line)

def is_regex_in_file(file_name, search_pattern):
    the_file = open(file_name, 'r')
    return re.findall(search_pattern, the_file.read(), re.MULTILINE)

# Configuration.

# Get directory of this script.
this_dir = os.path.dirname(os.path.realpath(__file__))
# Path of file containing the new settings (plain text).
settings_file = os.path.join(os.path.dirname(this_dir), '.wifisettings')

# Path of various config files.
hostapd_conf_file = "/etc/hostapd/hostapd.conf"
dhcpcd_conf_file = "/etc/dhcpcd.conf"
dnsmasq_conf_file = "/etc/dnsmasq.conf"
dnsmasq_lease_file = "/var/lib/misc/dnsmasq.leases"
hosts_file = "/etc/hosts"
nodogsplash_conf_file = "/etc/nodogsplash/nodogsplash.conf"

# Default settings.
default_channel = '11'
default_country = 'CH'
default_password = 'moodlebox'
default_ssid = '4d6f6f646c65426f78' # This means 'MoodleBox'.
default_ip_address = '10.0.0.1'
default_min_range = 10
default_max_range = 254

# New values taken from $settings_file.
with open(settings_file, 'r') as f:
    config_string = '[dummy_section]\n' + f.read()
settings = configparser.ConfigParser()
settings.read_string(config_string)
new_channel = settings['dummy_section']['channel']
new_country = settings['dummy_section']['country']
new_password = settings['dummy_section']['password']
new_ssid = settings['dummy_section']['ssid']
password_protected = settings['dummy_section']['passwordprotected']
ssid_hidden = settings['dummy_section']['ssidhiddenstate']
new_static_ip = settings['dummy_section']['ipaddress']

# Actions.

# Password setting.
# Validate password length and allowed chars.
# Password must have 8 to 63 characters. Each character must have an encoding in the
# range of 32 to 126, inclusive, see IEEE Std. 802.11i-2004, Annex H.4.1.
# Replace new password with 'moodlebox' if invalid.
password_pattern = re.compile('^[\x20-\x7e]{8,63}$')
if not bool(password_pattern.search(new_password)):
    new_password = default_password
# New password is now valid; set it in config file.
file_replace_line(hostapd_conf_file, '^wpa_passphrase=.*$', 'wpa_passphrase=' + new_password)

# Country setting.
# Valid country codes {@link https://www.iso.org/iso-3166-country-codes.html}.
allowed_countries = ['AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ', 'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY', 'HK', 'HM', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM', 'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SX', 'SY', 'SZ', 'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UM', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW']
# Country setting.
# Validate new country. Replace it with 'CH' if invalid.
if not new_country in allowed_countries:
    new_country = default_country
# New channel is now valid; set it in config file.
file_replace_line(hostapd_conf_file, 'country_code=.*', 'country_code=' + new_country)

# Channel setting.
# Validate new channel. Replace it with 11 if invalid.
if int(new_channel) < 1 or int(new_channel) > 13:
    new_channel = default_channel
# Channel 12 and 13 aren't valid in Canada and US.
if new_country in ['CA','US'] and int(new_channel) > 11:
    new_channel = default_channel
# New channel is now valid; set it in config file.
file_replace_line(hostapd_conf_file, 'channel=.*', 'channel=' + new_channel)

# SSID setting.
# Validate new SSID. At this point, new_ssid is a string of hex values,
# e.g. "74657374" for "test". We want to check that it is valid, and
# between 1 and 32 bytes.
ssid_pattern = re.compile('^(?:[0-9a-fA-F]{2}){1,32}$')
if not bool(ssid_pattern.search(new_ssid)):
    new_ssid = default_ssid
# New SSID is now valid; set it in config file. We change ssid to ssid2 too.
file_replace_line(hostapd_conf_file, '^ssid2?=.*', 'ssid2=' + new_ssid)
# Check if hostapd config file defines a 'utf8_ssid' key and add it if false.
if not is_regex_in_file(hostapd_conf_file, r'^utf8_ssid=\b'):
    file_replace_line(hostapd_conf_file,
            '^(?P<ssid>ssid2?=(?:[0-9a-fA-F]{2}){1,32}).*$',
            '\g<ssid>\nutf8_ssid=1')

# SSID hiding setting.
# Validate SSID hiding setting. Replace it with 0 if invalid.
if ssid_hidden not in ['0','1']:
    ssid_hidden = '0'
# SSID hiding setting is now valid; set it in config file.
# Check if line "ignore_broadcast_ssid=..." exist uncommented in config file.
if not is_regex_in_file(hostapd_conf_file, r'^ignore_broadcast_ssid=\b'):
    file_replace_line(hostapd_conf_file,
            '^(?P<hidden>utf8_ssid=[01]).*$',
            '\g<hidden>\n# Show or hide SSID\nignore_broadcast_ssid=' + ssid_hidden)
else:
    file_replace_line(hostapd_conf_file,
            'ignore_broadcast_ssid=.*',
            'ignore_broadcast_ssid=' + ssid_hidden)

# Password protection setting.
# Validate and convert password protection setting to boolean. Set it to 'true' if invalid.
if password_protected not in ['0','1']:
    password_protected = True
else:
    password_protected = (password_protected == '1')
# Check if line "wpa_passphrase=..." exist uncommented in config file.
# If found, the Wi-Fi network is currently password protected.
is_currently_protected = bool(is_regex_in_file(hostapd_conf_file, r'^wpa_passphrase=\b'))
# Set parameters adequately.
if not password_protected and is_currently_protected:
    file_replace_line(hostapd_conf_file, '^wpa_passphrase=.*', '# wpa_passphrase=moodlebox')
    file_replace_line(hostapd_conf_file, '^wpa=.*', '# wpa=2')
    file_replace_line(hostapd_conf_file, '^wpa_key_mgmt=.*', '# wpa_key_mgmt=WPA-PSK')
    file_replace_line(hostapd_conf_file, '^rsn_pairwise=.*', '# rsn_pairwise=CCMP')
elif password_protected and not is_currently_protected:
    file_replace_line(hostapd_conf_file, '^#.*wpa_passphrase=.*', 'wpa_passphrase=' + new_password)
    file_replace_line(hostapd_conf_file, '^#.*wpa=.*', 'wpa=2')
    file_replace_line(hostapd_conf_file, '^#.*wpa_key_mgmt=.*', 'wpa_key_mgmt=WPA-PSK')
    file_replace_line(hostapd_conf_file, '^#.*rsn_pairwise=.*', 'rsn_pairwise=CCMP')

# Static IP setting.
# Validate IP address. RFC 1918 {@link https://datatracker.ietf.org/doc/html/rfc1918#section-3 }.
try:
    new_static_ip = ipaddress.IPv4Address(new_static_ip)
except ValueError:
    new_static_ip = ipaddress.IPv4Address(default_ip_address)

# Ensure IP is private, but not reserved, nor loopback.
if not new_static_ip.is_private or new_static_ip.is_reserved or new_static_ip.is_loopback:
    new_static_ip = ipaddress.IPv4Address(default_ip_address)
# Set last segment of IP address to 1.
new_static_ip = ipaddress.IPv4Address('.'.join(new_static_ip.exploded.split('.')[:-1] + ['1']))
# New static IP is now in good shape; compute DHCP range.
min_range = str(new_static_ip + default_min_range - 1)
max_range = str(new_static_ip + default_max_range - 1)
new_static_ip = str(new_static_ip)

ip_regex = "(?:10(\.(25[0-5]|2[0-4][0-9]|1[0-9]{1,2}|[0-9]{1,2})){3}|((172\.(1[6-9]|2[0-9]|3[01]))|192\.168)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{1,2}|[0-9]{1,2})){2})"
# Set IP and range in all needed files.
file_replace_line(hosts_file,
        '^' + ip_regex + '\s+(?P<host>([a-zA-Z0-9][-a-zA-Z0-9]{0,62}\s*)+)$',
        new_static_ip + '\t\g<host>')
file_replace_line(dhcpcd_conf_file,
        '^static ip_address=.*$',
        'static ip_address=' + new_static_ip + '/24')
file_replace_line(dnsmasq_conf_file,
        '^listen-address=(?!127).*$',
        'listen-address=' + new_static_ip)
file_replace_line(dnsmasq_conf_file,
        '^address=\/home\/.*$',
        'address=/home/' + new_static_ip)
file_replace_line(dnsmasq_conf_file,
        '^dhcp-range=wifi,' + ip_regex + ',' + ip_regex + ',(?P<end>.*)$',
        'dhcp-range=wifi,' + min_range + ',' + max_range + ',\g<end>')
file_replace_line(dnsmasq_conf_file,
        '^dhcp-option=wifi,6,' + ip_regex + '(?P<end>.*)$',
        'dhcp-option=wifi,6,' + new_static_ip + '\g<end>')
file_replace_line(nodogsplash_conf_file,
        '^GatewayAddress\s+' + ip_regex,
        'GatewayAddress ' + new_static_ip)

# End of actions.
#
# Empty lease file to clean clients list.
try:
    open(dnsmasq_lease_file, 'w').close()
except IOError:
    pass

# Restart networking and hostapd service.
subprocess.call(['systemctl', 'restart', 'networking.service'])
subprocess.call(['systemctl', 'restart', 'hostapd.service'])
subprocess.call(['systemctl', 'restart', 'dnsmasq.service'])

# The end.
