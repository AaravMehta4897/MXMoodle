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
 * Rooming index page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');

admin_externalpage_setup('rooming_index');

$url = '/local/mxschool/rooming/index.php';
$title = get_string('rooming', 'local_mxschool');

setup_generic_page($url, $title);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\index(array(
    get_string('rooming_preferences', 'local_mxschool') => '/local/mxschool/rooming/preferences.php',
    get_string('rooming_form', 'local_mxschool') => '/local/mxschool/rooming/rooming_enter.php',
    get_string('rooming_report', 'local_mxschool') => '/local/mxschool/rooming/rooming_report.php'
));

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();