#!/bin/bash

# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
#
#  * Script file to handle reboot/shutdown of Moodlebox
#  *
#  * @package    local
#  * @subpackage moodlebox
#  * @copyright  2016 Nicolas Martignoni
#  * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
#  */

# See http://stackoverflow.com/questions/5226728/how-to-shutdown-ubuntu-with-exec-php
if [ -f /var/www/html/local/moodlebox/.reboot-server ]; then
  rm -f /var/www/html/local/moodlebox/.reboot-server
  if [ -f /var/www/html/local/moodlebox/.reboot-server ]; then
     echo "Can't remove file .reboot-server"
  else
    rsync -a --delete /var/cache/moodle/ /var/cache/moodle-cache-backup/
    /sbin/shutdown -r now
  fi
fi

if [ -f /var/www/html/local/moodlebox/.shutdown-server ]; then
  rm -f /var/www/html/local/moodlebox/.shutdown-server
  if [ -f /var/www/html/local/moodlebox/.shutdown-server ]; then
     echo "Can't remove file .shutdown-server"
  else
    rsync -a --delete /var/cache/moodle/ /var/cache/moodle-cache-backup/
    /sbin/shutdown -h now
  fi
fi
