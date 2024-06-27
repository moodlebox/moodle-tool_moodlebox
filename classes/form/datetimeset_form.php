<?php
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

namespace tool_moodlebox\form;
use moodleform;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * Class datetimeset_form
 *
 * Form class to set time and date.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datetimeset_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('date_time_selector', 'currentdatetime', get_string('datetime', 'tool_moodlebox'),
                            [
                                'startyear' => date("Y") - 2,
                                'stopyear'  => date("Y") + 2,
                                'timezone'  => 99,
                                'step'      => 1,
                                'optional'  => true,
                            ]);
        $mform->addHelpButton('currentdatetime', 'datetime', 'tool_moodlebox');

        $this->add_action_buttons(false, get_string('datetimeset', 'tool_moodlebox'));
    }
}
