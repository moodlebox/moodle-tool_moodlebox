#!/bin/bash
# This script is part of MoodleBox plugin for moodlebox
# Copyright (C) 2016 onwards Nicolas Martignoni
#
# This script is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This script  is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this script.  If not, see <http://www.gnu.org/licenses/>.
#
# This script MUST be run as root
[[ $EUID -ne 0 ]] && { echo "This script must be run as root"; exit 1; }
#
# Configuration
# get directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# path of file containing the new password (plain text)
FILE=${DIR%/*}/.wifisettings
# New values
NEWCHANNEL="$(grep 'channel' $FILE | cut -d= -f2)"
NEWPASSWORD="$(grep 'password' $FILE | cut -d= -f2)"
NEWSSID="$(grep 'ssid' $FILE | cut -d= -f2)"
#
# Script
# set new password
sed -i "/^wpa_passphrase/c\wpa_passphrase=$NEWPASSWORD" /etc/hostapd/hostapd.conf
# set new channel
sed -i "/^channel/c\channel=$NEWCHANNEL" /etc/hostapd/hostapd.conf
# set new ssid
sed -i "/^ssid/c\ssid=$NEWSSID" /etc/hostapd/hostapd.conf
# restart hostapd service
systemctl restart hostapd
# restart again after 1 second; workaround some wifi driver bug
sleep 1
systemctl restart hostapd
# the end