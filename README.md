# MoodleBox Moodle plugin

A Moodle administration plugin providing a GUI to some settings and management of a [MoodleBox](https://github.com/martignoni/make-moodlebox).

This plugin enable an administrator to monitor some hardware settings, to set the date of the MoodleBox, to allow restart and shutdown of the MoodleBox and changing Raspberry Pi passwords using a GUI. The plugin is compatible with Moodle 3.1 or later.

## Availability

The code is available at [https://github.com/martignoni/moodle-tool_moodlebox](https://github.com/martignoni/moodle-tool_moodlebox).

### Release notes

* 2016-12-06, version 1.4.3: Bug fixed for use with Moodle 3.2
* 2016-10-08, version 1.4.2: Display warnings when the plugin installation is not complete
* 2016-09-25, version 1.4.1: MoodleBox Wi-Fi network password cannot be changed to empty
* 2016-09-18, version 1.4: New option enabling to change the MoodleBox Wi-Fi network password
* 2016-09-10, version 1.3: New option enabling to change the MoodleBox password
* 2016-08-09, version 1.2: Changed to admin tool plugin (from local plugin)
* 2016-08-06, version 1.1: Added display of free space on SD card
* 2016-07-11, version 1.0: Added time setting feature
* 2016-06-26, version 1.0b (beta): Added two folder as RAM disks, for better performance
* 2016-06-19, version 1.0a2 (alpha): Reorganisation of project
* 2016-06-16, version 1.0a1 (alpha): First version

## Installation

The MoodleBox plugin must be installed in the Moodle tree of the MoodleBox, in the _tool_ folder. Once installed, an new option _MoodleBox_ will be available in Moodle, under _Site administration > Server_ in the _Administration_ block.

To complete the installation, you have to create some files in the plugin folder and configure some incron jobs on the MoodleBox. These steps are described in the [documentation on creating a MoodleBox](https://github.com/martignoni/make-moodlebox/blob/master/doc/Moodlebox.pdf).

## Features

* Info about the MoodleBox (kernel version, Raspbian version, free space on SD card, CPU load, CPU temperature, CPU frequency, uptime, DHCP clients).
* GUI to set the MoodleBox date and time.
* GUI to set the MoodleBox password.
* GUI to set the MoodleBox Wi-Fi network password.
* GUI to restart and shutdown the MoodleBox.

## License

Copyright © 2016 onwards, Nicolas Martignoni <nicolas@martignoni.net>

* All the source code is licensed under GPL 3 or any later version
* The documentation is licensed under Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


