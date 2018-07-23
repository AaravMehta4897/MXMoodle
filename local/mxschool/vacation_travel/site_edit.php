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
 * Edit page for vacation travel site records for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once('site_edit_form.php');

require_login();
require_capability('local/mxschool:manage_vacation_travel_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('vacation_travel', 'local_mxschool') => '/local/mxschool/vacation_travel/index.php',
    get_string('vacation_travel_preferences', 'local_mxschool') => '/local/mxschool/vacation_travel/preferences.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/vacation_travel/site_edit.php';
$title = get_string('vacation_travel_site_edit', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_vt_site' => array('abbreviation' => 's', 'fields' => array(
    'id', 'name', 'type', 'enabled_departure' => 'departureenabled', 'enabled_return' => 'returnenabled'
)));

if ($id && !$DB->record_exists('local_mxschool_vt_site', array('id' => $id))) {
    redirect($redirect);
}

$data = get_record($queryfields, 's.id = ?', array($id));

$form = new site_edit_form(array('id' => $id));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), $data->id ? get_string('vacation_travel_edit_success', 'local_mxschool')
        : get_string('vacation_travel_create_success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();