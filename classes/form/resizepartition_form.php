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

/**
 * MoodleBox resize partition form definition.
 *
 * @package    tool_moodlebox
 * @copyright  2018 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @copyright  2024 onwards Patrick Lemaire
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlebox\form;
use moodleform;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * Class resizepartition_form
 *
 * Form class to resize the partition of the MoodleBox.
 *
 * @package    tool_moodlebox
 * @copyright  2018 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resizepartition_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $buttonarray = [];
        $buttonarray[] = & $mform->createElement('submit', 'resizepartitionbutton',
                                                  get_string('resizepartition', 'tool_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }
}
