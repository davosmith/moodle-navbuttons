<?php

require_once(dirname(__FILE__).'/definitions.php');

echo '<!-- Navbuttons start -->';

$display = true;
if (!isset($THEME->menu) || $THEME->menu == '') {
    echo '<!-- No menu -->';
    $display = false;
} elseif ($COURSE->id <= 1) {
    echo '<!-- Front page -->';
    $display = false;
} elseif (!$settings = get_record('navbuttons', 'course', $COURSE->id)) {
    echo '<!-- No settings -->';
    $display = false;
} elseif (!$settings->enabled) {
    echo '<!-- Not enabled -->';
    $display = false;
}

if ($display) {
    $dom = new domDocument;
    $dom->loadHTML($THEME->menu);
    $dom->preserveWhiteSpace = false;

    $menu = $dom->getElementById('navmenupopup');
    if ($menu) {
        $options = $menu->getElementsByTagName('option');

        $next = false;
        $prev = false;
        $firstcourse = false;
        $firstsection = false;
        $lastcourse = false;
        $lastsection = false;

        if ($options->length > 0) {
            $firstactivity = $options->item(0);
            $firstcourse = new stdClass;
            $firstcourse->link = $firstactivity->getAttribute('value');
            $firstcourse->name = $firstactivity->nodeValue;

            $lastactivity = $options->item($options->length - 1);
            $lastcourse = new stdClass;
            $lastcourse->link = $lastactivity->getAttribute('value');
            $lastcourse->name = $lastactivity->nodeValue;
        }

        // Find the next/previous activty & first/last in course
        foreach ($options as $pos => $option) {
            if ($option->hasAttribute('selected')) {
                if ($pos > 0) {
                    $prevactivity = $options->item($pos - 1);
                    $prev = new stdClass;
                    $prev->link = $prevactivity->getAttribute('value');
                    $prev->name = $prevactivity->nodeValue;
                } else {
                    $firstcourse = false;
                }

                if ($pos < $options->length-1) {
                    $nextactivity = $options->item($pos + 1);
                    $next = new stdClass;
                    $next->link = $nextactivity->getAttribute('value');
                    $next->name = $nextactivity->nodeValue;
                } else if ($pos == $options->length-1) {
                    $lastcourse = false;
                }
            }
        }

        // Find first / last activity in section
        $optgroups = $menu->getElementsByTagName('optgroup');
        foreach ($optgroups as $optgroup) {
            $options = $optgroup->getElementsByTagName('option');
            foreach ($options as $pos => $option) {
                if ($option->hasAttribute('selected')) {
                    if ($pos > 0) {
                        $firstactivity = $options->item(0);
                        $firstsection = new stdClass;
                        $firstsection->link = $firstactivity->getAttribute('value');
                        $firstsection->name = $firstactivity->nodeValue;
                    }

                    if ($pos < $options->length -1) {
                        $lastactivity = $options->item($options->length - 1);
                        $lastsection = new stdClass;
                        $lastsection->link = $lastactivity->getAttribute('value');
                        $lastsection->name = $lastactivity->nodeValue;
                    }

                    break 2;
                }
            }
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
    } else {
        echo '<!-- Wrong menu type -->';
    }
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

?>
