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
 * Form for peer tutors to log their tutoring sessions for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_peertutoring\local;

defined('MOODLE_INTERNAL') || die();

class form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $tutors = $this->_customdata['tutors'];
        $students = $this->_customdata['students'];
        $departments = $this->_customdata['departments'];
        $courses = $this->_customdata['courses'];
        $types = $this->_customdata['types'];
        $ratings = $this->_customdata['ratings'];

        $fields = array(
            '' => array(
                'id' => self::ELEMENT_HIDDEN_INT,
                'timecreated' => self::ELEMENT_HIDDEN_INT,
                'isstudent' => self::ELEMENT_HIDDEN_INT
            ),
            'info' => array(
                'tutor' => array('element' => 'select', 'options' => $tutors),
                'tutoringdate' => array('element' => 'date_selector', 'options' => self::date_options_school_year()),
                'student' => array('element' => 'select', 'options' => $students)
            ),
            'details' => array(
                'department' => array('element' => 'select', 'options' => $departments),
                'course' => array('element' => 'select', 'options' => $courses),
                'topic' => self::ELEMENT_TEXT,
                'type' => array('element' => 'group', 'children' => array(
                    'select' => array('element' => 'select', 'options' => $types),
                    'other' => self::ELEMENT_TEXT
                )),
                'rating' => array('element' => 'select', 'options' => $ratings),
                'notes' => self::ELEMENT_TEXT_AREA
            )
        );
        $this->set_fields($fields, 'form', false, 'local_peertutoring');

        $mform = $this->_form;
        $mform->hideIf('tutor', 'isstudent', 'eq');
        $mform->hideIf('type_other', 'type_select', 'neq', '-1');
    }

    /**
     * Validates the tutoring form before it can be submitted.
     * The checks performed are to ensure that all required fields are filled out.
     *
     * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!$data['department']) {
            $errors['department'] = get_string('form:error:no_department', 'local_peertutoring');
        }
        if (!$data['course']) {
            $errors['course'] = get_string('form:error:no_course', 'local_peertutoring');
        }
        if (!$data['topic']) {
            $errors['topic'] = get_string('form:error:no_topic', 'local_peertutoring');
        }
        if (!$data['type_select'] || ($data['type_select'] === '-1' && empty($data['type_other']))) {
            $errors['type'] = get_string('form:error:no_type', 'local_peertutoring');
        }
        if (!$data['rating']) {
            $errors['rating'] = get_string('form:error:no_rating', 'local_peertutoring');
        }
        return $errors;
    }

}
