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
 *
 *
 * @package     mod_collection
 * @copyright   2023 Tyler Vu <tyler.vuvan@nashtechglobal.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Build a standard class object (record) from the form data, user id and course details.
 *
 * @param object $fromform The data from the form. Expected to have properties: name, email, and phone.
 * @param int $userid The id of the user.
 * @param object $course The course object. Expected to have properties: id and fullname.
 *
 * @return stdClass The created record, having properties: userid, contactname, contactemail, contactphone,
 * course, name, and timecreated.
 */
function build_record($fromform, $userid, $course) {
    $record = new stdClass();
    $record->userid = $userid;
    $record->contactname = $fromform->name;
    $record->contactemail = $fromform->email;
    $record->contactphone = $fromform->phone;

    $record->course = $course->id;
    $record->name = $course->fullname;
    $record->timecreated = time();

    return $record;
}

/**
 * Saves a record into the 'collection' database table. If the record already exists (determined by
 * checking the id in $currentrecord), it updates the record. Otherwise, it inserts a new record.
 *
 * @param stdClass $record The record to be saved, typically created by build_record().
 * @param stdClass $currentrecord The current record, typically retrieved from a form.
 *
 * @return bool|int On success, return true for update operations or the new record ID for insert operations.
 *                  On failure, return false.
 */
function save_data($record, $currentrecord) {
    global $DB;
    if (isset($currentrecord->id)) {
        // If the record already exists.
        $record->id = $currentrecord->id;
        return $DB->update_record('collection', $record);
    } else {
        // Otherwise create new record.
        return $DB->insert_record('collection', $record);
    }
}

/**
 * Retrieves records from the 'collection' database table for a given course.
 *
 * @param stdClass $course The course object, which is expected to have an 'id' property.
 *
 * @return array Returns an array of stdClass objects representing database records.
 */
function get_records($course) {
    global $DB;
    return $DB->get_records('collection', array('course' => $course->id));
}

/**
 * Displays a table of records on the page. The table includes columns for name, email, and phone.
 * If a record has all three fields empty, it's skipped and not displayed in the table.
 *
 * @param array $records An array of stdClass objects representing records to be displayed.
 *
 * @return void
 */
function display_table($records) {
    // Open table.
    echo '<table class="table table-responsive table-striped">';
    echo '<thead>';
    echo '<tr><th>Name</th><th>Email</th><th>Phone</th></tr>';
    echo '</thead>';
    echo '<tbody>';

    // Display the records.
    foreach ($records as $record) {
        // Corner case: check if name, email, or phone is missing.
        if (empty($record->contactname) && empty($record->contactemail) && empty($record->contactphone)) {
            // Skip this record if all the fields are empty.
            continue;
        }
        echo '<tr>';
        echo '<td>' . s($record->contactname) . '</td>';
        echo '<td>' . s($record->contactemail) . '</td>';
        echo '<td>' . s($record->contactphone) . '</td>';
        echo '</tr>';
    }

    // Close table.
    echo '</tbody>';
    echo '</table>';
}
