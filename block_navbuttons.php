<?php

class block_navbuttons extends block_base {
    function init() {
        $this->title = get_string('navbuttons','block_navbuttons');
        $this->version = 2011012900;
    }

    function get_content() {
        global $CFG, $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (!has_capability('moodle/course:manageactivities',$context)) {
            return NULL;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        $editlink = $CFG->wwwroot.'/blocks/navbuttons/edit.php?course='.$COURSE->id;
        $this->content->text = '<a href="'.$editlink.'">'.get_string('editsettings', 'block_navbuttons').'</a>';

        return $this->content;
    }

    function instance_create() {
        global $COURSE;
        
        // Enable the buttons when the block is added to a course
        if (!$settings = get_record('navbuttons', 'course', $COURSE->id)) {
            $settings = new stdClass;
            $settings->course = $COURSE->id;
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
        // Disable the buttons when the block is removed from a course (but leave the record, in case it is enabled later)
        if ($settings = get_record('navbuttons', 'course', $COURSE->id)) {
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

?>
