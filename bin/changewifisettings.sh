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
# This script MUST be run as root.
[[ $EUID -ne 0 ]] && { echo "This script must be run as root"; exit 1; }
#
# Configuration.
# Get directory of this script.
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# Path of file containing the new settings (plain text).
FILE=${DIR%/*}/.wifisettings
# Path of hostapd config file.
CONFIGFILE="/etc/hostapd/hostapd.conf"
# New values taken from $FILE.
NEWCHANNEL="$(grep '^channel\b' $FILE | cut -d= -f2)"
NEWPASSWORD="$(grep '^password\b' $FILE | cut -d= -f2)"
NEWSSID="$(grep '^ssid\b' $FILE | cut -d= -f2)"
PASSWORDPROTECTED="$(grep '^passwordprotected\b' $FILE | cut -d= -f2)"
#
# Actions.
#
# Password setting.
# Validate password length and allowed chars. Replace it with 'moodlebox' if invalid.
# Each character must have an encoding in the range of 32 to 126, inclusive,
# see IEEE Std. 802.11i-2004, Annex H.4.1.
[[ $NEWPASSWORD =~ ^[\ -z\{\|\}\~]{8,63}$ ]] || NEWPASSWORD="moodlebox"
# New password is now valid; set it in config file.
sed -i "/^wpa_passphrase=/c\wpa_passphrase=$NEWPASSWORD" "$CONFIGFILE"
#
# Channel setting.
# Validate new channel. Replace it with 6 if invalid.
[[ $NEWCHANNEL =~ ^[1-9]|1[0-3]$ ]] || NEWCHANNEL="6"
# New channel is now valid; set it in config file.
sed -i "/^channel=/c\channel=$NEWCHANNEL" "$CONFIGFILE"
#
# SSID setting.
# Validate new SSID. Replace it with 'MoodleBox' if invalid.
[[ $NEWSSID =~ ^[[:alnum:]]{1,32}$ ]] || NEWSSID="MoodleBox"
# New SSID is now valid; set it in config file.
sed -i "/^ssid=/c\ssid=$NEWSSID" "$CONFIGFILE"
# Check if line "wpa_passphrase=..." exist uncommented in config file.
# If found, the Wi-Fi network is currently password protected.
STRING="^wpa_passphrase=\b"
if [ -z $(grep "$STRING" "$CONFIGFILE") ]; then # Line not found, we're not password protected.
    ISCURRENTLYPROTECTED=false
else # Line found, we're password protected.
    ISCURRENTLYPROTECTED=true
fi
if [ "$PASSWORDPROTECTED" -eq 1 ]; then
    if [[ "$ISCURRENTLYPROTECTED" == false ]]; then
        # echo "Would like to protect and is NOT protected"
        sed -i "/#*wpa_passphrase=/c\wpa_passphrase=$NEWPASSWORD" "$CONFIGFILE"
        sed -i "/#*wpa=/c\wpa=2" "$CONFIGFILE"
        sed -i "/#*wpa_key_mgmt=/c\wpa_key_mgmt=WPA-PSK" "$CONFIGFILE"
        sed -i "/#*rsn_pairwise=/c\rsn_pairwise=CCMP" "$CONFIGFILE"
    fi
else
    if [[ "$ISCURRENTLYPROTECTED" == true ]]; then
        # echo "Would NOT like to protect but is protected"
        sed -i "/^wpa_passphrase=/c\#wpa_passphrase=moodlebox" "$CONFIGFILE"
        sed -i "/^wpa=/c\#wpa=2" "$CONFIGFILE"
        sed -i "/^wpa_key_mgmt=/c\#wpa_key_mgmt=WPA-PSK" "$CONFIGFILE"
        sed -i "/^rsn_pairwise=/c\#rsn_pairwise=CCMP" "$CONFIGFILE"
    fi
fi
# End of actions.
#
# Restart hostapd service.
systemctl restart hostapd
# Restart again after 1 second; workaround some wifi driver bug.
sleep 1
systemctl restart hostapd
# The end.
