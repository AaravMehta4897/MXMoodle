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
 * Form for editing advisor selection preferences for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_form.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $dateparameters = array(
            'startyear' => strftime('%Y', get_config('local_mxschool', 'dorms_open_date')),
            'stopyear' => strftime('%Y', get_config('local_mxschool', 'dorms_close_date')),
            'timezone'  => core_date::get_server_timezone_object()
        );
        $unsubmittedtags = implode(', ', array_map(function($tag) {
            return "{{$tag}}";
        }, array('studentname', 'salutation')));
        $resultstags = implode(', ', array_map(function($tag) {
            return "{{$tag}}";
        }, array('studentname', 'salutation', 'advisorname')));

        $fields = array(
            'availability' => array(
                'start' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                    'time' => parent::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
                )), 'stop' => array('element' => 'group', 'separator' => '&nbsp;', 'children' => array(
                    'time' => parent::time_selector(1),
                    'date' => array('element' => 'date_selector', 'parameters' => $dateparameters)
                )), 'who' => array('element' => 'radio', 'options' => array('new', 'all'), 'rules' => array('required'))
            ), 'notifications' => array(
                'unsubmittedavailable' => array('element' => 'static', 'text' => $unsubmittedtags),
                'unsubmittedsubject' => parent::ELEMENT_LONG_TEXT_REQUIRED,
                'unsubmittedbody' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'resultsavailable' => array('element' => 'static', 'text' => $resultstags),
                'resultssubject' => parent::ELEMENT_LONG_TEXT_REQUIRED,
                'resultsbody' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            ), 'text' => array(
                'closing_warning' => parent::ELEMENT_FORMATED_TEXT_REQUIRED,
                'instructions' => parent::ELEMENT_FORMATED_TEXT_REQUIRED
            )
        );
        parent::set_fields($fields, 'advisor_selection_preferences', true);
    }

}
