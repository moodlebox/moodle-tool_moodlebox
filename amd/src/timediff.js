// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @link       https://github.com/moodlebox/moodle-tool_moodlebox
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module tool_moodlebox/timediff
 */
define(['core/str', 'core/notification'], function(str, notification) {

    return /** @alias module:tool/moodlebox */ {
        /**
         * Global init function for this module.
         *
         * @method init
         * @param {Object} servertime The timestamp of current time on the server.
         */
        init: function(servertime) {
            var usertime = Math.round(M.pageloadstarttime.getTime() / 1000);
            if (Math.abs(usertime - servertime) >= 300) { // Time difference greater than 5 minutes.
                str.get_strings([{'key': 'datetimesetmessage', component: 'tool_moodlebox'}]).done(function(s) {
                    notification.addNotification({
                        message: s[0],
                        type: 'error'
                    });
                }).fail(notification.exception);
            }
        }
    };
});
