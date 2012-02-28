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

require_once(dirname(__FILE__).'/activityready.php');

if ($ADMIN->fulltree) {
    $plugins = get_plugin_list('mod');
    $pluginopts = array();
    $stralways = get_string('activityalways', 'block_navbuttons');
    $strcomplete = get_string('activitycomplete', 'block_navbuttons');
    $strnever = get_string('activitynever', 'block_navbuttons');
    foreach ($plugins as $pluginname => $unused) {
        if ($pluginname == 'label') {
            continue;
        }
        $pluginopts[$pluginname] = array(NAVBUTTONS_ACTIVITY_ALWAYS => $stralways,
                                         NAVBUTTONS_ACTIVITY_COMPLETE => $strcomplete);
        $funcname = 'navbuttons_mod_'.$pluginname.'_showbuttons';
        if (function_exists($funcname)) {
            $pluginopts[$pluginname][NAVBUTTONS_ACTIVITY_CUSTOM] = get_string('activitycustom'.$pluginname, 'block_navbuttons');
        }
        $pluginopts[$pluginname][NAVBUTTONS_ACTIVITY_NEVER] = $strnever;
    }

    $settings->add(new admin_setting_heading('block_navbuttons/intro', '', get_string('activityreadydesc', 'block_navbuttons')));
    foreach ($pluginopts as $pluginname => $opts) {
        $settings->add(new admin_setting_configselect('block_navbuttons/activity'.$pluginname, get_string('pluginname', $pluginname), '',
                                                      NAVBUTTONS_ACTIVITY_ALWAYS, $opts));
    }
}