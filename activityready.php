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

defined('MOODLE_INTERNAL') || die();

define('NAVBUTTONS_ACTIVITY_ALWAYS', 0);
define('NAVBUTTONS_ACTIVITY_COMPLETE', 1);
define('NAVBUTTONS_ACTIVITY_CUSTOM', 2);
define('NAVBUTTONS_ACTIVITY_NEVER', 3);

/**
 * Check if an activity is configured to only show navbuttons when
 * complete and then check if the activity is complete
 * @param cm_info $cm the course module for the activity
 * @param object $course the course the activity is part of
 * @return boolean true if the navbuttons should be shown
 */
function navbuttons_activity_showbuttons($cm) {
    $modname = $cm->modname;

    $show = get_config('block_navbuttons', 'activity'.$modname);
    if ($show === false || $show == NAVBUTTONS_ACTIVITY_ALWAYS) {
        return true; // No config or 'always show'
    }

    if ($show == NAVBUTTONS_ACTIVITY_NEVER) {
        return false;
    }

    if ($show == NAVBUTTONS_ACTIVITY_COMPLETE) {
        $completion = new completion_info($cm->get_course());
        if (!$completion->is_enabled($cm)) {
            return true; // No completion tracking - show the buttons
        }
        $cmcompletion  = $completion->get_data($cm);
        if ($cmcompletion->completionstate == COMPLETION_INCOMPLETE) {
            return false;
        }
        return true;
    }

    if (!isloggedin() || isguestuser()) {
        return true; // Always show the buttons if not logged in
    }

    // NAVBUTTONS_ACTIVITY_CUSTOM
    $funcname = 'navbuttons_mod_'.$modname.'_showbuttons';
    if (!function_exists($funcname)) {
        return true; // Shouldn't have got to here, but allow the buttons anyway
    }
    return $funcname($cm);
}

function navbuttons_mod_assignment_showbuttons($cm) {
    global $DB, $CFG, $USER;

    if (!$assignment = $DB->get_record('assignment', array('id' => $cm->instance))) {
        return true; // Not quite sure what went wrong
    }
    $type = $assignment->assignmenttype;
    if ($type == 'offline') {
        return true; // Cannot track 'offline' assignments
    }

    require_once($CFG->dirroot.'/mod/assignment/type/'.$type.'/assignment.class.php');
    $class = 'assignment_'.$type;
    $instance = new $class($cm->id, $assignment, $cm, $cm->get_course());
    if (!$submission = $instance->get_submission($USER->id)) {
        return false; // No submission
    }
    if ($type == 'upload' || $type == 'uploadpdf') {
        if ($instance->drafts_tracked()) {
            if ($instance->is_finalized($submission)) {
                return true; // Upload submission is 'finalised'
            } else {
                return false;
            }
        }
    }
    if ($submission->timemodified > 0) {
        return true; // Submission has a 'modified' time
    }

    return false;
}

function navbuttons_mod_choice_showbuttons($cm) {
    global $USER, $DB;
    return $DB->record_exists('choice_answers', array('choiceid' => $cm->instance,
                                                      'userid' => $USER->id));
}

function navbuttons_mod_quiz_showbuttons($cm) {
    global $USER, $CFG;

    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    if (quiz_get_user_attempt_unfinished($cm->instance, $USER->id)) {
        return false; // Unfinished attempt in progress
    }
    if (!quiz_get_user_attempts($cm->instance, $USER->id, 'finished', true)) {
        return false; // No finished attempts
    }
    return true;
}

function navbuttons_mod_questionnaire_showbuttons($cm) {
    global $USER, $DB;
    return $DB->record_exists('questionnaire_attempts', array('qid' => $cm->instance,
                                                              'userid' => $USER->id));
}