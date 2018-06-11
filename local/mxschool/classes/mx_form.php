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
 * Generic moodleform with desired defaults to be used for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

abstract class local_mxschool_form extends moodleform {

    const ELEMENT_HIDDEN_INT = array('element' => 'hidden', 'name' => null, 'type' => PARAM_INT);
    const ELEMENT_TEXT = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 20)
    );
    const ELEMENT_TEXT_REQUIRED = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 20), 'rules' => array('required')
    );
    const ELEMENT_YES_NO = array(
        'element' => 'radio', 'options' => array('Yes', 'No')
    );
    const ELEMENT_YES_NO_REQUIRED = array(
        'element' => 'radio', 'options' => array('Yes', 'No'), 'rules' => array('required')
    );
    const ELEMENT_EMAIL_REQUIRED = array(
        'element' => 'text', 'type' => PARAM_TEXT, 'attributes' => array('size' => 40), 'rules' => array('email', 'required')
    );
    const ELEMENT_TEXT_AREA = array(
        'element' => 'textarea', 'type' => PARAM_TEXT, 'attributes' => array('rows' => 3, 'cols' => 40)
    );
    const ELEMENT_TEXT_AREA_REQUIRED = array(
        'element' => 'textarea', 'type' => PARAM_TEXT, 'attributes' => array('rows' => 3, 'cols' => 40),
        'rules' => array('required')
    );

    /**
     * Sets all the fields for the form.
     *
     * @param array $fields Array of fields as category => [name => [properties]].
     * @param string $stringprefix A prefix for any necessary language strings.
     * @param bool $actiontop Whether the submit and cancel buttons should appear at the top of the form as well as at the bottom.
     */
    protected function set_fields($fields, $stringprefix, $actiontop = true) {
        if ($actiontop) {
            $this->add_action_buttons();
        }
        $mform = $this->_form;
        $mform->addElement('hidden', 'redirect', null);
        $mform->setType('redirect', PARAM_TEXT);
        foreach ($fields as $category => $categoryfields) {
            if ($category) {
                $category = "_{$category}";
                $mform->addElement('header', $category, get_string("{$stringprefix}_header{$category}", 'local_mxschool'));
            }
            foreach ($categoryfields as $name => $properties) {
                $mform->addElement($this->create_element($name, $properties, $stringprefix.$category));
                if (isset($properties['type'])) {
                    $mform->setType($name, $properties['type']);
                }
                if (isset($properties['rules'])) {
                    if (in_array('required', $properties['rules'])) {
                        $mform->addRule($name, null, 'required', null, 'client');
                    }
                    if (in_array('email', $properties['rules'])) {
                        $mform->addRule($name, null, 'email');
                    }
                }
            }
        }
        $this->add_action_buttons();
    }

    /**
     * Creates and returns an element for the form. Has different behavior for different elements.
     * Can be used recursively for grouped elements which will appear on the same line.
     *
     * @param string $name The name of the element (what appears in the html).
     * @param array $properties Variable properties depeding upon element type.
     *        Must include an 'element' key and may optionsally include 'name', 'nameparam', 'options', 'text', and 'children' keys.
     * @param string $stringprefix A prefix for the language string.
     * @return HTML_QuickForm_element The newly created element.
     */
    private function create_element($name, $properties, $stringprefix) {
        $mform = $this->_form;
        $tag = isset($properties['name']) ? $properties['name'] : $name;
        if ($tag) {
            $param = isset($properties['nameparam']) ? $properties['nameparam'] : null;
            $displayname = get_string("{$stringprefix}_{$tag}", 'local_mxschool', $param);
        }
        $attributes = isset($properties['attributes']) ? $properties['attributes'] : array();

        $result = null;
        switch($properties['element']) {
            case 'hidden':
                $result = $mform->createElement($properties['element'], $name, null);
                break;
            case 'text':
            case 'textarea':
                $result = $mform->createElement($properties['element'], $name, $displayname, $attributes);
                break;
            case 'static':
                $result = $mform->createElement($properties['element'], $name, $displayname, $properties['text']);
                break;
            case 'date_selector':
            case 'date_time_selector':
            case 'select':
                $result = $mform->createElement($properties['element'], $name, $displayname, $properties['options'], $attributes);
                break;
            case 'radio':
                $buttons = array();
                foreach ($properties['options'] as $option) {
                    $buttons[] = $mform->createElement($properties['element'], $name, '', $option, $option, $attributes);
                }
                $result = $mform->createElement('group', $name, $displayname, $buttons, '&emsp;', false);
                break;
            case 'group':
                $childelements = array();
                foreach ($properties['children'] as $childname => $childproperties) {
                    $childelements[] = $this->create_element("{$name}_{$childname}", $childproperties, $stringprefix);
                }
                $result = $mform->createElement('group', $name, $displayname, $childelements, '&emsp;', false);
                break;
            default:
                debugging("unsupported element type: {$properties['element']}");
        }
        return $result;
    }

    /**
     * Sets the redirect url so long as the form has been neither cancelled nor submitted.
     * Uses the server's HTTP_REFERER if it is set, otherwise uses the fallback provided.
     *
     * @param moodle_url $fallback The url to use if no referer is set.
     */
    public function set_redirect($fallback) {
        if (!$this->is_submitted()) {
            $this->_form->setDefault('redirect', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $fallback->out());
        }
    }

    /**
     * Retrieves the redirect url to be used after the form is submitted or cancelled.
     *
     * @return string url to redirect to.
     */
    public function get_redirect() {
        return $this->_form->exportValue('redirect');
    }

}
