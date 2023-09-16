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

/**
 * Class wifisettings_form
 *
 * Form class to change MoodleBox Wi-Fi settings.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wifisettings_form extends moodleform {

    /**
     * Define the form.
     *
     */
    public function definition() {
        global $currentssid;
        global $currentssidhidden;
        global $currentapchannel;
        global $currentappassword;
        global $currentregcountry;
        global $staticipaddress;

        $mform = $this->_form;

        // SSID setting.
        $mform->addElement('text', 'wifissid', get_string('wifissid', 'tool_moodlebox'));
        $mform->addRule('wifissid', get_string('required'), 'required', null, 'client');
        $mform->setType('wifissid', PARAM_RAW_TRIMMED);
        $mform->setDefault('wifissid', $currentssid);
        $mform->addHelpButton('wifissid', 'wifissid', 'tool_moodlebox');

        // SSID hiding setting.
        $mform->addElement('checkbox', 'wifissidhiddenstate', get_string('wifissidhiddenstate', 'tool_moodlebox'),
            ' ' . get_string('wifissidhidden', 'tool_moodlebox'));
        $mform->setDefault('wifissidhiddenstate', $currentssidhidden ? 1 : 0);
        $mform->setType('wifissidhiddenstate', PARAM_INT);
        $mform->addHelpButton('wifissidhiddenstate', 'wifissidhiddenstate', 'tool_moodlebox');

        // Channel setting.
        if ($currentregcountry == 'US' || $currentregcountry == 'CA') {
            $wifichannelrange = range(1, 11);
        } else {
            $wifichannelrange = range(1, 13);
        }
        $mform->addElement('select', 'wifichannel', get_string('wifichannel', 'tool_moodlebox'),
                array_combine($wifichannelrange, $wifichannelrange));
        $mform->addRule('wifichannel', get_string('required'), 'required', null, 'client');
        $mform->setType('wifichannel', PARAM_INT);
        $mform->setDefault('wifichannel', $currentapchannel);
        $mform->addHelpButton('wifichannel', 'wifichannel', 'tool_moodlebox');

        // Regulatory country setting.
        $mform->addElement('select', 'wificountry', get_string('wificountry', 'tool_moodlebox'),
                get_string_manager()->get_list_of_countries(true));
        $mform->addRule('wificountry', get_string('required'), 'required', null, 'client');
        $mform->setType('wificountry', PARAM_RAW);
        $mform->setDefault('wificountry', $currentregcountry);
        $mform->addHelpButton('wificountry', 'wificountry', 'tool_moodlebox');

        // Password protection setting.
        $mform->addElement('checkbox', 'wifipasswordon', get_string('wifipasswordon', 'tool_moodlebox'),
            ' ' . get_string('passwordprotected', 'tool_moodlebox'));
        $mform->setDefault('wifipasswordon', ($currentappassword == null) ? 0 : 1);
        $mform->setType('wifipasswordon', PARAM_INT);
        $mform->addHelpButton('wifipasswordon', 'wifipasswordon', 'tool_moodlebox');

        // Password setting.
        $mform->addElement('text', 'wifipassword', get_string('wifipassword', 'tool_moodlebox'));
        $mform->disabledIf('wifipassword', 'wifipasswordon');
        $mform->setType('wifipassword', PARAM_RAW_TRIMMED);
        $mform->setDefault('wifipassword', ($currentappassword == null) ? 'moodlebox' : $currentappassword);
        $mform->addHelpButton('wifipassword', 'wifipassword', 'tool_moodlebox');

        // IP setting.
        $mform->addElement('text', 'staticipaddress', get_string('staticipaddress', 'tool_moodlebox'));
        $mform->addRule('staticipaddress', get_string('required'), 'required', null, 'client');
        $mform->setType('staticipaddress', PARAM_RAW);
        $mform->setDefault('staticipaddress', $staticipaddress);
        $mform->addHelpButton('staticipaddress', 'staticipaddress', 'tool_moodlebox');

        $this->add_action_buttons(false, get_string('changewifisettings', 'tool_moodlebox'));
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

        // SSID must have a length between 1 and 32 bytes.
        if (mb_strlen($data['wifissid'], '8bit') > 32 || mb_strlen($data['wifissid'], '8bit') < 1) {
            $errors['wifissid'] = get_string('wifissidinvalid', 'tool_moodlebox');
        }

        // Password must have 8 to 63 ASCII printable characters.
        // See IEEE Std. 802.11i-2004, Annex H.4.1.
        if (!preg_match('/^[ -~]{8,63}$/', $data['wifipassword']) ) {
            $errors['wifipassword'] = get_string('wifipasswordinvalid', 'tool_moodlebox');
        }

        // Validate IP address.
        if (!\tool_moodlebox\local\utils::is_private_ipv4_address($data['staticipaddress'])) {
            $errors['staticipaddress'] = get_string('staticipaddressinvalid', 'tool_moodlebox');
        }

        return $errors;
    }
}

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

/**
 * Class restartshutdown_form
 *
 * Form class to restart and shutdown the MoodleBox.
 *
 * @package    tool_moodlebox
 * @copyright  2016 onwards Nicolas Martignoni {@link mailto:nicolas@martignoni.net}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 w later
 */
class restartshutdown_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;
        $buttonarray = [];
        $buttonarray[] = & $mform->createElement('submit', 'restartbutton',
                                                  get_string('restart', 'tool_moodlebox'));
        $buttonarray[] = & $mform->createElement('submit', 'shutdownbutton',
                                                  get_string('shutdown', 'tool_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }
}
