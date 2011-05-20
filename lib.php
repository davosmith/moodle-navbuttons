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

function block_navbuttons_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload) {
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

    if (!$file = $fs->get_file($context->id, 'block_navbuttons', 'icons', $iconid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 60*60, 0, $forcedownload);
}
