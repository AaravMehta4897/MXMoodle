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
 * Tutoring Report for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once('tutoring_table.php');
require_once(__DIR__.'/../mxschool/classes/mx_dropdown.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/../mxschool/classes/events/page_visited.php');
require_once('locallib.php');

require_login();
require_capability('local/peertutoring:manage_tutoring', context_system::instance());

$filter = new stdClass();
$filter->tutor = optional_param('tutor', 0, PARAM_INT);
$filter->department = optional_param('department', 0, PARAM_INT);
$filter->type = optional_param('type', 0, PARAM_INT);
$filter->date = optional_param('date', 0, PARAM_INT);
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_peertutoring') => '/local/peertutoring/index.php'
);
$url = '/local/peertutoring/tutoring_report.php';
$title = get_string('tutoring_report', 'local_peertutoring');

if ($action === 'delete' && $id) {
    $record = $DB->get_record('local_peertutoring_session', array('id' => $id));
    $urlparams = array(
        'tutor' => $filter->tutor, 'department' => $filter->department, 'type' => $filter->type, 'date' => $filter->date,
        'search' => $filter->search
    );
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_peertutoring_session', $record);
        redirect(
            new moodle_url($url, $urlparams), get_string('session_delete_success', 'local_peertutoring'), null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url($url, $urlparams), get_string('session_delete_failure', 'local_peertutoring'), null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

$tutors = get_tutor_list();
$departments = get_department_list();
$types = get_type_list();
$dates = get_tutoring_dates_list();

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

$table = new tutoring_table($filter, $download);

$dropdowns = array(
    new local_mxschool_dropdown(
        'date', $dates, $filter->date, get_string('tutoring_report_select_date_all', 'local_peertutoring')
    ), new local_mxschool_dropdown(
        'tutor', $tutors, $filter->tutor, get_string('tutoring_report_select_tutor_all', 'local_peertutoring')
    ), new local_mxschool_dropdown(
        'department', $departments, $filter->department, get_string('tutoring_report_select_department_all', 'local_peertutoring')
    ), new local_mxschool_dropdown(
        'type', $types, $filter->type, get_string('tutoring_report_select_type_all', 'local_peertutoring')
    )
);
$addbutton = new stdClass();
$addbutton->text = get_string('tutoring_report_add', 'local_peertutoring');
$addbutton->url = new moodle_url('/local/peertutoring/tutoring_enter.php');

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new \local_mxschool\output\report_table($table, $DB->count_records('local_peertutoring_session'));
    echo $output->render($renderable);
    die();
}
$renderable = new \local_mxschool\output\report_page($table, 50, $filter->search, $dropdowns, true, $addbutton);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
