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

require_once(dirname(__FILE__).'/definitions.php');
require_once(dirname(__FILE__).'/activityready.php');

function draw_navbuttons() {
    global $COURSE, $DB, $CFG, $PAGE;

    $output = '<!-- Navbuttons start -->';
    $outend = '<!-- Navbuttons end -->';

    if (isset($CFG->navbuttons_self_test)) {
        $CFG->navbuttons_self_test = 0; // All OK.
        return $output.'<!-- Self test -->'.$outend;
    }

    if ($COURSE->id <= 1) {
        return $output.'<!-- Front page -->'.$outend;
    }
    if (!$settings = $DB->get_record('navbuttons', array('course' => $COURSE->id))) {
        return $output.'<!-- No settings -->'.$outend;
    }
    if (!$settings->enabled) {
        return $output.'<!-- Not enabled -->'.$outend;
    }
    if (!$PAGE->cm) {
        return $output.'<!-- No course module -->'.$outend;
    }
    if (!navbuttons_activity_showbuttons($PAGE->cm)) {
        return $output.'<!-- Activity not ready for navbuttons -->'.$outend;
    }

    if ($CFG->branch >= 31) {
        if ($PAGE->cm->modname == 'assign') {
            if (optional_param('action', null, PARAM_ALPHA) == 'grader') {
                return $output.'<!-- Navbuttons do not work with activity grading layout -->'.$outend;
            }
        }
    }

    $cmid = $PAGE->cm->id;

    $modinfo = get_fast_modinfo($COURSE);
    if ($CFG->version < 2011120100) {
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    } else {
        $context = context_course::instance($COURSE->id);
    }
    $sections = $DB->get_records('course_sections', array('course' => $COURSE->id), 'section', 'section,visible,summary');

    /** @var object|null $next */
    $next = null;
    /** @var object|null $prev */
    $prev = null;
    /** @var object|null $firstcourse */
    $firstcourse = null;
    /** @var object|null $firstsection */
    $firstsection = null;
    /** @var object|null $lastcourse */
    $lastcourse = null;
    /** @var object|null $lastsection */
    $lastsection = null;

    $sectionnum = -1;
    $thissection = null;
    $firstthissection = null;
    $flag = false;
    $sectionflag = false;
    $previousmod = null;
    $simple = ($settings->buttonstype == BLOCK_NAVBUTTONS_TYPE_TEXT_SIMPLE);

    foreach ($modinfo->cms as $mod) {
        if ($mod->modname == 'label') {
            continue;
        }

        if ($CFG->version >= 2012120300) { // Moodle 2.4.
            $format = course_get_format($COURSE);
            if (method_exists($format, 'get_last_section_number')) {
                $numsections = $format->get_last_section_number();
            } else {
                $opts = course_get_format($COURSE)->get_format_options();
                $numsections = isset($opts['numsections']) ? $opts['numsections'] : 0;
            }
            if ($numsections && $mod->sectionnum > $numsections) {
                break;
            }
        } else {
            if ($mod->sectionnum > $COURSE->numsections) {
                break;
            }
        }

        if (!$mod->uservisible) {
            continue;
        }

        if ($mod->sectionnum > 0 && $sectionnum != $mod->sectionnum) {
            $thissection = $sections[$mod->sectionnum];

            if ($thissection->visible || !$COURSE->hiddensections ||
                has_capability('moodle/course:viewhiddensections', $context)
            ) {
                $sectionnum = $mod->sectionnum;
                $firstthissection = false;
                if ($sectionflag) {
                    if ($flag) { // Flag means selected mod was the last in the section.
                        $lastsection = 'none';
                    } else {
                        $lastsection = $previousmod;
                    }
                    $sectionflag = false;
                }
            } else {
                continue;
            }
        }

        $thismod = (object)[
            'link' => new moodle_url('/mod/'.$mod->modname.'/view.php', array('id' => $mod->id)),
            'name' => strip_tags(format_string($mod->name, true))
        ];

        if ($flag) { // Current mod is the 'next' mod.
            $next = $thismod;
            $flag = false;
        }
        if ($cmid == $mod->id) {
            $flag = true;
            $sectionflag = true;
            $prev = $previousmod;
            $firstsection = $firstthissection;
            if (!$firstcourse) {
                $firstcourse = 'none'; // Prevent the 'firstcourse' link if this is the first item.
            }
        }
        if (!$firstthissection) {
            $firstthissection = $thismod;
        }
        if (!$firstcourse) {
            $firstcourse = $thismod;
        }

        $previousmod = $thismod;
    }
    if (!$flag) { // Flag means selected mod is the last in the course.
        if (!$lastsection) {
            $lastsection = $previousmod;
        }
        $lastcourse = $previousmod;
    }
    if ($firstcourse == 'none') {
        $firstcourse = false;
    }
    if ($lastsection == 'none') {
        $lastsection = false;
    }

    $output .= '<div id="navbuttons">';
    if ($settings->homebuttonshow) {
        $home = new stdClass;
        if ($settings->homebuttontype == BLOCK_NAVBUTTONS_HOME_COURSE) {
            $home->link = new moodle_url('/course/view.php', array('id' => $COURSE->id));
            $home->name = get_string('coursepage', 'block_navbuttons');
        } else {
            $home->link = $CFG->wwwroot;
            $home->name = get_string('frontpage', 'block_navbuttons');
        }
        list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'home', $context, BLOCK_NAVBUTTONS_HOMEICON,
                                                    $settings->backgroundcolour, $settings->customusebackground);
        $output .= make_navbutton($icon, $bgcolour, $home->name, $home->link, "home");
    }

    if ($settings->firstbuttonshow) {
        $first = new stdClass;
        if ($settings->firstbuttontype == BLOCK_NAVBUTTONS_FIRST_IN_COURSE) {
            if (!$firstcourse) {
                $first = false;
            } else {
                if ($simple) {
                    $first->name = get_string('firstcourse_simple', 'block_navbuttons');
                } else {
                    $first->name = get_string('firstcourse', 'block_navbuttons').': '.$firstcourse->name;
                }
                $first->link = $firstcourse->link;
            }
        } else if ($settings->firstbuttontype == BLOCK_NAVBUTTONS_FIRST_IN_SECTION) {
            if (!$firstsection) {
                $first = false;
            } else {
                if ($simple) {
                    $first->name = get_string('firstsection_simple', 'block_navbuttons');
                } else {
                    $first->name = get_string('firstsection', 'block_navbuttons').': '.$firstsection->name;
                }
                $first->link = $firstsection->link;
            }
        } else {
            $first->name = get_string('coursepage', 'block_navbuttons');
            $first->link = new moodle_url('/course/view.php', array('id' => $COURSE->id));
        }
        if ($first) {
            list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'first', $context, BLOCK_NAVBUTTONS_FIRSTICON,
                                                        $settings->backgroundcolour, $settings->customusebackground);
            $output .= make_navbutton($icon, $bgcolour, $first->name, $first->link, "first");
        }
    }

    if ($settings->prevbuttonshow && $prev) {
        list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'prev', $context, BLOCK_NAVBUTTONS_PREVICON,
                                                    $settings->backgroundcolour, $settings->customusebackground);
        if ($simple) {
            $str = get_string('prevactivity_simple', 'block_navbuttons');
        } else {
            $str = get_string('prevactivity', 'block_navbuttons').': '.$prev->name;
        }
        $output .= make_navbutton($icon, $bgcolour, $str, $prev->link, "prev");
    }

    if ($settings->nextbuttonshow && $next) {
        list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'next', $context, BLOCK_NAVBUTTONS_NEXTICON,
                                                    $settings->backgroundcolour, $settings->customusebackground);
        if ($simple) {
            $str = get_string('nextactivity_simple', 'block_navbuttons');
        } else {
            $str = get_string('nextactivity', 'block_navbuttons').': '.$next->name;
        }
        $output .= make_navbutton($icon, $bgcolour, $str, $next->link, "next");
    }

    if ($settings->lastbuttonshow) {
        $last = new stdClass;
        if ($settings->lastbuttontype == BLOCK_NAVBUTTONS_LAST_IN_COURSE) {
            if (!$lastcourse) {
                $last = false;
            } else {
                if ($simple) {
                    $last->name = get_string('lastcourse_simple', 'block_navbuttons');
                } else {
                    $last->name = get_string('lastcourse', 'block_navbuttons').': '.$lastcourse->name;
                }
                $last->link = $lastcourse->link;
            }
        } else if ($settings->lastbuttontype == BLOCK_NAVBUTTONS_LAST_IN_SECTION) {
            if (!$lastsection) {
                $last = false;
            } else {
                if ($simple) {
                    $last->name = get_string('lastsection_simple', 'block_navbuttons');
                } else {
                    $last->name = get_string('lastsection', 'block_navbuttons').': '.$lastsection->name;
                }
                $last->link = $lastsection->link;
            }
        } else {
            $last->name = get_string('coursepage', 'block_navbuttons');
            $last->link = new moodle_url('/course/view.php', array('id' => $COURSE->id));
        }
        if ($last) {
            list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'last', $context, BLOCK_NAVBUTTONS_LASTICON,
                                                        $settings->backgroundcolour, $settings->customusebackground);
            $output .= make_navbutton($icon, $bgcolour, $last->name, $last->link, "last");
        }
    }

    if ($settings->extra1show && $settings->extra1link) {
        list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'extra1', $context, BLOCK_NAVBUTTONS_EXTRA1ICON,
                                                    $settings->backgroundcolour, $settings->customusebackground);
        if (!$settings->extra1title) {
            $settings->extra1title = $settings->extra1link;
        }
        $output .= make_navbutton($icon, $bgcolour, $settings->extra1title, $settings->extra1link,
                                  "extra1", $settings->extra1openin);
    }

    if ($settings->extra2show && $settings->extra2link) {
        list($icon, $bgcolour) = navbutton_get_icon($settings->buttonstype, 'extra2', $context, BLOCK_NAVBUTTONS_EXTRA2ICON,
                                                    $settings->backgroundcolour, $settings->customusebackground);
        if (!$settings->extra2title) {
            $settings->extra2title = $settings->extra2link;
        }
        $output .= make_navbutton($icon, $bgcolour, $settings->extra2title, $settings->extra2link,
                                  "extra2", $settings->extra2openin);
    }

    $output .= make_activitycomplete_button($settings);

    $output .= '</div>';
    $output .= '<br style="clear:both;" />';
    $output .= $outend;

    return $output;
}

