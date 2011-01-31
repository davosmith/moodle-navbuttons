<?php

$display = true;
if (!isset($THEME->menu) || $THEME->menu == '') {
    $display = false;
} elseif ($COURSE->id <= 1) {
    $display = false;
} elseif (!$settings = get_record('navbuttons', 'course', $COURSE->id)) {
    $display = false;
} elseif (!$settings->enabled) {
    $display = false;
}
        
if ($display) {
    $dom = new domDocument;
    $dom->loadHTML($THEME->menu);
    $dom->preserveWhiteSpace = false;
    $options = $dom->getElementsByTagName('option');

    $next = false;
    $prev = false;
    $first = false;
    $last = false;

    if ($options->length > 0) {
        $lastactivity = $options->item($options->length - 1);
        $last = new stdClass;
        $last->link = $lastactivity->getAttribute('value');
        $last->name = $lastactivity->nodeValue;
    }

    foreach ($options as $pos => $option) {
        if ($option->hasAttribute('selected')) {
            if ($pos > 0) {
                $prevactivity = $options->item($pos - 1);
                $prev = new stdClass;
                $prev->link = $prevactivity->getAttribute('value');
                $prev->name = $prevactivity->nodeValue;
            }

            if ($pos < $options->length-1) {
                $nextactivity = $options->item($pos + 1);
                $next = new stdClass;
                $next->link = $nextactivity->getAttribute('value');
                $next->name = $nextactivity->nodeValue;
            } else if ($pos == $options->length-1) {
                $lastlink = $lastname = false;
            }
        }
    }

    echo '<div id="navbuttons" style="float: right; width: 400px; right: 0;">';
    if ($settings->homebuttonshow) {
        $homelink = $CFG->wwwroot;
        list($icon, $bgcolour) = navbutton_get_icon('home.png', $settings->homebuttonicon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, get_string('frontpage','block_navbuttons'), $homelink);
    }

    if ($settings->firstbuttonshow) {
        $firstlink = $CFG->wwwroot.'/course/view.php?id='.$COURSE->id;
        list($icon, $bgcolour) = navbutton_get_icon('first.png', $settings->firstbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, get_string('coursepage','block_navbuttons'), $firstlink);
    }

    if ($settings->prevbuttonshow && $prev) {
        list($icon, $bgcolour) = navbutton_get_icon('prev.png', $settings->prevbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, get_string('prevactivity','block_navbuttons').': '.$prev->name, $prev->link);
    }
    if ($settings->nextbuttonshow && $next) {
        list($icon, $bgcolour) = navbutton_get_icon('next.png', $settings->nextbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, get_string('nextactivity','block_navbuttons').': '.$next->name, $next->link);
    }

    if ($settings->lastbuttonshow && $last) {
        list($icon, $bgcolour) = navbutton_get_icon('last.png', $settings->lastbuttonicon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, get_string('lastactivity','block_navbuttons').': '.$last->name, $last->link);
    }

    if ($settings->extra1show && $settings->extra1link) {
        list($icon, $bgcolour) = navbutton_get_icon('extra1.png', $settings->extra1icon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, $settings->extra1link, $settings->extra1link);
    }

    if ($settings->extra2show && $settings->extra2link) {
        list($icon, $bgcolour) = navbutton_get_icon('extra2.png', $settings->extra2icon, $settings->backgroundcolour, $settings->customusebackground);
        echo make_navbutton($icon, $bgcolour, $settings->extra2link, $settings->extra2link);
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

function make_navbutton($imgsrc, $bgcolour, $title, $url) {
    $bgcolour = preg_replace('/[^a-zA-Z0-9#]/', '', $bgcolour);
    $output = '<a href="'.$url.'"><img alt="'.$title.'" title="'.$title.'" src="'.$imgsrc.'" style="';
    if ($bgcolour) {
        $output .= 'background-color: '.$bgcolour.'; ';
    }
    $output .= 'margin-right: 5px;" width="50" height="50" /></a>';
    return $output;
}

?>
