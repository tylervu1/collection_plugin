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

function save_data($record, $currentrecord) {
    global $DB;
    if (isset($currentrecord->id)) {
        // If the record already exists.
        $record->id = $currentrecord->id;
        if ($DB->update_record('collection', $record)) {
            return true;
        } else {
            return false;
        }
    } else {
        // Otherwise create new record.
        if ($DB->insert_record('collection', $record)) {
            return true;
        } else {
            return false;
        }
    }
}

function get_records($course) {
    global $DB;
    return $DB->get_records('collection', array('course' => $course->id));
}

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
