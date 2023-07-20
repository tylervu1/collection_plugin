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

    private $record;
    private $userid;
    private $course;

    public function setUp(): void {
        $this->record = new stdClass();
        $this->record->name = 'Test name';
        $this->record->email = 'test@email.com';
        $this->record->phone = '1234567890';

        $this->userid = 1;
        $this->course = new stdClass();
        $this->course->id = 1;
        $this->course->fullname = 'Test course';
    }

    public function test_build_record() {
        $result = build_record($this->record, $this->userid, $this->course);

        $this->assertEquals($this->record->name, $result->contactname);
        $this->assertEquals($this->record->email, $result->contactemail);
        $this->assertEquals($this->record->phone, $result->contactphone);
        $this->assertEquals($this->userid, $result->userid);
        $this->assertEquals($this->course->id, $result->course);
        $this->assertEquals($this->course->fullname, $result->name);
    }

    public function test_save_data() {
        global $DB;

        // Mock the $DB object.
        $DB = $this->createMock(get_class($DB));

        // Test when current record exists.
        $currentrecord = new stdClass();
        $currentrecord->id = 1;

        $DB->expects($this->once())
            ->method('update_record')
            ->with($this->equalTo('collection'), $this->equalTo($this->record))
            ->willReturn(true);

        $this->assertTrue(save_data($this->record, $currentrecord));

        // Test when current record does not exist.
        $currentrecord = null;

        $DB->expects($this->once())
            ->method('insert_record')
            ->with($this->equalTo('collection'), $this->equalTo($this->record))
            ->willReturn(true);

        $this->assertTrue(save_data($this->record, $currentrecord));
    }
}
