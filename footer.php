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
    global $COURSE, $DB, $CFG, $OUTPUT, $PAGE;

    $output = '<!-- Navbuttons start -->';
    $outend = '<!-- Navbuttons end -->';

    if (isset($CFG->navbuttons_self_test)) {
        $CFG->navbuttons_self_test = 0; // All OK
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

    $cmid = $PAGE->cm->id;


    $modinfo = get_fast_modinfo($COURSE);
    $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    $sections = $DB->get_records('course_sections', array('course'=>$COURSE->id),'section','section,visible,summary');

    $next = false;
    $prev = false;
    $firstcourse = false;
    $firstsection = false;
    $lastcourse = false;
    $lastsection = false;

    $sectionnum = -1;
    $thissection = null;
    $firstthissection = false;
    $flag = false;
    $sectionflag = false;
    $previousmod = false;

    foreach ($modinfo->cms as $mod) {
        if ($mod->modname == 'label') {
            continue;
        }

        if ($CFG->version >= 2012120300) { // Moodle 2.4
            $opts = course_get_format($COURSE)->get_format_options();
            if ($opts['numsections'] && $mod->sectionnum > $opts['numsections']) {
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
                has_capability('moodle/course:viewhiddensections', $context)) {
                $sectionnum = $mod->sectionnum;
                $firstthissection = false;
                if ($sectionflag) {
                    if ($flag) { // flag means selected mod was the last in the section
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

        $thismod = new stdClass;
        $thismod->link = new moodle_url('/mod/'.$mod->modname.'/view.php', array('id'=>$mod->id));
        $thismod->name = strip_tags(format_string($mod->name,true));

        if ($flag) { // Current mod is the 'next' mod
            $next = $thismod;
            $flag = false;
        }
        if ($cmid == $mod->id) {
            $flag = true;
            $sectionflag = true;
            $prev = $previousmod;
            $firstsection = $firstthissection;
            if (!$firstcourse) {
                $firstcourse = 'none'; // Prevent the 'firstcourse' link if this is the first item
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
    if (!$flag) { // flag means selected mod is the last in the course
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

    $output .=  '<div id="navbuttons" style="float: right; width: 400px; right: 0; margin-top: 5px;">';
    if ($settings->homebuttonshow) {
        $home = new stdClass;
        if ($settings->homebuttontype == BLOCK_NAVBUTTONS_HOME_COURSE) {
            $home->link = new moodle_url('/course/view.php', array('id'=>$COURSE->id));
            $home->name = get_string('coursepage','block_navbuttons');
        } else {
            $home->link = $CFG->wwwroot;
            $home->name = get_string('frontpage','block_navbuttons');
        }
        list($icon, $bgcolour) = navbutton_get_icon('home', $context, BLOCK_NAVBUTTONS_HOMEICON, $settings->backgroundcolour, $settings->customusebackground);
        $output .= make_navbutton($icon, $bgcolour, $home->name, $home->link);
    }

    if ($settings->firstbuttonshow) {
        $first = new stdClass;
        if ($settings->firstbuttontype == BLOCK_NAVBUTTONS_FIRST_IN_COURSE) {
            if (!$firstcourse) {
                $first = false;
            } else {
                $first->name = get_string('firstcourse','block_navbuttons').': '.$firstcourse->name;
                $first->link = $firstcourse->link;
            }
        } elseif ($settings->firstbuttontype == BLOCK_NAVBUTTONS_FIRST_IN_SECTION) {
            if (!$firstsection) {
                $first = false;
            } else {
                $first->name = get_string('firstsection','block_navbuttons').': '.$firstsection->name;
                $first->link = $firstsection->link;
            }
        } else {
            $first->name = get_string('coursepage','block_navbuttons');
            $first->link = new moodle_url('/course/view.php', array('id'=>$COURSE->id));
        }
        if ($first) {
            list($icon, $bgcolour) = navbutton_get_icon('first', $context, BLOCK_NAVBUTTONS_FIRSTICON, $settings->backgroundcolour, $settings->customusebackground);
            $output .= make_navbutton($icon, $bgcolour, $first->name, $first->link);
        }
    }

    if ($settings->prevbuttonshow && $prev) {
        list($icon, $bgcolour) = navbutton_get_icon('prev', $context, BLOCK_NAVBUTTONS_PREVICON, $settings->backgroundcolour, $settings->customusebackground);
        $output .= make_navbutton($icon, $bgcolour, get_string('prevactivity','block_navbuttons').': '.$prev->name, $prev->link);
    }
    if ($settings->nextbuttonshow && $next) {
        list($icon, $bgcolour) = navbutton_get_icon('next', $context, BLOCK_NAVBUTTONS_NEXTICON, $settings->backgroundcolour, $settings->customusebackground);
        $output .= make_navbutton($icon, $bgcolour, get_string('nextactivity','block_navbuttons').': '.$next->name, $next->link);
    }

    if ($settings->lastbuttonshow) {
        $last = new stdClass;
        if ($settings->lastbuttontype == BLOCK_NAVBUTTONS_LAST_IN_COURSE) {
            if (!$lastcourse) {
                $last = false;
            } else {
                $last->name = get_string('lastcourse','block_navbuttons').': '.$lastcourse->name;
                $last->link = $lastcourse->link;
            }
        } elseif ($settings->lastbuttontype == BLOCK_NAVBUTTONS_LAST_IN_SECTION) {
            if (!$lastsection) {
                $last = false;
            } else {
                $last->name = get_string('lastsection','block_navbuttons').': '.$lastsection->name;
                $last->link = $lastsection->link;
            }
        } else {
            $last->name = get_string('coursepage','block_navbuttons');
            $last->link = new moodle_url('/course/view.php', array('id'=>$COURSE->id));
        }
        if ($last) {
            list($icon, $bgcolour) = navbutton_get_icon('last', $context, BLOCK_NAVBUTTONS_LASTICON, $settings->backgroundcolour, $settings->customusebackground);
            $output .= make_navbutton($icon, $bgcolour, $last->name, $last->link);
        }
    }

    if ($settings->extra1show && $settings->extra1link) {
        list($icon, $bgcolour) = navbutton_get_icon('extra1', $context, BLOCK_NAVBUTTONS_EXTRA1ICON, $settings->backgroundcolour, $settings->customusebackground);
        if (!$settings->extra1title) {
            $settings->extra1title = $settings->extra1link;
        }
        $output .= make_navbutton($icon, $bgcolour, $settings->extra1title, $settings->extra1link, $settings->extra1openin);
    }

    if ($settings->extra2show && $settings->extra2link) {
        list($icon, $bgcolour) = navbutton_get_icon('extra2', $context, BLOCK_NAVBUTTONS_EXTRA2ICON, $settings->backgroundcolour, $settings->customusebackground);
        if (!$settings->extra2title) {
            $settings->extra2title = $settings->extra2link;
        }
        $output .= make_navbutton($icon, $bgcolour, $settings->extra2title, $settings->extra2link, $settings->extra2openin);
    }

    $output .= '</div>';
    $output .= '<br style="clear:both;" />';
    $output .= $outend;

    return $output;
}

function navbutton_get_icon($default, $context, $iconid, $bgcolour, $customusebackground) {
    global $CFG, $COURSE, $OUTPUT;

    $defaulturl = $OUTPUT->pix_url($default.'icon', 'block_navbuttons');

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'block_navbuttons', 'icons', $iconid, '', false);

    if (empty($files)) {
        return array($defaulturl, $bgcolour);
    }

    $iconfilename = array_shift(array_values($files))->get_filename();
    $iconurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/block_navbuttons/icons/'.$iconid.'/'.$iconfilename);

    return array($iconurl, $customusebackground ? $bgcolour : false);
}

function make_navbutton($imgsrc, $bgcolour, $title, $url, $newwindow = false) {
    $url = preg_replace('/[\'"<>]/','',$url);
    $bgcolour = preg_replace('/[^a-zA-Z0-9#]/', '', $bgcolour);
    $target = $newwindow ? ' target="_blank" ' : '';
    $output = '<a href="'.$url.'" '.$target.'><img alt="'.$title.'" title="'.$title.'" src="'.$imgsrc.'" style="';
    if ($bgcolour) {
        $output .= 'background-color: '.$bgcolour.'; ';
    }
    $output .= 'margin-right: 5px;" width="50" height="50" /></a>';
    return $output;
}

