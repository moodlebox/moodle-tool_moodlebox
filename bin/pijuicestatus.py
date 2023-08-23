#!/usr/bin/env python3
"""
This script is part of MoodleBox plugin for moodlebox
Copyright (C) 2021 onwards Nicolas Martignoni

This script is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This script  is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this script.  If not, see <https://www.gnu.org/licenses/>.
"""
import sys

try:
    from pijuice import PiJuice
except ModuleNotFoundError:
    print('{"status_error": "Installation problem", "is_fault": "No module named \'pijuice\' installed"}')
    sys.exit(1)

# Instantiate PiJuice interface object.
pijuice = PiJuice(1, 0x14)

# PiJuice GetStatus.
status_error = pijuice.status.GetStatus()['error']
is_fault = str(pijuice.status.GetStatus()['data']['isFault'])
battery_status = pijuice.status.GetStatus()['data']['battery']
battery_temp = str(pijuice.status.GetBatteryTemperature()['data'])

# PiJuice GetChargeLevel.
charge_level_error = pijuice.status.GetChargeLevel()['error']
charge_level = str(pijuice.status.GetChargeLevel()['data'])

# Print the result as JSON format.
print('{"status_error": "'+status_error+'", "is_fault": "'+is_fault+'", "battery_status": "'+battery_status+'", "battery_temp": "'+battery_temp+'", "charge_level_error": "'+charge_level_error+'", "charge_level": "'+charge_level+'"}')

sys.exit(0)
