<?php
// This file is part of Moodle - http://moodle.org/
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
 * Main file
 *
 * @package    local
 * @subpackage moodlebox
 * @copyright  2016 Nicolas Martignoni
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin = new stdClass();

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__).'/version.php');

admin_externalpage_setup('local_moodlebox', '', null);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'local_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

$PAGE->requires->js('/local/moodlebox/checktime.js', false);
$systemtime = usergetdate(time())[0];
$PAGE->requires->js_init_call('checktime', array($systemtime), false);

exec('uname -srm', $kernelversion);
exec('lsb_release -d | cut -d\':\' -f2', $raspbianversion);
$cpuload = sys_getloadavg();
exec('cat /var/lib/misc/dnsmasq.leases', $leases);
$dhcpclientnumber = count($leases);
exec('awk \'{print $1/1000" °C"}\' /sys/class/thermal/thermal_zone0/temp', $cputemperature);
exec('awk \'{print $1/1000" Mhz"}\' /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq', $cpufrequency);
exec('uptime -p', $uptime);
$moodleboxversion = $plugin->release . ' (' . $plugin->version . ')';

class datetimeset_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('date_time_selector', 'currentdatetime', get_string('datetime', 'local_moodlebox'),
                            array(
                                'startyear' => date("Y") - 2,
                                'stopyear'  => date("Y") + 2,
                                'timezone'  => 99,
                                'step'      => 1,
                                'optional'  => true)
                            );
        $mform->addElement('submit', 'datetimesetbutton', get_string('datetimeset', 'local_moodlebox'));
    }
}

class restartshutdown_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $buttonarray = array();
        $buttonarray[] = & $mform->createElement('submit', 'restartbutton',
                                                  get_string('restart', 'local_moodlebox'));
        $buttonarray[] = & $mform->createElement('submit', 'shutdownbutton',
                                                  get_string('shutdown', 'local_moodlebox'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}

echo $OUTPUT->header();

// System information section
echo $OUTPUT->heading(get_string('systeminfo', 'local_moodlebox'));
echo $OUTPUT->box_start('generalbox');

echo html_writer::start_tag('table', array('class' => 'admintable environmenttable generaltable', 'id' => 'moodleboxstatus'));

echo html_writer::start_tag('thead');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('parameter', 'local_moodlebox'),
        array('class' => 'header c0', 'scope' => 'col', 'width' => '50%'));
echo html_writer::tag('th', get_string('information', 'local_moodlebox'),
        array('class' => 'header c1', 'scope' => 'col', 'width' => '50%'));
echo html_writer::end_tag('tr');
echo html_writer::end_tag('thead');

echo html_writer::start_tag('tbody');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('moodleboxversion', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $moodleboxversion, array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('kernelversion', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $kernelversion[0], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('raspbianversion', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $raspbianversion[0], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('cpuload', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $cpuload[0] . ', ' . $cpuload[1] . ', ' . $cpuload[2], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('cputemperature', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $cputemperature[0], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('cpufrequency', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $cpufrequency[0], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('uptime', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $uptime[0], array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', get_string('dhcpclientnumber', 'local_moodlebox'), array('class' => 'cell c0'));
echo html_writer::tag('td', $dhcpclientnumber, array('class' => 'cell c1'));
echo html_writer::end_tag('tr');
if ($dhcpclientnumber > 0) {
    foreach($leases as $row) {
        $item = explode(' ', $row);
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', get_string('clientinfo', 'local_moodlebox'),
                array('class' => 'cell c1', 'style' => 'padding-left:3em;'));
        echo html_writer::tag('td', $item[2] . ' (' . $item[3] . ')', array('class' => 'cell c1'));
        echo html_writer::end_tag('tr');
    }
}
echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');

echo $OUTPUT->box_end();

// Time setting section
echo $OUTPUT->heading(get_string('datetimesetting', 'local_moodlebox'));
echo $OUTPUT->box_start('generalbox');

$datetimesetform = new datetimeset_form();
$datetimesetform->display();

if ($data = $datetimesetform->get_data()) {
    if (!empty($data->datetimesetbutton)) {
        $datecommand = "date +%s -s @$data->currentdatetime";
        exec("echo $datecommand > .set-server-datetime");
        \core\notification::warning(get_string('datetimemessage', 'local_moodlebox'));
    }
}

echo $OUTPUT->box_end();

// Restart-shutdown section
echo $OUTPUT->heading(get_string('restartstop', 'local_moodlebox'));
echo $OUTPUT->box_start('generalbox');

$restartshutdownform = new restartshutdown_form();
$restartshutdownform->display();

if ($data = $restartshutdownform->get_data()) {
// idea from http://stackoverflow.com/questions/5226728/how-to-shutdown-ubuntu-with-exec-php
// adapted for use with incron
    if (!empty($data->restartbutton)) {
        exec('touch .reboot-server');
        \core\notification::warning(get_string('restartmessage', 'local_moodlebox'));
    }
    if (!empty($data->shutdownbutton)) {
        exec('touch .shutdown-server');
        \core\notification::warning(get_string('shutdownmessage', 'local_moodlebox'));
    }
}

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
