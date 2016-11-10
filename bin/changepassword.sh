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
# See http://sylnsr.blogspot.ch/2012/09/keep-unix-password-in-sync-with.html
#
# Configuration
# get directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# path of file containing the new password (plain text)
FILE=${DIR%/*}/.newpassword
# username
USER="moodlebox"
# get oldpassword from Moodle config.php file
OLDPASSWORD="$(grep '\$CFG->dbpass' /var/www/html/config.php | cut -d\' -f2)"
#
# Script
# make sure there is a matching USER, but not the root user
if [ -n "$(getent passwd $USER)" ] && [ $USER != "root" ]; then
    NEWPASSWORD="$(head -n 1 $FILE | sed 's/ *$//g' | sed 's/^ *//g')"
    # change the password if non empty
    if [ -n "$NEWPASSWORD" ]; then
        # 1. change MariaDB root password
        mysql mysql -u root -p"$OLDPASSWORD" -e "UPDATE user SET password=PASSWORD('$NEWPASSWORD') WHERE user='root'; FLUSH PRIVILEGES;"
        # 2. change phpMyAdmin root password, using new root password!
        mysql mysql -u root -p"$NEWPASSWORD" -e "UPDATE user SET password=PASSWORD('$NEWPASSWORD') WHERE user='phpmyadmin'; FLUSH PRIVILEGES;"
        sed -i "/\$dbpass/c\$dbpass='$NEWPASSWORD';" /etc/phpmyadmin/config-db.php
        # 3. change moodlebox account password
        echo $USER:$NEWPASSWORD | chpasswd
        # 4. change Moodle config.php
        sed -i "/\$CFG->dbpass/c\$CFG->dbpass    = '$NEWPASSWORD';" /var/www/html/config.php
    else
        echo "Empty password given"; exit 1;
    fi
fi
# empty file
> $FILE