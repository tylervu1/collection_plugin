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
 * Contains unit tests for collection_form.php.
 *
 * @package     mod_collection
 * @copyright   2023 Tyler Vu <tyler.vuvan@nashtechglobal.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
// Include the Moodle PHPUnit integration files.
require_once($CFG->dirroot . '/lib/phpunit/classes/advanced_testcase.php');
require_once(__DIR__ . '/../collection_form.php');

class collection_form_test extends advanced_testcase {
    private $form;
    private $currentrecord;

    /**
     * Setup test data.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        // Pass necessary parameters for the form.
        $this->currentrecord = new stdClass();
        $this->currentrecord->contactname = '';
        $this->currentrecord->contactemail = '';
        $this->currentrecord->contactphone = '';

        // Instantiate the form.
        $this->form = new collection_form(null, ['id' => 0, 'currentrecord' => $this->currentrecord]);
    }

    public function test_form_elements() {
        // Start output buffering.
        ob_start();

        // Display the form.
        $this->form->display();

        // Get the form HTML.
        $formhtml = ob_get_clean();

        // Check for the presence of form fields.
        $this->assertStringContainsString('name="name"', $formhtml);
        $this->assertStringContainsString('name="email"', $formhtml);
        $this->assertStringContainsString('name="phone"', $formhtml);
        $this->assertStringContainsString('name="id"', $formhtml);
    }

    public function test_form_validation() {
        // Too many digits.
        $testdata = array(
            'name' => 'John Doe',
            'email' => 'johndoe123@example.com',
            'phone' => '00123456789',
            'id' => 1,
        );

        $validationerrors = $this->form->validation($testdata, []);
        $this->assertArrayHasKey('phone', $validationerrors, 'Form should be invalid with phone number having too many digits');

        // Too few digits.
        $testdata['phone'] = '01234567';
        $validationerrors = $this->form->validation($testdata, []);
        $this->assertArrayHasKey('phone', $validationerrors, 'Form should be invalid with phone number having too few digits');

        // Not numeric.
        $testdata['phone'] = 'abcdefg';
        $validationerrors = $this->form->validation($testdata, []);
        $this->assertArrayHasKey('phone', $validationerrors, 'Form should be invalid with non-numeric phone number');

        // Correct number of digits.
        $testdata['phone'] = '0123456789';
        $validationerrors = $this->form->validation($testdata, []);
        $this->assertArrayNotHasKey('phone', $validationerrors, 'Form should be valid with correct input');

        // Invalid email.
        $testdata['email'] = 'invalid email';
        $errors = $this->form->validation($testdata, []);
        $this->assertArrayHasKey('email', $errors, 'Form should be invalid with invalid email');

        // Valid email.
        $testdata['email'] = 'valid.email@example.com';
        $errors = $this->form->validation($testdata, []);
        $this->assertArrayNotHasKey('email', $errors, 'Form should be valid with valid email');
    }
}
