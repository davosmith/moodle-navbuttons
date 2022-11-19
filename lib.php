<?php
// This file is part of the Navigation buttons plugin for Moodle - http://moodle.org/
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
 * Moodle API functions
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package block_navbuttons
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Serve requested files
 * @param object $course
 * @param object $birecordorcm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @throws coding_exception
 */
function block_navbuttons_pluginfile($course, $birecordorcm, $context, $filearea, $args, $forcedownload) {
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    require_course_login($course);

    if ($filearea !== 'icons') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $filename = $args[1];
    $iconid = $args[0];

    if (!($file = $fs->get_file($context->id, 'block_navbuttons', 'icons', $iconid, '/', $filename)) || $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 60 * 60, 0, $forcedownload);
}

/**
 * Output the navigation buttons before the page footer.
 * @return string
 */
function block_navbuttons_before_footer() {
    global $CFG;
    require_once($CFG->dirroot.'/blocks/navbuttons/footer.php');
    return draw_navbuttons();
}