function navbutton_get_icon($buttonstype, $default, $context, $iconid, $bgcolour, $customusebackground) {
    global $OUTPUT, $CFG;

    if ($buttonstype == BLOCK_NAVBUTTONS_TYPE_ICON) {
        if ($CFG->branch < 33) {
            $defaulturl = $OUTPUT->pix_url($default.'icon', 'block_navbuttons');
        } else {
            $defaulturl = $OUTPUT->image_url($default.'icon', 'block_navbuttons');
        }

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'block_navbuttons', 'icons', $iconid, '', false);

        if (empty($files)) {
            return array($defaulturl, $bgcolour);
        }

        $file = reset($files);
        $iconfilename = $file->get_filename();
        $iconurl = moodle_url::make_pluginfile_url($context->id, 'block_navbuttons', 'icons', $iconid, '/', $iconfilename);

        return array($iconurl, $customusebackground ? $bgcolour : false);
    } else {
        return array(null, false); // Do not use an icon.
    }
}

function make_navbutton($imgsrc, $bgcolour, $title, $url, $classes = null, $newwindow = false) {
    $url = preg_replace('/[\'"<>]/', '', $url);
    $bgcolour = preg_replace('/[^a-zA-Z0-9#]/', '', $bgcolour);
    $target = $newwindow ? ' target="_blank" ' : '';
    if ($imgsrc !== null) {
        // Generate an icon button.
        $class = $classes ? ' class="'.$classes.'" ' : '';
        $output = '<a href="'.$url.'" '.$target.$class.'><img alt="'.$title.'" title="'.$title.'" src="'.$imgsrc.'" style="';
        if ($bgcolour) {
            $output .= 'background-color: '.$bgcolour.'; ';
        }
        $output .= 'margin-right: 5px;" width="50" height="50" /></a>';
    } else {
        $formclass = 'navbuttontext';
        if ($classes) {
            $formclass .= " ".$classes;
        }
        // Generate a text button.
        $output = html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'navbutton', 'value' => $title));
        $params = explode('?', $url, 2);
        if (count($params) > 1) {
            $params = str_replace('&amp;', '&', $params[1]);
            $params = explode('&', $params);
            foreach ($params as $param) {
                $parts = explode('=', $param, 2);
                if (!isset($parts[1])) {
                    $parts[1] = null;
                }
                $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $parts[0], 'value' => $parts[1]));
            }
        }

        $output = html_writer::tag('form', $output, array('action' => $url, 'method' => 'get', 'class' => $formclass));
    }
    return $output;
}

