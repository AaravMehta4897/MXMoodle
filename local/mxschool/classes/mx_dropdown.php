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
 * Generic class to encapsulate dropdown fields for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../locallib.php');

class local_mxschool_dropdown {

    /** @var string $name The name of the dropdown, which serves as the url parameter from filter.*/
    public $name;
    /** @param array $options The options for the dropdown.*/
    public $options;
    /** @param string $selected The initially selected option.*/
    public $selected;
    /** @param array|bool $default A 'nothing' option or false if there is no such option.*/
    public $nothing;

    /**
     * @param string $name The name of the dropdown, which serves as the url parameter from filter.
     * @param array $options The options for the dropdown.
     * @param string $selected The initially selected option.
     * @param string|bool $default A 'nothing' option or false if there is no such option.
     */
    public function __construct($name, $options, $selected, $default = false) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->nothing = is_string($default) ? array('' => $default) : false;
    }

    /**
     * Generates a local_mxschool_dropdown object for all dorms and optionally day houses.
     *
     * @param string $selected The currently selected option.
     * @param bool $includeday Whether to include day houses or limit to boading houses.
     * @return local_mxschool_dropdown Object with the specified properties.
     */
    public static function dorm_dropdown($selected, $includeday = true) {
        if ($includeday) {
            $options = array(
                -2 => get_string('report_select_boarding', 'local_mxschool'),
                -1 => get_string('report_select_day', 'local_mxschool')
            ) + get_dorm_list();
            $default = get_string('report_select_house', 'local_mxschool');
        } else {
            $options = get_boarding_dorm_list();
            $default = get_string('report_select_dorm', 'local_mxschool');
        }
        return new local_mxschool_dropdown('dorm', $options, $selected, $default);
    }

}
