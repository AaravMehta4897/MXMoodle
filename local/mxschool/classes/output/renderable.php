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
 * Provides renderable classes for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use html_writer;

/**
 * Renderable class for index pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {

    /** @var array $links array of links [unlocalizedtext => url] to be passed to the template.*/
    private $links;

    /**
     * @param array $links array of links [unlocalizedtext => url] to be passed to the template.
     */
    public function __construct($links) {
        $this->links = $links;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with property links which is an array of stdClass with properties text and url.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $data = new stdClass();
        $data->links = array();
        foreach ($this->links as $text => $url) {
            $data->links[] = array('text' => get_string($text, 'local_mxschool'), 'url' => $CFG->wwwroot.$url);
        }
        return $data;
    }
}

/**
 * Renderable class for report pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_page implements renderable, templatable {

    /** @var string $id An id to tag this report for custom formatting.*/
    private $id;
    /** @var report_table $table The table for the report.*/
    private $table;
    /** @var report_filter $filter The filter for the report.*/
    private $filter;

    /**
     * @param string $id An id to tag this report for custom formatting.
     * @param mx_table $table The table object to output to the template
     * @param int $size The number of rows to output.
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool_dropdown objects.
     * @param bool $printbutton Whether to display a print button.
     * @param stdClass|bool $addbutton Object with text and url properties for an add button or false.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     * @param stdClass|bool $highlight Object with formatcolumn and referencecolumn properties or false.
     */
    public function __construct(
        $id, $table, $size, $search = null, $dropdowns = array(), $printbutton = false, $addbutton = false, $headers = false,
        $highlight = false
    ) {
        $this->id = $id;
        $this->table = new report_table($table, $size, $headers, $highlight);
        $this->filter = new report_filter($search, $dropdowns, $printbutton, $addbutton);
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties id, filter, and table.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->id = $this->id;
        $data->filter = $output->render($this->filter);
        $data->table = $output->render($this->table);
        return $data;
    }

}

/**
 * Renderable class for report tables.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table implements renderable, templatable {

    /** @var mx_table $table The table object to output to the template.*/
    private $table;
    /** @var int $size The number of rows to output.*/
    private $size;
    /** @var array|bool $headers Array of headers as ['text', 'length'] to prepend or false.*/
    private $headers;
    /** @var @param stdClass|bool $highlight Object with formatcolumn and referencecolumn properties or false.*/
    private $highlight;

    /**
     * @param mx_table $table The table object to output to the template
     * @param int $size The number of rows to output.
     * @param array|bool $headers Array of headers as ['text', 'length'] to prepend or false.
     * @param stdClass|bool $highlight Object with formatcolumn and referencecolumn properties or false.
     */
    public function __construct($table, $size, $headers, $highlight) {
        $this->table = $table;
        $this->size = $size;
        $this->headers = $headers;
        $this->highlight = $highlight;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with property table.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->table->out($this->size, true);
        $data->table = ob_get_clean();
        $data->headers = $this->headers ? json_encode($this->headers) : $this->headers;
        $data->highlight = $this->highlight;
        return $data;
    }

}