/**
 * Create Activity completion buttons
 *
 * @param $settings
 * @return string
 */
function make_activitycomplete_button($settings) {

    global $COURSE, $CFG, $PAGE, $OUTPUT;

    $output = "";
    $style = "";

    if (!$settings->completebuttonshow) {
        return $output;
    }

    $completioninfo = new completion_info($COURSE);
    $completion = $completioninfo->is_enabled($PAGE->cm);
    $completiondata = $completioninfo->get_data($PAGE->cm, true);

    $incompletebtntext = get_string('incompletebuttontext', 'block_navbuttons');
    $completebtntext = get_string('completebuttontext', 'block_navbuttons');

    if ($CFG->branch < 33) {
        $incompletebtnicon = $OUTPUT->pix_url('crossicon', 'block_navbuttons');
        $completebtnicon = $OUTPUT->pix_url('tickicon', 'block_navbuttons');
    } else {
        $incompletebtnicon = $OUTPUT->image_url('crossicon', 'block_navbuttons');
        $completebtnicon = $OUTPUT->image_url('tickicon', 'block_navbuttons');
    }

    $newstate = ($completiondata->completionstate == COMPLETION_COMPLETE) ? COMPLETION_INCOMPLETE : COMPLETION_COMPLETE;

    $completionbtntext = ($completiondata->completionstate == COMPLETION_COMPLETE)? $incompletebtntext : $completebtntext;

    if ($settings->buttonstype == BLOCK_NAVBUTTONS_TYPE_ICON) {
        $style = 'background-color: '.$settings->backgroundcolour.'; margin-right: 5px';
        $completionicon = $completiondata->completionstate == COMPLETION_COMPLETE ? $incompletebtnicon : $completebtnicon;
    }

    if ($completioninfo->is_enabled()) {

        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings('block_navbuttons', 'en');
        $PAGE->requires->strings_for_js(array_keys($strings), 'block_navbuttons');

        $jsmodule = array(
            'name' => 'block_navbuttons',
            'fullpath' => new moodle_url('/blocks/navbuttons/completion.js')
        );

        $PAGE->requires->js_init_call('M.block_navbuttons.completion.init', null, false, $jsmodule);

    }

    if ($completion == COMPLETION_TRACKING_MANUAL) {

        // If this completion state is used by the
        // conditional activities system, we need to turn
        // off the JS.
        $extraclass = '';
        if (!empty($CFG->enableavailability) &&
            core_availability\info::completion_value_used($COURSE, $PAGE->cm->id)) {
            $extraclass = ' preventjs';
        }
        $output .= html_writer::start_tag('form', array('method' => 'post',
            'action' => new moodle_url('/course/togglecompletion.php'),
            'class' => 'togglecompletion navbuttontext'. $extraclass));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden', 'name' => 'id', 'value' => $PAGE->cm->id));
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden', 'name' => 'modulename', 'value' => $PAGE->cm->name));
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden', 'name' => 'completionstate', 'value' => $newstate));
        $output .= html_writer::empty_tag('input', array(
            'type' => 'hidden', 'name' => 'btntype', 'value' => $settings->buttonstype));
        if ($settings->buttonstype == BLOCK_NAVBUTTONS_TYPE_ICON) {
            $output .= html_writer::empty_tag('input', array(
                'type' => 'image',
                'src'  => $completionicon,
                'alt'  => $completionbtntext,
                'class' => 'custom_activity_completion',
                'title' => $completionbtntext,
                'style' => $style,
                'aria-live' => 'polite'));
        } else {
            $output .= html_writer::empty_tag('input', array(
                'type' => 'submit',
                'value' => $completionbtntext,
                'class' => 'custom_activity_completion',
                'style' => $style,
                'aria-live' => 'polite'));
        }
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
    }

    return $output;
}