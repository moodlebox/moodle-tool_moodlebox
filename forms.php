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
 * MoodleBox dashboard form definition.
 *
 * @package    tool_moodlebox
 * @copyright  2018 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Class datetimeset_form
 *
 * Form class to set time and date.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datetimeset_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('date_time_selector', 'currentdatetime', get_string('datetime', 'tool_moodlebox'),
                            array(
                                'startyear' => date("Y") - 2,
                                'stopyear'  => date("Y") + 2,
                                'timezone'  => 99,
                                'step'      => 1,
                                'optional'  => true)
                            );

        $this->add_action_buttons(false, get_string('datetimeset', 'tool_moodlebox'));
    }
}

/**
 * Class changepassword_form
 *
 * Form class to change MoodleBox password.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
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
     * @param array $data submitted data
     * @param array $files not used
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = array();

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
        }

        return $errors;
    }
}

/**
 * Class wifisettings_form
 *
 * Form class to change MoodleBox Wi-Fi settings.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wifisettings_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $currentwifissid;
        global $currentwifichannel;
        global $currentwifipassword;
        global $currentwificountry;

        $mform = $this->_form;

        // SSID setting.
        $mform->addElement('text', 'wifissid', get_string('wifissid', 'tool_moodlebox'));
        $mform->addRule('wifissid', get_string('required'), 'required', null, 'client');
        $mform->setType('wifissid', PARAM_RAW_TRIMMED);
        $mform->setDefault('wifissid', $currentwifissid);

        // Channel setting.
        if ($currentwificountry == 'US' or $currentwificountry == 'CA') {
            $wifichannelrange = range(1, 11);
        } else {
            $wifichannelrange = range(1, 13);
        }
        $mform->addElement('select', 'wifichannel', get_string('wifichannel', 'tool_moodlebox'),
                array_combine($wifichannelrange, $wifichannelrange));
        $mform->addRule('wifichannel', get_string('required'), 'required', null, 'client');
        $mform->setType('wifichannel', PARAM_INT);
        $mform->setDefault('wifichannel', $currentwifichannel);

        // Regulatory country setting.
        $mform->addElement('select', 'wificountry', get_string('wificountry', 'tool_moodlebox'),
                get_string_manager()->get_list_of_countries(true));
        $mform->addRule('wificountry', get_string('required'), 'required', null, 'client');
        $mform->setType('wificountry', PARAM_RAW);
        $mform->setDefault('wificountry', $currentwificountry);

        // Password protection setting.
        $mform->addElement('checkbox', 'wifipasswordon', get_string('wifipasswordon', 'tool_moodlebox'),
            ' ' . get_string('wifipasswordonhelp', 'tool_moodlebox'));
        $mform->setDefault('wifipasswordon', ($currentwifipassword == null) ? 0 : 1);
        $mform->setType('wifipasswordon', PARAM_INT);

        // Password setting.
        $mform->addElement('text', 'wifipassword', get_string('wifipassword', 'tool_moodlebox'));
        $mform->disabledIf('wifipassword', 'wifipasswordon');
        $mform->setType('wifipassword', PARAM_RAW_TRIMMED);
        $mform->setDefault('wifipassword', ($currentwifipassword == null) ? 'moodlebox' : $currentwifipassword);

        $this->add_action_buttons(false, get_string('changewifisettings', 'tool_moodlebox'));
    }

}

/**
 * Class resizepartition_form
 *
 * Form class to resize the partition of the MoodleBox.
 *
 * @package    tool_moodlebox
 * @copyright  2018 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resizepartition_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $buttonarray = array();
        $buttonarray[] = & $mform->createElement('submit', 'resizepartitionbutton',
                                                  get_string('resizepartition', 'tool_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}

/**
 * Class restartshutdown_form
 *
 * Form class to restart and shutdown the MoodleBox.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restartshutdown_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $buttonarray = array();
        $buttonarray[] = & $mform->createElement('submit', 'restartbutton',
                                                  get_string('restart', 'tool_moodlebox'));
        $buttonarray[] = & $mform->createElement('submit', 'shutdownbutton',
                                                  get_string('shutdown', 'tool_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}
