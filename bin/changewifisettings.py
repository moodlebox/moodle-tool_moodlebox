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
import os, sys, re, configparser, fileinput, ipaddress, subprocess, mmap, binascii

# This script MUST be run as root.
if not os.geteuid() == 0:
    sys.exit('This script must be run as root')

# Get directory of this script.
this_dir = os.path.dirname(os.path.realpath(__file__))
# Path of file containing the new access point settings (plain text).
settings_file = os.path.join(os.path.dirname(this_dir), '.wifisettings')

# Helper functions.

def file_replace_line(file_name, search_pattern, replace_pattern):
    for line in fileinput.input(file_name, inplace=True):
        line = line.rstrip('\r\n')
        line = re.sub(search_pattern, replace_pattern, line)
        print(line)

def is_regex_in_file(file_name, search_pattern):
    the_file = open(file_name, 'r')
    return re.findall(search_pattern, the_file.read(), re.MULTILINE)

def is_networkmanager():
    return subprocess.run(['systemctl', '-q', 'is-active', 'NetworkManager']).returncode == 0

def is_pi3():
    """True if this is a Pi 3 B (not Plus!)."""
    return "Pi 3 Model B Rev" in open('/proc/device-tree/model').read()

# Default access point settings.

default_channel = '11'
default_country = 'CH'
default_password = 'moodlebox'
if is_networkmanager():
    default_ssid = 'MoodleBox'
else:
    default_ssid = '4d6f6f646c65426f78' # This means 'MoodleBox'.
default_ip_address = '10.0.0.1'
default_min_range = 10
default_max_range = 254

# Workaround a bug with WPA2 protocol on RPi3B.
# See https://github.com/moodlebox/moodlebox/issues/319.
if is_pi3():
    proto = 'rsn,wpa'
else:
    proto = 'rsn'

# Path of various config files.

kernel_cmdline_file = "/boot/firmware/cmdline.txt"
hosts_file = "/etc/hosts"
nodogsplash_conf_file = "/etc/nodogsplash/nodogsplash.conf"
if is_networkmanager():
    dnsmasq_lease_file = "/tmp/dnsmasq.leases"
    dnsmasq_conf_file = "/etc/NetworkManager/dnsmasq-shared.d/00-dhcp.conf"
else:
    dnsmasq_lease_file = "/var/lib/misc/dnsmasq.leases"
    if os.path.exists("/etc/dnsmasq.d/uap0.conf"):
        dnsmasq_conf_file = "/etc/dnsmasq.d/uap0.conf"
    else:
        dnsmasq_conf_file = "/etc/dnsmasq.conf"
hostapd_conf_file = "/etc/hostapd/hostapd.conf"
dhcpcd_conf_file = "/etc/dhcpcd.conf"

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
ssid_hidden_state = settings['dummy_section']['ssidhiddenstate']
new_static_ip = settings['dummy_section']['ipaddress']

# Action functions.

def do_regulatory_country():
    """Regulatory country setting."""
    global new_country
    # Check if new_country is in list of ISO 3166 alpha-2 country codes.
    regex = re.compile(bytes('\n' + new_country + '\s', 'ascii'))
    with open('/usr/share/zoneinfo/iso3166.tab') as iso3166file:
        data = mmap.mmap(iso3166file.fileno(), 0, access=mmap.ACCESS_READ)
    # Replace new_country with default_country if invalid.
    if not re.search(regex, data):
        new_country = default_country
    # new_country is now valid.
    if is_networkmanager():
        # Set regulatory country in kernel command line.
        file_replace_line(kernel_cmdline_file,
                r'\s*cfg80211.ieee80211_regdom=\S*',
                r'')
        file_replace_line(kernel_cmdline_file,
                r'^(.*)$',
                r'\1 cfg80211.ieee80211_regdom=' + new_country)
        # Set regulatory country with iw.
        subprocess.run(['sudo', 'iw', 'reg', 'set', new_country])
    else:
        # Set regulatory country in hostapd config file.
        file_replace_line(hostapd_conf_file, 'country_code=.*', 'country_code=' + new_country)

