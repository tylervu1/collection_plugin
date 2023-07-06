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
 * Prints an instance of mod_collection.
 *
 * @package     mod_collection
 * @copyright   2023 Tyler Vu <tyler.vuvan@nashtechglobal.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once('collection_form.php');
require_once('collection_functions.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$c = optional_param('c', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('collection', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('collection', array('id' => $cm->instance), '*', MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$PAGE->set_url('/mod/collection/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

if (has_capability('mod/collection:submit', $modulecontext)) {
    // This user is a student (or another role with the 'submit' capability).
    // Display the form for them to enter information.
    $userid = $USER->id;
    $currentrecord = (object) ['contactname' => '', 'contactemail' => '', 'contactphone' => ''];
    if ($DB->record_exists('collection', ['userid' => $USER->id])) {
        $currentrecord = $DB->get_record('collection', ['userid' => $USER->id]);
    }

    $mform = new collection_form(null, array('currentrecord' => $currentrecord, 'id' => $cm->id));

    if ($fromform = $mform->get_data()) {
        $record = build_record($fromform, $userid, $course);
        save_data($record, $currentrecord);
        echo 'Data saved successfully';
    } else {
        $mform->display();
    }
} else if (has_capability('mod/collection:view', $modulecontext)) {
    // This user is a teacher (or another role with the 'view' capability).
    // Display the data for them.

    // Get the records from the 'collection' table for the current course.
    $records = get_records($course);
    display_table($records);
}

echo $OUTPUT->footer();
