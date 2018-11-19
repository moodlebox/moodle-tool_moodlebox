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
 * @see        https://github.com/moodlebox/moodle-tool_moodlebox
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @author     2018 Adrian Perez Rodriguez {@link mailto:p.adrian@gmx.ch}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
  * @module tool_moodlebox/footerform
  */
define(['jquery'], function($) {

    return /** @alias module:tool/moodlebox */ {
        /**
         * Global init function for this module.
         *
         * @method init
         */
        init: function() {
            $('#rebootbox,#shutdownbox').click(function() {
                $('#restartstopvalue').val($(this).attr('name'));
                $('#footerrestartstop').submit();
            });
        }
    };
});
