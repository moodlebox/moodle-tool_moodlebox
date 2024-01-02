# MoodleBox Moodle plugin

[![Build Status](https://github.com/moodlebox/moodlebox/workflows/CI/badge.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/actions?query=workflow%3ACI)
[![GitHub release](https://img.shields.io/github/release/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/releases/latest)
[![GitHub Release Date](https://img.shields.io/github/release-date/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/releases/latest)
[![GitHub last commit](https://img.shields.io/github/last-commit/moodlebox/moodle-tool_moodlebox.svg)](https://github.com/moodlebox/moodle-tool_moodlebox/commits/)

A Moodle administration plugin providing a GUI to some settings and management of a [MoodleBox](https://moodlebox.net/), a Moodle server installed on a [Raspberry Pi](https://www.raspberrypi.org/).

This plugin enables a Moodle administrator to monitor some hardware settings, to set the date of the MoodleBox, to allow restart and shutdown of the MoodleBox and changing Raspberry Pi passwords using a GUI. After the installation in Moodle, some steps are required to complete on the Raspberry Pi (see below).

Administrators and users with manager role can moreover restart and shutdown the MoodleBox with buttons in the footer of each Moodle page.

The plugin is compatible with Moodle 3.6 or later. A Raspberry Pi model Zero 2 W, 3A+, 3B, 3B+, 4B or 5 is recommended.

## Installation

The MoodleBox plugin must be installed in the Moodle tree of the MoodleBox, in the _tool_ folder. Once installed, an new option _MoodleBox_ will be available in Moodle, under _Site administration > Server_ in the _Administration_ block.

To complete the installation, you have to configure some `direvent` jobs on the MoodleBox.

1. Install `direvent` package:
    ```bash
    sudo apt-get install direvent
    ```

1. Add following lines to file `/etc/direvent.conf`:
    ```bash
    # This is the configuration file for direvent. Read
    # direvent.conf(5) for more information about how to
    # fill this file.

    debug 0;

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .reboot-server;
      event CLOSE_WRITE;
      command "/sbin/shutdown -r now";
    }

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .shutdown-server;
      event CLOSE_WRITE;
      command "/sbin/shutdown -h now";
    }

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .set-server-datetime;
      event CLOSE_WRITE;
      command "/bin/bash /var/www/moodle/admin/tool/moodlebox/.set-server-datetime";
    }

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .newpassword;
      event CLOSE_WRITE;
      command "/bin/bash /var/www/moodle/admin/tool/moodlebox/bin/changepassword.sh";
    }

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .wifisettings;
      event CLOSE_WRITE;
      command "/usr/bin/python3 /var/www/moodle/admin/tool/moodlebox/bin/changewifisettings.py";
    }

    watcher {
      path /var/www/moodle/admin/tool/moodlebox/;
      file .resize-partition;
      event CLOSE_WRITE;
      command "/bin/bash /var/www/moodle/admin/tool/moodlebox/bin/resizepartition.sh";
    }
    ```

1. Copy the following line at the end of file `/etc/sudoers.d/020_www-data-nopasswd` (create it if it's not here):
    ```bash
    www-data ALL=(ALL) NOPASSWD:/sbin/parted /dev/mmcblk0 unit MB print free
    www-data ALL=(ALL) NOPASSWD:/usr/bin/vcgencmd
    ```

1. If you use the [PiJuice module](https://github.com/PiSupply/PiJuice), you need to install the packages related
    ```bash
    sudo apt-get install pijuice-base
    ```
   then allow www-data to access I2C:
    ```bash
    sudo adduser www-data i2c
    ```
   and reboot.

## Features

- Info about the MoodleBox (kernel version, Raspberry Pi OS version, free space on SD card, CPU load, CPU temperature, CPU frequency, uptime, DHCP clients and more).
- Warning when under voltage detected.
- GUI to set the MoodleBox date and time.
- GUI to set the MoodleBox password.
- GUI to set the MoodleBox Wi-Fi settings: SSID and its visibility, regulatory country, channel, password (or remove password) and fixed IP address.
- GUI to resize the partition of the SD card of the MoodleBox, when needed.
- GUI to restart and shutdown the MoodleBox.

## Availability

The code is available at [https://github.com/moodlebox/moodle-tool_moodlebox](https://github.com/moodlebox/moodle-tool_moodlebox).

### Release notes

See [Release notes](https://github.com/moodlebox/moodle-tool_moodlebox/blob/master/CHANGELOG.md).

## Thanks

- To Adrian Perez (@adpe), for implementing restart and shutdown buttons in footer.
- To Vincent Widmer (@smallhacks), for implementing PiJuice support.
- To Visvanath Ratnaweera (@ratnavis), who kindly donated a Raspberry Pi 3A+ and loaned a Raspberry Pi 4 8GB, enabling support of these Raspberry Pi models.

## License

Copyright © 2016 onwards, Nicolas Martignoni <nicolas@martignoni.net>

- All the source code is licensed under GPL 3 or any later version
- The documentation is licensed under Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
