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
# along with this script.  If not, see <https://www.gnu.org/licenses/>.
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
NEWCOUNTRY="$(grep '^country\b' $FILE | cut -d= -f2)"
NEWPASSWORD="$(grep '^password\b' $FILE | cut -d= -f2)"
NEWSSID="$(grep '^ssid\b' $FILE | cut -d= -f2)"
PASSWORDPROTECTED="$(grep '^passwordprotected\b' $FILE | cut -d= -f2)"
# Valid country codes. See https://www.iso.org/iso-3166-country-codes.html.
ALLOWEDCOUNTRIES="AD AE AF AG AI AL AM AO AQ AR AS AT AU AW AX AZ BA BB BD BE BF BG BH BI BJ BL BM BN BO BQ BR BS BT BV BW BY BZ CA CC CD CF CG CH CI CK CL CM CN CO CR CU CV CW CX CY CZ DE DJ DK DM DO DZ EC EE EG EH ER ES ET FI FJ FK FM FO FR GA GB GD GE GF GG GH GI GL GM GN GP GQ GR GS GT GU GW GY HK HM HN HR HT HU ID IE IL IM IN IO IQ IR IS IT JE JM JO JP KE KG KH KI KM KN KP KR KW KY KZ LA LB LC LI LK LR LS LT LU LV LY MA MC MD ME MF MG MH MK ML MM MN MO MP MQ MR MS MT MU MV MW MX MY MZ NA NC NE NF NG NI NL NO NP NR NU NZ OM PA PE PF PG PH PK PL PM PN PR PS PT PW PY QA RE RO RS RU RW SA SB SC SD SE SG SH SI SJ SK SL SM SN SO SR SS ST SV SX SY SZ TC TD TF TG TH TJ TK TL TM TN TO TR TT TV TW TZ UA UG UM US UY UZ VA VC VE VG VI VN VU WF WS YE YT ZA ZM ZW"
#
# Actions.
#
# Password setting.
# Validate password length and allowed chars. Replace it with 'moodlebox' if invalid.
# Password must have 8 to 63 characters. Each character must have an encoding in the
# range of 32 to 126, inclusive, see IEEE Std. 802.11i-2004, Annex H.4.1.
[[ $NEWPASSWORD =~ ^[\ -z\{\|\}\~]{8,63}$ ]] || NEWPASSWORD="moodlebox"
# New password is now valid; set it in config file.
sed -i "/^wpa_passphrase=/c\wpa_passphrase=$NEWPASSWORD" "$CONFIGFILE"
#
# Country setting.
# Validate new country. Replace it with 'CH' if invalid.
[[ $ALLOWEDCOUNTRIES =~ $NEWCOUNTRY ]] || NEWCOUNTRY="CH"
# New channel is now valid; set it in config file.
sed -i "/^country_code=/c\country_code=$NEWCOUNTRY" "$CONFIGFILE"
#
# Channel setting.
# Validate new channel. Replace it with 11 if invalid.
[[ $NEWCHANNEL =~ ^[1-9]|1[0-3]$ ]] || NEWCHANNEL="11"
# Channel 12 and 13 aren't valid in Canada and US.
if [[ $NEWCOUNTRY =~ ^(CA|US)$ ]] && [[ $NEWCHANNEL =~ ^1[23]$ ]]; then
    NEWCHANNEL="11"
fi
# New channel is now valid; set it in config file.
sed -i "/^channel=/c\channel=$NEWCHANNEL" "$CONFIGFILE"
#
# SSID setting.
# Validate new SSID. Replace it with 'MoodleBox' if invalid.
# At this point, $NEWSSID is a string of hex values, e.g. "74657374" for "test"
# We want to check that it is valid, and between 1 and 32 bytes.
[[ $NEWSSID =~ ^([0-9a-fA-F]{2}){1,32}$ ]] || NEWSSID="4d6f6f646c65426f78" # "MoodleBox"
# New SSID is now valid; set it in config file.
# Change ssid to ssid2
sed -i "/^ssid=/c\ssid2=$NEWSSID" "$CONFIGFILE"
sed -i "/^ssid2=/c\ssid2=$NEWSSID" "$CONFIGFILE"
if [[ -z $(grep "^utf8_ssid=1$" "$CONFIGFILE") ]]; then # add utf8_ssid param.
    sed -i "/ssid2/a utf8_ssid=1" "$CONFIGFILE"
fi
#
# Password protection setting.
# Validate password protection setting. Replace it with 1 if invalid.
[[ $PASSWORDPROTECTED =~ ^[01]$ ]] || PASSWORDPROTECTED="1"
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
