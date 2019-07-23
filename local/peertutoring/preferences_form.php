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
 * Form for editing preferences for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_form.php');
require_once(__DIR__.'/classes/notification/pt_notification.php');

class preferences_form extends local_mxschool_form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
            'notifications' => array(
                'tags' => self::email_tags(new \local_peertutoring\local\daily_summary()),
                'subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                'body' => self::ELEMENT_FORMATED_TEXT_REQUIRED,
            )
        );
        $this->set_fields($fields, 'preferences', true, 'local_peertutoring');
    }

}
