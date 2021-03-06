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
 * Content for BLOCK_DESCRIPTION.
 *
 * @package    block_mxschool_BLOCK
 * @author     PRIMARY AUTHOR
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../local/mxschool/locallib.php'); // TODO: If there is a more specific dependency, use that instead.

class block_mxschool_BLOCK extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_mxschool_BLOCK');
    }

    public function get_content() {
        global $PAGE, $USER;
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        if (/* Permissions checks to verify that the current user can access the block. */) {
            $output = $PAGE->get_renderer('PRIMARY_DEPENDECY');
            $renderable = new local_mxschool\output\index(array(
                get_string('LINK1', 'block_mxschool_BLOCK') => 'URL1',
                get_string('LINK2', 'block_mxschool_BLOCK') => 'URL2',
                // Etc.
            ));
            $this->content->text = $output->render($renderable);
        }

        return $this->content;
    }

    public function specialization() {
        $this->title = get_string('blockname', 'block_mxschool_BLOCK');
    }
}
