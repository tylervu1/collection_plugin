<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Display information about all the mod_collection modules in the requested course.
 *
 * @package     mod_collection
 * @copyright   2023 Tyler Vu <tyler.vuvan@nashtechglobal.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

class collection_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $currentrecord = $this->_customdata['currentrecord'];

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setDefault('name', $currentrecord->contactname);
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'));
        $mform->setDefault('email', $currentrecord->contactemail);
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');
        $mform->addRule('email', get_string('invalidemail', 'collection'), 'email', null, 'client');

        $mform->addElement('text', 'phone', get_string('phone'));
        $mform->setDefault('phone', $currentrecord->contactphone);
        $mform->setType('phone', PARAM_RAW);
        $mform->addRule('phone', get_string('required'), 'required', null, 'client');
        $mform->addRule('phone', get_string('invalidphone', 'collection'), 'regex', '/^\d{10}$/', 'client');

        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Server-side email validation.
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = get_string('invalidemail', 'collection');
        }

        // Server-side phone validation.
        if (!preg_match('/^\d{10}$/', $data['phone'])) {
            $errors['phone'] = get_string('invalidphone', 'collection');
        }

        return $errors;
    }
}
