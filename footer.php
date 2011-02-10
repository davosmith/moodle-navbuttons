<?php

require_once(dirname(__FILE__).'/definitions.php');

if (isset($CFG->navbuttons_self_test)) {
    navmenu($COURSE); // Trigger the $THEME->cm setting - if it has been correctly inserted
    $theme = (array)$THEME;
    if (!array_key_exists('cm', $theme)) {
        $CFG->navbuttons_self_test = 2; // $THEME->cm not set
    } else {
        $CFG->navbuttons_self_test = 0; // All OK
    }
} else {

    echo '<!-- Navbuttons start -->';

    $display = true;
    if ($COURSE->id <= 1) {
        echo '<!-- Front page -->';
        $display = false;
    } elseif (!$settings = get_record('navbuttons', 'course', $COURSE->id)) {
        echo '<!-- No settings -->';
        $display = false;
    } elseif (!$settings->enabled) {
        echo '<!-- Not enabled -->';
        $display = false;
    } elseif (!isset($THEME->cm)) {
        echo '<!-- No cmid -->';
        $display = false;
    }

    if ($display) {
        $cmid = $THEME->cm->id;

        $modinfo = get_fast_modinfo($COURSE);
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $sections = get_records('course_sections', 'course', $COURSE->id, 'section', 'section,visible,summary');
    
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

            if ($mod->sectionnum > $COURSE->numsections) {
                break;
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
            $thismod->link = $CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id;
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
    

        echo '<div id="navbuttons" style="float: right; width: 400px; right: 0; margin-top: 5px;">';
        if ($settings->homebuttonshow) {
            $home = new stdClass;
            if ($settings->homebuttontype == BLOCK_NAVBUTTONS_HOME_COURSE) {
                $home->link = $CFG->wwwroot.'/course/view.php?id='.$COURSE->id;
                $home->name = get_string('coursepage','block_navbuttons');
            } else {
                $home->link = $CFG->wwwroot;
                $home->name = get_string('frontpage','block_navbuttons');
            }
            list($icon, $bgcolour) = navbutton_get_icon('home.png', $settings->homebuttonicon, $settings->backgroundcolour, $settings->customusebackground);
            echo make_navbutton($icon, $bgcolour, $home->name, $home->link);
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
                $first->link = $CFG->wwwroot.'/course/view.php?id='.$COURSE->id;
            }
            if ($first) {
                list($icon, $bgcolour) = navbutton_get_icon('first.png', $settings->firstbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
                echo make_navbutton($icon, $bgcolour, $first->name, $first->link);
            }
        }

        if ($settings->prevbuttonshow && $prev) {
            list($icon, $bgcolour) = navbutton_get_icon('prev.png', $settings->prevbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
            echo make_navbutton($icon, $bgcolour, get_string('prevactivity','block_navbuttons').': '.$prev->name, $prev->link);
        }
        if ($settings->nextbuttonshow && $next) {
            list($icon, $bgcolour) = navbutton_get_icon('next.png', $settings->nextbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
            echo make_navbutton($icon, $bgcolour, get_string('nextactivity','block_navbuttons').': '.$next->name, $next->link);
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
                $last->link = $CFG->wwwroot.'/course/view.php?id='.$COURSE->id;
            }
            if ($last) {
                list($icon, $bgcolour) = navbutton_get_icon('last.png', $settings->lastbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
                echo make_navbutton($icon, $bgcolour, $last->name, $last->link);
            }
        }

        if ($settings->extra1show && $settings->extra1link) {
            list($icon, $bgcolour) = navbutton_get_icon('extra1.png', $settings->extra1icon, $settings->backgroundcolour, $settings->customusebackground);
            if (!$settings->extra1title) {
                $settings->extra1title = $settings->extra1link;
            }
            echo make_navbutton($icon, $bgcolour, $settings->extra1title, $settings->extra1link, $settings->extra1openin);
        }

        if ($settings->extra2show && $settings->extra2link) {
            list($icon, $bgcolour) = navbutton_get_icon('extra2.png', $settings->extra2icon, $settings->backgroundcolour, $settings->customusebackground);
            if (!$settings->extra2title) {
                $settings->extra2title = $settings->extra2link;
            }
            echo make_navbutton($icon, $bgcolour, $settings->extra2title, $settings->extra2link, $settings->extra2openin);
        }

        echo '</div>';
        echo '<br style="clear:both;" />';
    }

    function navbutton_get_icon($default, $usericon, $bgcolour, $customusebackground) {
        global $CFG, $COURSE;

        $defaulturl = $CFG->wwwroot.'/blocks/navbuttons/pix/'.$default;
        if ($usericon == NULL || $usericon == '') {
            return array($defaulturl, $bgcolour);
        }

        $userfile = $CFG->dataroot.'/'.$COURSE->id.'/'.$usericon;
        if (!file_exists($userfile)) {
            return array($defaulturl, $bgcolour);
        }

        if (!exif_imagetype($userfile)) {
            return array($defaulturl, $bgcolour);
        }

        return array($CFG->wwwroot.'/file.php?file=/'.$COURSE->id.'/'.$usericon, $customusebackground ? $bgcolour : false);
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

    echo '<!-- End of Navbuttons -->';
}

?>
