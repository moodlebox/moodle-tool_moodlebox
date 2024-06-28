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
 * MoodleBox change password form definition.
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
 * Class changepassword_form
 *
 * Form class to change MoodleBox password.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class changepassword_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('passwordunmask', 'newpassword1', get_string('newpassword'));
        $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword1', PARAM_RAW_TRIMMED);

        $mform->addElement('passwordunmask', 'newpassword2', get_string('newpassword').' ('.get_string('again').')');
        $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword2', PARAM_RAW_TRIMMED);

        $this->add_action_buttons(false, get_string('changepassword'));
    }

    /**
     * Validate the form.
     *
     * @param array $data submitted data
     * @param array $files not used
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
        }

        return $errors;
    }
}