/**
 * Renderable class for report filters.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_filter implements renderable, templatable {

    /** @var string $search Default search text, null if there is no search option.*/
    private $search;
    /** @param array $dropdowns Array of local_mxschool_dropdown objects.*/
    private $dropdowns;
    /** @var bool $printbutton Whether to display a print button.*/
    private $printbutton;
    /** @var stdClass|bool $addbutton Object with text and url properties for an add button or false.*/
    private $addbutton;

    /**
     * @param string $search Default search text, null if there is no search option.
     * @param array $dropdowns Array of local_mxschool_dropdown objects.
     * @param bool $printbutton Whether to display a print button.
     * @param stdClass|bool $addbutton Object with text and url properties for an add button or false.
     */
    public function __construct($search, $dropdowns, $printbutton, $addbutton) {
        $this->search = $search;
        $this->dropdowns = $dropdowns;
        $this->printbutton = $printbutton;
        $this->addbutton = $addbutton;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties url, dropdowns, searchable, search, printable, and addbutton.
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $data = new stdClass();
        $data->url = $PAGE->url;
        $data->dropdowns = array();
        foreach ($this->dropdowns as $dropdown) {
            $data->dropdowns[] = html_writer::select($dropdown->options, $dropdown->name, $dropdown->selected, $dropdown->nothing);
        }
        $data->searchable = $this->search !== null;
        $data->search = $this->search;
        $data->printable = $this->printbutton;
        if ($this->addbutton) {
            $data->addbutton = new stdClass();
            $data->addbutton->text = $this->addbutton->text;
            $data->addbutton->url = $this->addbutton->url->out();
        }
        return $data;
    }

}

/**
 * Renderable class for form pages.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form_page implements renderable, templatable {

    /** @var moodleform $form The form object to render.*/
    private $form;
    /** @var string|bool $topdescription A description for the top of the form or false.*/
    private $descrption;
    /** @var string|bool $bottomdescription A description for the bottom of the form or false.*/
    private $bottomdescription;

    /**
     * @param moodleform $form The form object to render.
     * @param string|bool $topdescription A description for the top of the form or false.
     * @param string|bool $bottomdescription A description for the bottom of the form or false.
     */
    public function __construct($form, $topdescription = false, $bottomdescription = false) {
        $this->form = $form;
        $this->topdescription = $topdescription;
        $this->bottomdescription = $bottomdescription;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties form, topdescription, and bottomdescription.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        ob_start();
        $this->form->display();
        $data->form = ob_get_clean();
        $data->topdescription = $this->topdescription;
        $data->bottomdescription = $this->bottomdescription;
        return $data;
    }
}

/**
 * Renderable class for checkboxes.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox implements renderable, templatable {

    /** @var string $value The value attribute of the checkbox.*/
    private $value;
    /** @var string $name The name attribute of the checkbox.*/
    private $name;
    /** @var bool $checked Whether the checkbox should be checked.*/
    private $checked;
    /** @var string $table The table in the database which the checkbox corresponds to.*/
    private $table;

    /**
     * @param string $value The value attribute of the checkbox.
     * @param string $name The name attribute of the checkbox.
     * @param bool $checked Whether the checkbox should be checked.
     * @param string $table The table in the database which the checkbox corresponds to.
     */
    public function __construct($value, $name, $checked, $table) {
        $this->value = $value;
        $this->name = $name;
        $this->checked = $checked;
        $this->table = $table;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value, name, and checked.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->value = $this->value;
        $data->name = $this->name;
        $data->checked = $this->checked;
        $data->table = $this->table;
        return $data;
    }

}

/**
 * Renderable class for email buttons.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_button implements renderable, templatable {

    /** @var int The value attribute of the button.*/
    private $value;
    /** @var string The string identifier for the email.*/
    private $emailclass;

    /**
     * @param int $value The value attribute of the button.
     * @param string $emailclass The string identifier for the email.
     */
    public function __construct($value, $emailclass) {
        $this->value = $value;
        $this->emailclass = $emailclass;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value and emailclass.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->value = $this->value;
        $data->emailclass = $this->emailclass;
        return $data;
    }

}

/**
 * Renderable class for tables which serve as legends.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class legend_table implements renderable, templatable {

    /** @var array $rows $rows The rows of the table as arrays with keys leftclass, lefttext, rightclass, and righttext.*/
    private $rows;

    /**
     * @param array $rows The rows of the table as arrays with keys leftclass, lefttext, rightclass, and righttext.
     */
    public function __construct($rows) {
        $this->rows = $rows;
    }

    /**
     * Exports this data so it can be used as the context for a mustache template.
     *
     * @return stdClass Object with properties value and emailclass.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->rows = $this->rows;
        return $data;
    }

}
