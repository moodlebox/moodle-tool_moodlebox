# MoodleBox Moodle plugin

This local plugin for Moodle 3.0 or later is provided to help the administrator of the MoodleBox to monitor some hardware settings and allow restart and shutdown of the MoodleBox.

## Availability

The code is available at [https://github.com/martignoni/moodlebox](https://github.com/martignoni/moodlebox).


### Release notes

* 2016-06-16, version 1.0a1 (alpha): first version

## Installation

The plugin needs to be installed in the Moodle tree of the MoodleBox, in the _local_ folder. Once installed, an new option _MoodleBox administration_ will be available in Moodle, under _Site administration > Server_ in the _Administration_ block.

To enable the restart and shutdown feature, an cron job should be defined on the MoodleBox (for root user) in the following way.

```
* * * * * bash /var/www/html/local/moodlebox/lib/checkrebootrestart.sh
```
## Usage

TODO

## License

Copyright © 2016 onwards, Nicolas Martignoni <nicolas@martignoni.net>

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


