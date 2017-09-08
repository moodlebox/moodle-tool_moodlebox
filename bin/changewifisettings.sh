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
# Get directory of this script.
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# Path of file containing the new password (plain text).
FILE=${DIR%/*}/.wifisettings
# New values from $FILE
NEWCHANNEL="$(grep '^channel\b' $FILE | cut -d= -f2)"
NEWPASSWORD="$(grep '^password\b' $FILE | cut -d= -f2)"
NEWSSID="$(grep '^ssid\b' $FILE | cut -d= -f2)"
PASSWORDPROTECTED="$(grep '^passwordprotected\b' $FILE | cut -d= -f2)"
#
# Actions.
# Set new password.
sed -i "/^wpa_passphrase/c\wpa_passphrase=$NEWPASSWORD" /etc/hostapd/hostapd.conf
# Set new channel.
sed -i "/^channel/c\channel=$NEWCHANNEL" /etc/hostapd/hostapd.conf
# Set new ssid.
sed -i "/^ssid/c\ssid=$NEWSSID" /etc/hostapd/hostapd.conf
## WIP: Handle PASSWORDPROTECTED value
if [ "$PASSWORDPROTECTED" -eq 1 ]; then
    # Check if lines wpa=, wpa_key_mgmt=, wpa_passphrase=, rsn_pairwise= exist.
    # Leave them alone if this is the case
    # Create them otherwise
    : # TODO
else
    # Check if lines wpa=, wpa_key_mgmt=, wpa_passphrase=, rsn_pairwise= exist.
    # Delete them if this is the case
    # Do nothing otherwise
    : # TODO
fi
exit 0
# End of actions.
#
# Restart hostapd service.
systemctl restart hostapd
# Restart again after 1 second; workaround some wifi driver bug
sleep 1
systemctl restart hostapd
# The end
