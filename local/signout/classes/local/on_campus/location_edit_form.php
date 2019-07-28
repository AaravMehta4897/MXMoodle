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
 * Form for editing on-campus location data for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signout\local\on_campus;

defined('MOODLE_INTERNAL') || die();

class location_edit_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT
            ),
            'location' => array(
                'name' => self::ELEMENT_TEXT_REQUIRED,
                'grade' => array('element' => 'radio', 'options' => array(9, 10, 11, 12), 'rules' => array('required')),
                'enabled' => self::ELEMENT_BOOLEAN_REQUIRED,
                'start' => array('element' => 'date_selector', 'options' => self::date_options_school_year(true)),
                'end' => array('element' => 'date_selector', 'options' => self::date_options_school_year(true)),
                'warning' => self::ELEMENT_FORMATTED_TEXT
            )
        );
        $this->set_fields($fields, 'on_campus_location_edit', false, 'local_signout');
    }
}