def do_channel():
    """Channel setting."""
    global new_channel
    # Validate new_channel. Replace it with default_channel if invalid.
    if int(new_channel) < 1 or int(new_channel) > 13:
        new_channel = default_channel
    # Channel 12 and 13 aren't valid in Canada and US.
    if new_country in ['CA','US'] and int(new_channel) > 11:
        new_channel = default_channel
    # new_channel is now valid.
    if is_networkmanager():
        # Workaround bug in NetworkManager: country_code is not used, so if new_channel is 12 and 13,
        # we revert to default_channel (11).
        # See https://gitlab.freedesktop.org/NetworkManager/NetworkManager/-/issues/960.
        new_channel = str(min(int(default_channel), int(new_channel)))
        # Set channel with nmcli
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi.channel', new_channel])
    else:
        # Set channel in hostapd config file.
        file_replace_line(hostapd_conf_file, 'channel=.*', 'channel=' + new_channel)

def do_ssid():
    """SSID setting."""
    global new_ssid
    # Validate new_ssid. At this point, new_ssid is a string of hex values,
    # e.g. "44756d6d79" for "Dummy". We want to check that it is valid, and
    # between 1 and 32 bytes long.
    ssid_pattern = re.compile('^(?:[0-9a-fA-F]{2}){1,32}$')
    if not bool(ssid_pattern.search(new_ssid)):
        new_ssid = default_ssid
    # new_ssid is now valid.
    if is_networkmanager():
        # Convert new_ssid into plain string.
        new_ssid = binascii.unhexlify(new_ssid).decode()
        # Set SSID with nmcli.
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi.ssid', new_ssid])
    else:
        # Set SSID in hostapd config file. We change ssid to ssid2 too.
        file_replace_line(hostapd_conf_file, '^ssid2?=.*', 'ssid2=' + new_ssid)
        # Check if hostapd config file defines a 'utf8_ssid' key and add it when missing.
        if not is_regex_in_file(hostapd_conf_file, r'^utf8_ssid=\b'):
            file_replace_line(hostapd_conf_file,
                    '^(?P<ssid>ssid2?=(?:[0-9a-fA-F]{2}){1,32}).*$',
                    '\g<ssid>\nutf8_ssid=1')

def do_ssid_hidden_state():
    """SSID hidden state setting."""
    global ssid_hidden_state
    # Validate ssid_hidden_state. Replace it with '0' if invalid.
    if ssid_hidden_state not in ['0','1']:
        ssid_hidden_state = '0'
    # SSID hidden status setting is now valid.
    if is_networkmanager():
        # Set ssid_hidden_state with nmcli.
        if ssid_hidden_state == '1':
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi.hidden', 'yes'])
        else:
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi.hidden', 'no'])
    else:
        # Set ssid_hidden_state in hostapd config file.
        # Check if line "ignore_broadcast_ssid=..." exist uncommented in hostapd config file.
        if not is_regex_in_file(hostapd_conf_file, r'^ignore_broadcast_ssid=\b'):
            file_replace_line(hostapd_conf_file,
                    '^(?P<hidden>utf8_ssid=[01]).*$',
                    '\g<hidden>\n# Show or hide SSID\nignore_broadcast_ssid=' + ssid_hidden_state)
        else:
            file_replace_line(hostapd_conf_file,
                    'ignore_broadcast_ssid=.*',
                    'ignore_broadcast_ssid=' + ssid_hidden_state)

def do_password_protected():
    """Password protection setting."""
    global password_protected
    # Validate and convert password_protected to boolean. Set it to True if invalid.
    if password_protected not in ['0','1']:
        password_protected = True
    else:
        password_protected = (password_protected == '1')
    # Check if access point is currently password protected.
    if is_networkmanager():
        # Check with nmcli if access point is currently password protected.
        output = subprocess.run(
            ['sudo', 'nmcli', '-g', '802-11-wireless-security.psk', 'con', 'show', 'WifiAP'],
            capture_output = True,
            text = True,
        ).stdout
        # If output isn't empty (falsy), access point is currently password protected.
        is_currently_protected = bool(output)
    else:
        # Check if line "wpa_passphrase=..." exists uncommented in hostapd config file.
        # If found, the access point is currently password protected.
        is_currently_protected = bool(is_regex_in_file(hostapd_conf_file, r'^wpa_passphrase=\b'))
    if is_networkmanager():
        # Set parameters adequately with nmcli.
        if not password_protected and is_currently_protected:
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'remove', 'wifi-sec'])
        elif password_protected and not is_currently_protected:
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.key-mgmt', 'wpa-psk'])
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.psk', new_password])
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.group', 'ccmp'])
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.pairwise', 'ccmp'])
            subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.proto', proto])
    else:
        # Set parameters adequately in hostapd config file.
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

