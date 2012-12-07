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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $CFG, $DB, $PAGE, $OUTPUT;

require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__).'/definitions.php');

$courseid = required_param('course', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid));
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
        $mform->addElement('static', 'colourselector', null, '<div id="yui-picker"></div>');
        $mform->addElement('selectyesno', 'customusebackground', get_string('customusebackground', 'block_navbuttons'));

        $hometypes = array(BLOCK_NAVBUTTONS_HOME_FRONTPAGE => get_string('frontpage','block_navbuttons'),
                           BLOCK_NAVBUTTONS_HOME_COURSE => get_string('coursepage', 'block_navbuttons'));
        $mform->addElement('header', 'homebutton', get_string('homebutton', 'block_navbuttons'));
        $mform->addElement('select', 'homebuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'homebuttonicon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));
        $mform->addElement('select', 'homebuttontype', get_string('buttontype', 'block_navbuttons'), $hometypes);

        $firsttypes = array(BLOCK_NAVBUTTONS_FIRST_COURSE => get_string('coursepage', 'block_navbuttons'),
                            BLOCK_NAVBUTTONS_FIRST_IN_COURSE => get_string('firstcourse','block_navbuttons'),
                            BLOCK_NAVBUTTONS_FIRST_IN_SECTION => get_string('firstsection', 'block_navbuttons'));
        $mform->addElement('header', 'firstbutton', get_string('firstbutton', 'block_navbuttons'));
        $mform->addElement('select', 'firstbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'firstbuttonicon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));
        $mform->addElement('select', 'firstbuttontype', get_string('buttontype', 'block_navbuttons'), $firsttypes);

        $mform->addElement('header', 'prevbutton', get_string('prevbutton', 'block_navbuttons'));
        $mform->addElement('select', 'prevbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'prevbuttonicon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));

        $mform->addElement('header', 'nextbutton', get_string('nextbutton', 'block_navbuttons'));
        $mform->addElement('select', 'nextbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'nextbuttonicon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));

        $lasttypes = array(BLOCK_NAVBUTTONS_LAST_COURSE => get_string('coursepage', 'block_navbuttons'),
                           BLOCK_NAVBUTTONS_LAST_IN_COURSE => get_string('lastcourse','block_navbuttons'),
                           BLOCK_NAVBUTTONS_LAST_IN_SECTION => get_string('lastsection', 'block_navbuttons'));
        $mform->addElement('header', 'lastbutton', get_string('lastbutton', 'block_navbuttons'));
        $mform->addElement('select', 'lastbuttonshow', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'lastbuttonicon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));
        $mform->addElement('select', 'lastbuttontype', get_string('buttontype', 'block_navbuttons'), $lasttypes);


        $openin = array(BLOCK_NAVBUTTONS_SAMEWINDOW => get_string('linktargettop','editor'),
                        BLOCK_NAVBUTTONS_NEWWINDOW => get_string('linktargetblank','editor'));
        $mform->addElement('header', 'extra1', get_string('extra1', 'block_navbuttons'));
        $mform->addElement('select', 'extra1show', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'extra1icon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));
        $mform->addElement('text', 'extra1link', get_string('buttonlink', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('text', 'extra1title', get_string('buttontitle', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('select', 'extra1openin', get_string('buttonopenin', 'block_navbuttons'), $openin);

        $mform->addElement('header', 'extra2', get_string('extra2', 'block_navbuttons'));
        $mform->addElement('select', 'extra2show', get_string('displaybutton', 'block_navbuttons'), $showhide);
        $mform->addElement('filemanager', 'extra2icon', get_string('buttonicon', 'block_navbuttons'), null,
                           array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image') ));
        $mform->addElement('text', 'extra2link', get_string('buttonlink', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('text', 'extra2title', get_string('buttontitle', 'block_navbuttons'), array('size'=>50));
        $mform->addElement('select', 'extra2openin', get_string('buttonopenin', 'block_navbuttons'), $openin);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'course', 0);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesettings');
        $mform->setType('action', PARAM_TEXT);

        $this->add_action_buttons();

    }
}

$mform = new block_navbuttons_edit_form();

$defaults = new stdClass;
$settings = $DB->get_record('navbuttons', array('course' => $course->id));
if (!$settings) {
    $settings = new stdClass;
    $settings->course = $course->id;
    $settings->id = $DB->insert_record('navbuttons', $settings);
    $settings = $DB->get_record('navbuttons', array('id' => $settings->id));
}

$defaults->id = $settings->id;
$defaults->course = $settings->course;
$defaults->enabled = $settings->enabled;
$defaults->backgroundcolour = $settings->backgroundcolour;
$defaults->customusebackground = $settings->customusebackground;
$defaults->homebuttonshow = $settings->homebuttonshow;
$defaults->homebuttontype = $settings->homebuttontype;
$defaults->firstbuttonshow = $settings->firstbuttonshow;
$defaults->firstbuttontype = $settings->firstbuttontype;
$defaults->prevbuttonshow = $settings->prevbuttonshow;
$defaults->nextbuttonshow = $settings->nextbuttonshow;
$defaults->lastbuttonshow = $settings->lastbuttonshow;
$defaults->lastbuttontype = $settings->lastbuttontype;
$defaults->extra1show = $settings->extra1show;
$defaults->extra1link = $settings->extra1link;
$defaults->extra1title = $settings->extra1title;
$defaults->extra1openin = $settings->extra1openin;
$defaults->extra2show = $settings->extra2show;
$defaults->extra2link = $settings->extra2link;
$defaults->extra2title = $settings->extra2title;
$defaults->extra2openin = $settings->extra2openin;

$draftitemid = file_get_submitted_draft_itemid('homebuttonicon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_HOMEICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->homebuttonicon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('firstbuttonicon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_FIRSTICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->firstbuttonicon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('prevbuttonicon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_PREVICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->prevbuttonicon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('nextbuttonicon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_NEXTICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->nextbuttonicon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('lastbuttonicon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_LASTICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->lastbuttonicon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('extra1icon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_EXTRA1ICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->extra1icon = $draftitemid;
$draftitemid = file_get_submitted_draft_itemid('extra2icon');
file_prepare_draft_area($draftitemid, $context->id, 'block_navbuttons', 'extra2icon', BLOCK_NAVBUTTONS_EXTRA2ICON, array('subdirs' => 0, 'maxfiles' => 1));
$defaults->extra2icon = $draftitemid;

$mform->set_data($defaults);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
}

if ($data = $mform->get_data() and $data->action == 'savesettings') {
    $update = new stdClass;

    $update->id = $data->id;
    $update->enabled = $data->enabled;
    $update->backgroundcolour = $data->backgroundcolour;
    $update->customusebackground = $data->customusebackground;
    $update->homebuttonshow = $data->homebuttonshow;
    $update->homebuttontype = $data->homebuttontype;
    $update->firstbuttonshow = $data->firstbuttonshow;
    $update->firstbuttontype = $data->firstbuttontype;
    $update->prevbuttonshow = $data->prevbuttonshow;
    $update->nextbuttonshow = $data->nextbuttonshow;
    $update->lastbuttonshow = $data->lastbuttonshow;
    $update->lastbuttontype = $data->lastbuttontype;
    $update->extra1show = $data->extra1show;
    $update->extra1link = $data->extra1link;
    $update->extra1title = $data->extra1title;
    $update->extra1openin = $data->extra1openin;
    $update->extra2show = $data->extra2show;
    $update->extra2link = $data->extra2link;
    $update->extra2title = $data->extra2title;
    $update->extra2openin = $data->extra2openin;

    file_save_draft_area_files($data->homebuttonicon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_HOMEICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->firstbuttonicon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_FIRSTICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->prevbuttonicon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_PREVICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->nextbuttonicon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_NEXTICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->lastbuttonicon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_LASTICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->extra1icon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_EXTRA1ICON, array('subdirs' => 0, 'maxfiles' => 1));
    file_save_draft_area_files($data->extra2icon, $context->id, 'block_navbuttons', 'icons', BLOCK_NAVBUTTONS_EXTRA2ICON, array('subdirs' => 0, 'maxfiles' => 1));

    if ($DB->update_record('navbuttons', $update)) {
        block_navbutton_settings_header($course);
        echo $OUTPUT->notification(get_string('settingsupdated', 'block_navbuttons'));
        echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
        echo $OUTPUT->footer();
        die;
    }
}

if ($CFG->version < 2012120300) { // < Moodle 2.4
    $PAGE->requires->yui2_lib('dom');
    $PAGE->requires->yui2_lib('event');
    $PAGE->requires->yui2_lib('element');
    $PAGE->requires->yui2_lib('dragdrop');
    $PAGE->requires->yui2_lib('slider');
    $PAGE->requires->yui2_lib('colorpicker');
    $PAGE->requires->yui2_lib('get');
    $jsmodule = array(
        'name' => 'block_navbuttons',
        'fullpath' => new moodle_url('/blocks/navbuttons/edit.js')
    );
} else { // Moodle 2.4
    $jsmodule = array(
        'name' => 'block_navbuttons',
        'fullpath' => new moodle_url('/blocks/navbuttons/edit24.js')
    );
}
$cssurl = new moodle_url('/lib/yui/2.8.2/build/assets/skins/sam');
$PAGE->requires->js_init_call('navbuttons.init', array($cssurl->out()), true, $jsmodule);



block_navbutton_settings_header($course);

echo $OUTPUT->heading(get_string('editsettings', 'block_navbuttons'), 1);

$mform->display();

$CFG->navbuttons_self_test = 1;
$footer = $OUTPUT->footer();

if ($CFG->navbuttons_self_test == 1) { // footer.php not called at all
    echo '<strong style="background-color: red;">'.get_string('selftest_nofooter','block_navbuttons').'</strong><br />';
} else {
    echo '<em>'.get_string('selftest_ok','block_navbuttons').'</em>';
}

echo $footer;

function block_navbutton_settings_header($course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_url(new moodle_url('/blocks/navbuttons/edit.php', array('course'=>$course->id)));
    $PAGE->set_title(get_string('navbuttons', 'block_navbuttons'));
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
}
