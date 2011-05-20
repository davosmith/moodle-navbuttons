<?php

// This file is part of the Navigation buttons block for Moodle - http://moodle.org/
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

class block_navbuttons extends block_base {
    function init() {
        $this->title = get_string('navbuttons','block_navbuttons');
        $this->version = 2011012900;
    }

    function get_content() {
        global $CFG;

        $courseid = $this->instance->pageid;
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        if (!has_capability('moodle/course:manageactivities',$context)) {
            return NULL;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        $editlink = $CFG->wwwroot.'/blocks/navbuttons/edit.php?course='.$courseid;
        $this->content->text = '<a href="'.$editlink.'">'.get_string('editsettings', 'block_navbuttons').'</a>';

        if (!$settings = get_record('navbuttons', 'course', $courseid)) {
            $settings = new stdClass;
            $settings->course = $courseid;
            $settings->enabled = 1;
            // All other records as database defaults
            insert_record('navbuttons', $settings);
        }

        return $this->content;
    }

    function instance_create() {
        $courseid = $this->instance->pageid;

        // Enable the buttons when the block is added to a course
        if (!$settings = get_record('navbuttons', 'course', $courseid)) {
            $settings = new stdClass;
            $settings->course = $courseid;
            $settings->enabled = 1;
            // All other records as database defaults
            insert_record('navbuttons', $settings);
        } else {
            if (!$settings->enabled) {
                $updsettings = new stdClass;
                $updsettings->id = $settings->id;
                $updsettings->enabled = 1;
                update_record('navbuttons', $updsettings);
            }
        }

        return true;
    }

    function instance_delete() {
        $courseid = $this->instance->pageid;

        // Disable the buttons when the block is removed from a course (but leave the record, in case it is enabled later)
        if ($settings = get_record('navbuttons', 'course', $courseid)) {
            if ($settings->enabled) {
                $updsettings = new stdClass;
                $updsettins->id = $settings->id;
                $updsettings->enabled = 0;
                update_record('navbuttons', $updsettings);
            }
        }
        return true;
    }
}