def do_password():
    """Password setting."""
    global new_password
    # Validate password length and allowed chars.
    # Password must have 8 to 63 characters. Each character must have an encoding in the
    # range of 32 to 126, inclusive, see IEEE Std. 802.11i-2004, Annex H.4.1.
    password_pattern = re.compile('^[\x20-\x7e]{8,63}$')
    # Replace new_password with default_password if invalid.
    if not bool(password_pattern.search(new_password)):
        new_password = default_password
    # new_password is now valid.
    if is_networkmanager():
        # Set password with nmcli.
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.key-mgmt', 'wpa-psk'])
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.psk', new_password])
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.group', 'ccmp'])
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.pairwise', 'ccmp'])
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'wifi-sec.proto', proto])
    else:
        # Set password in hostapd config file.
        file_replace_line(hostapd_conf_file, '^wpa_passphrase=.*$', 'wpa_passphrase=' + new_password)

def do_ip_address():
    """Static IP setting."""
    global new_static_ip
    # Validate IP address. RFC 1918 {@link https://datatracker.ietf.org/doc/html/rfc1918#section-3 }.
    try:
        new_static_ip = ipaddress.IPv4Address(new_static_ip)
    except ValueError:
        new_static_ip = ipaddress.IPv4Address(default_ip_address)
    # Ensure IP address is private, but not reserved, nor loopback.
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
    file_replace_line(nodogsplash_conf_file,
            '^GatewayAddress\s+' + ip_regex,
            'GatewayAddress ' + new_static_ip)
    file_replace_line(dnsmasq_conf_file,
            '^address=\/home\/.*$',
            'address=/home/' + new_static_ip)
    if is_networkmanager():
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'ipv4.addresses', new_static_ip + '/24'])
        subprocess.run(['sudo', 'nmcli', 'con', 'mod', 'WifiAP', 'ipv4.gateway', new_static_ip])
        file_replace_line(dnsmasq_conf_file,
                '^dhcp-option=6,' + ip_regex + '(?P<end>.*)$',
                'dhcp-option=6,' + new_static_ip + '\g<end>')
    else:
        file_replace_line(dhcpcd_conf_file,
                '^static ip_address=.*$',
                'static ip_address=' + new_static_ip + '/24')
        file_replace_line(dnsmasq_conf_file,
                '^listen-address=(?!127).*$',
                'listen-address=' + new_static_ip)
        file_replace_line(dnsmasq_conf_file,
                '^dhcp-range=wifi,' + ip_regex + ',' + ip_regex + ',(?P<end>.*)$',
                'dhcp-range=wifi,' + min_range + ',' + max_range + ',\g<end>')
        file_replace_line(dnsmasq_conf_file,
                '^dhcp-option=wifi,6,' + ip_regex + '(?P<end>.*)$',
                'dhcp-option=wifi,6,' + new_static_ip + '\g<end>')

def fix_wrong_kernel_cmdline():
    """Fix buggy file produced by buggy script in version 2.17.0 and 2.17.1."""
    kernel_cmdline_tofix = "/boot/cmdline.txt"
    if os.path.exists(kernel_cmdline_tofix) and not os.path.islink(kernel_cmdline_tofix):
        os.remove(kernel_cmdline_tofix)
        os.symlink(
            os.path.relpath(kernel_cmdline_file, "/boot/"),
            kernel_cmdline_tofix
        )

# Actions.

if is_networkmanager():
    fix_wrong_kernel_cmdline()
do_regulatory_country()
do_channel()
do_ssid()
do_ssid_hidden_state()
do_password_protected()
if password_protected:
    do_password()
do_ip_address()

# End of actions.
#
# Empty lease file to clean clients list.
try:
    open(dnsmasq_lease_file, 'w').close()
except IOError:
    pass

# Restart networking and hostapd service.
if is_networkmanager():
    subprocess.call(['systemctl', 'restart', 'NetworkManager.service'])
else:
    subprocess.call(['systemctl', 'restart', 'hostapd.service'])
    subprocess.call(['systemctl', 'restart', 'dnsmasq.service'])
subprocess.call(['systemctl', 'restart', 'networking.service'])

# The end.
