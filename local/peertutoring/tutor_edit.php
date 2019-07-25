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
 * Edit page for tutor records for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/tutor_edit_form.php');

require_login();
require_capability('local/peertutoring:manage_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('tutor_edit', 'preferences', null, 'peertutoring');

$queryfields = array('local_peertutoring_tutor' => array('abbreviation' => 't', 'fields' => array(
    'id', 'userid' => 'student', 'departments', 'deleted'
)));

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_peertutoring_tutor', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 't.id = ?', array($id));
    $data->departments = json_decode($data->departments);
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
}

$students = get_eligible_student_list();
$departments = get_department_list();

$form = new tutor_edit_form(array('students' => $students, 'departments' => $departments));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->departments = json_encode($data->departments);
    $existingdeletedid = $DB->get_field('local_peertutoring_tutor', 'id', array('userid' => $data->student));
    if ($existingdeletedid) {
        $data->id = $existingdeletedid;
    }
    $data->deleted = 0;
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string($data->id ? 'tutor_edit_success' : 'tutor_create_success', 'local_peertutoring'),
        $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);
$jsrenderable = new local_mxschool\output\amd_module('local_peertutoring/tutor_form');

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->render($jsrenderable);
echo $output->footer();
