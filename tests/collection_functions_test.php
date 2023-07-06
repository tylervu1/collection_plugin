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
 * Contains unit tests for collection_functions.php.
 *
 * @package     mod_collection
 * @copyright   2023 Tyler Vu <tyler.vuvan@nashtechglobal.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
// Include the Moodle PHPUnit integration files.
require_once($CFG->dirroot . '/lib/phpunit/classes/advanced_testcase.php');
require_once(__DIR__ . '/../collection_functions.php');

class collection_functions_test extends advanced_testcase {

    public function test_build_record() {
        $fromform = new stdClass();
        $fromform->name = 'Test name';
        $fromform->email = 'test@email.com';
        $fromform->phone = '1234567890';

        $userid = 1;
        $course = new stdClass();
        $course->id = 1;
        $course->fullname = 'Test course';

        $result = build_record($fromform, $userid, $course);

        $this->assertEquals($fromform->name, $result->contactname);
        $this->assertEquals($fromform->email, $result->contactemail);
        $this->assertEquals($fromform->phone, $result->contactphone);
        $this->assertEquals($userid, $result->userid);
        $this->assertEquals($course->id, $result->course);
        $this->assertEquals($course->fullname, $result->name);
    }

    public function test_save_data() {
        global $DB;

        // Mock the $DB object.
        $DB = $this->createMock(get_class($DB));

        $record = new stdClass();
        $record->userid = 1;
        $record->contactname = 'Test name';
        $record->contactemail = 'test@email.com';
        $record->contactphone = '1234567890';
        $record->course = 1;
        $record->name = 'Test course';
        $record->timecreated = time();

        // Test when current record exists.
        $currentrecord = new stdClass();
        $currentrecord->id = 1;

        $DB->expects($this->once())
            ->method('update_record')
            ->with($this->equalTo('collection'), $this->equalTo($record))
            ->willReturn(true);

        $this->assertTrue(save_data($record, $currentrecord));

        // Test when current record does not exist.
        $currentrecord = null;

        $DB->expects($this->once())
            ->method('insert_record')
            ->with($this->equalTo('collection'), $this->equalTo($record))
            ->willReturn(true);

        $this->assertTrue(save_data($record, $currentrecord));
    }
}
