# MoodleBox Moodle plugin

[![Build Status](https://travis-ci.org/martignoni/moodle-tool_moodlebox.svg?branch=master)](https://travis-ci.org/martignoni/moodle-tool_moodlebox)
[![GitHub release](https://img.shields.io/github/release/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/releases/latest)
[![GitHub Release Date](https://img.shields.io/github/release-date/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/releases/latest)
[![GitHub last commit](https://img.shields.io/github/last-commit/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodlebox/commits/)


A Moodle administration plugin providing a GUI to some settings and management of a [MoodleBox](https://moodlebox.net/), a Moodle server installed on a [Raspberry Pi](http://www.raspberrypi.org/).

This plugin enables a Moodle administrator to monitor some hardware settings, to set the date of the MoodleBox, to allow restart and shutdown of the MoodleBox and changing Raspberry Pi passwords using a GUI. After the installation in Moodle, some steps are required to complete on the Raspberry Pi (see below).

The plugin is compatible with Moodle 3.1 or later.

## Installation

The MoodleBox plugin must be installed in the Moodle tree of the MoodleBox, in the _tool_ folder. Once installed, an new option _MoodleBox_ will be available in Moodle, under _Site administration > Server_ in the _Administration_ block.

To complete the installation, you have to create some files in the plugin folder and configure some incron jobs on the MoodleBox.

1. Create necessary files
    ```bash
    cd /var/www/moodle/admin/tool/moodlebox
    touch .reboot-server; touch .shutdown-server; touch .set-server-datetime; touch .newpassword; touch .wifipassword
    chown -R www-data:www-data /var/www/moodle/admin/tool/moodlebox
    ```

1. Install `incron` package and allow `root` to run it:
    ```bash
    sudo apt-get install incron
    echo root | sudo tee -a /etc/incron.allow
    ```

1. Add following lines to `incrontab`:
    ```bash
    /var/www/moodle/admin/tool/moodlebox/.reboot-server IN_CLOSE_WRITE /sbin/shutdown -r now
    /var/www/moodle/admin/tool/moodlebox/.shutdown-server IN_CLOSE_WRITE /sbin/shutdown -h now
    /var/www/moodle/admin/tool/moodlebox/.set-server-datetime IN_CLOSE_WRITE /bin/bash /var/www/moodle/admin/tool/moodlebox/.set-server-datetime
    /var/www/moodle/admin/tool/moodlebox/.newpassword IN_CLOSE_WRITE /bin/bash /var/www/moodle/admin/tool/moodlebox/bin/changepassword.sh
    /var/www/moodle/admin/tool/moodlebox/.wifisettings IN_CLOSE_WRITE /bin/bash /var/www/moodle/admin/tool/moodlebox/bin/changewifisettings.sh
    ```

## Features

- Info about the MoodleBox (kernel version, Raspbian version, free space on SD card, CPU load, CPU temperature, CPU frequency, uptime, DHCP clients).
- GUI to set the MoodleBox date and time.
- GUI to set the MoodleBox password.
- GUI to set the MoodleBox Wi-Fi network password (or remove it), SSID and channel.
- GUI to restart and shutdown the MoodleBox.

## Availability

The code is available at [https://github.com/moodlebox/moodle-tool_moodlebox](https://github.com/moodlebox/moodle-tool_moodlebox).

### Release notes

See [Release notes](https://github.com/moodlebox/moodle-tool_moodlebox/blob/master/CHANGELOG.md).

## License

Copyright © 2016 onwards, Nicolas Martignoni <nicolas@martignoni.net>

- All the source code is licensed under GPL 3 or any later version
- The documentation is licensed under Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


