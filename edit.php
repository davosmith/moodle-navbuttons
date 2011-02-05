<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__).'/definitions.php');

$courseid = required_param('course', PARAM_INT);
$course = get_record('course', 'id', $courseid);
if (!$course) {
    error('Invalid courseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/course:manageactivities',$context)) {
    error('You do not have permission to edit button settings on this course');
}

class block_navbuttons_edit_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $showhide = array(1 => get_string('show'), 0 => get_string('hide'));

        $mform->addElement('header', 'general', get_string('generalsettings', 'block_navbuttons'));
        $mform->addElement('selectyesno', 'enabled', get_string('buttonsenabled', 'block_navbuttons'));
        $mform->addElement('text', 'backgroundcolour', get_string('backgroundcolour', 'block_navbuttons'));
        $mform->addElement('selectyesno', 'customusebackground', get_string('customusebackground', 'block_navbuttons'));
        
        $hometypes = array(BLOCK_NAVBUTTONS_HOME_FRONTPAGE => get_string('frontpage','block_navbuttons'), 
                           BLOCK_NAVBUTTONS_HOME_COURSE => get_string('coursepage', 'block_navbuttons'));
        $mform->addElement('header', 'homebutton', get_string('homebutton', 'block_navbuttons'));
        $mform->addElement('select', 'homebuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'homebuttonicon', get_string('buttonicon', 'block_navbuttons'));
        $mform->addElement('select', 'homebuttontype', get_string('buttontype', 'block_navbuttons'), $hometypes);

        $firsttypes = array(BLOCK_NAVBUTTONS_FIRST_COURSE => get_string('coursepage', 'block_navbuttons'), 
                            BLOCK_NAVBUTTONS_FIRST_IN_COURSE => get_string('firstcourse','block_navbuttons'), 
                            BLOCK_NAVBUTTONS_FIRST_IN_SECTION => get_string('firstsection', 'block_navbuttons'));
        $mform->addElement('header', 'firstbutton', get_string('firstbutton', 'block_navbuttons'));
        $mform->addElement('select', 'firstbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'firstbuttonicon', get_string('buttonicon', 'block_navbuttons'));
        $mform->addElement('select', 'firstbuttontype', get_string('buttontype', 'block_navbuttons'), $firsttypes);

        $mform->addElement('header', 'prevbutton', get_string('prevbutton', 'block_navbuttons'));
        $mform->addElement('select', 'prevbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'prevbuttonicon', get_string('buttonicon', 'block_navbuttons'));

        $mform->addElement('header', 'nextbutton', get_string('nextbutton', 'block_navbuttons'));
        $mform->addElement('select', 'nextbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'nextbuttonicon', get_string('buttonicon', 'block_navbuttons'));

        $lasttypes = array(BLOCK_NAVBUTTONS_LAST_COURSE => get_string('coursepage', 'block_navbuttons'), 
                           BLOCK_NAVBUTTONS_LAST_IN_COURSE => get_string('lastcourse','block_navbuttons'), 
                           BLOCK_NAVBUTTONS_LAST_IN_SECTION => get_string('lastsection', 'block_navbuttons'));
        $mform->addElement('header', 'lastbutton', get_string('lastbutton', 'block_navbuttons'));
        $mform->addElement('select', 'lastbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'lastbuttonicon', get_string('buttonicon', 'block_navbuttons'));
        $mform->addElement('select', 'lastbuttontype', get_string('buttontype', 'block_navbuttons'), $lasttypes);


        $openin = array(BLOCK_NAVBUTTONS_SAMEWINDOW => get_string('linktargettop','editor'),
                        BLOCK_NAVBUTTONS_NEWWINDOW => get_string('linktargetblank','editor'));
        $mform->addElement('header', 'extra1', get_string('extra1', 'block_navbuttons'));
        $mform->addElement('select', 'extra1show', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'extra1icon', get_string('buttonicon', 'block_navbuttons'));
        $mform->addElement('text', 'extra1link', get_string('buttonlink', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('text', 'extra1title', get_string('buttontitle', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('select', 'extra1openin', get_string('buttonopenin', 'block_navbuttons'), $openin);

        $mform->addElement('header', 'extra2', get_string('extra2', 'block_navbuttons'));
        $mform->addElement('select', 'extra2show', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('choosecoursefile', 'extra2icon', get_string('buttonicon', 'block_navbuttons'));
        $mform->addElement('text', 'extra2link', get_string('buttonlink', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('text', 'extra2title', get_string('buttontitle', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('select', 'extra2openin', get_string('buttonopenin', 'block_navbuttons'), $openin);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'course', 0);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesettings');
        $mform->setType('course', PARAM_TEXT);

        $this->add_action_buttons();

    }
}

$mform = new block_navbuttons_edit_form();

$defaults = new stdClass;;
$settings = get_record('navbuttons', 'course', $course->id);
if (!$settings) {
    $settings = new stdClass;
    $settings->course = $course->id;
    $settings->id = insert_record('navbuttons', $settings);
    $settings = get_record('navbuttons', 'id', $settings->id);
}

$defaults->id = $settings->id;
$defaults->course = $settings->course;
$defaults->enabled = $settings->enabled;
$defaults->backgroundcolour = $settings->backgroundcolour;
$defaults->customusebackground = $settings->customusebackground;
$defaults->homebuttonshow = $settings->homebuttonshow;
$defaults->homebuttonicon = $settings->homebuttonicon;
$defaults->homebuttontype = $settings->homebuttontype;
$defaults->firstbuttonshow = $settings->firstbuttonshow;
$defaults->firstbuttonicon = $settings->firstbuttonicon;
$defaults->firstbuttontype = $settings->firstbuttontype;
$defaults->prevbuttonshow = $settings->prevbuttonshow;
$defaults->prevbuttonicon = $settings->prevbuttonicon;
$defaults->nextbuttonshow = $settings->nextbuttonshow;
$defaults->nextbuttonicon = $settings->nextbuttonicon;
$defaults->lastbuttonshow = $settings->lastbuttonshow;
$defaults->lastbuttonicon = $settings->lastbuttonicon;
$defaults->lastbuttontype = $settings->lastbuttontype;
$defaults->extra1show = $settings->extra1show;
$defaults->extra1icon = $settings->extra1icon;
$defaults->extra1link = $settings->extra1link;
$defaults->extra1title = $settings->extra1title;
$defaults->extra1openin = $settings->extra1openin;
$defaults->extra2show = $settings->extra2show;
$defaults->extra2icon = $settings->extra2icon;
$defaults->extra2link = $settings->extra2link;
$defaults->extra2title = $settings->extra2title;
$defaults->extra2openin = $settings->extra2openin;

$mform->set_data($defaults);

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}

if ($data = $mform->get_data() and $data->action == 'savesettings') {
    $update = new stdClass;
    
    $update->id = $data->id;
    $update->enabled = $data->enabled;
    $update->backgroundcolour = $data->backgroundcolour;
    $update->customusebackground = $data->customusebackground;
    $update->homebuttonshow = $data->homebuttonshow;
    $update->homebuttonicon = $data->homebuttonicon;
    $update->homebuttontype = $data->homebuttontype;
    $update->firstbuttonshow = $data->firstbuttonshow;
    $update->firstbuttonicon = $data->firstbuttonicon;
    $update->firstbuttontype = $data->firstbuttontype;
    $update->prevbuttonshow = $data->prevbuttonshow;
    $update->prevbuttonicon = $data->prevbuttonicon;
    $update->nextbuttonshow = $data->nextbuttonshow;
    $update->nextbuttonicon = $data->nextbuttonicon;
    $update->lastbuttonshow = $data->lastbuttonshow;
    $update->lastbuttonicon = $data->lastbuttonicon;
    $update->lastbuttontype = $data->lastbuttontype;
    $update->extra1show = $data->extra1show;
    $update->extra1icon = $data->extra1icon;
    $update->extra1link = $data->extra1link;
    $update->extra1title = $data->extra1title;
    $update->extra1openin = $data->extra1openin;
    $update->extra2show = $data->extra2show;
    $update->extra2icon = $data->extra2icon;
    $update->extra2link = $data->extra2link;
    $update->extra2title = $data->extra2title;
    $update->extra2openin = $data->extra2openin;

    if (update_record('navbuttons', $update)) {
        block_navbutton_settings_header($course);
        notify(get_string('settingsupdated', 'block_navbuttons'));
        print_continue($CFG->wwwroot.'/course/view.php?id='.$course->id);
        print_footer($course);
        die;
    }
}

block_navbutton_settings_header($course);

print_heading(get_string('editsettings', 'block_navbuttons'), '');

$mform->display();

print_footer($course);

function block_navbutton_settings_header($course) {
    $navlinks = array(array('name' => get_string('navbuttons','block_navbuttons')));
    $navigation = build_navigation($navlinks);

    print_header_simple(get_string('navbuttons', 'block_navbuttons'), '', $navigation, '', '', true, '', false);
}

?>