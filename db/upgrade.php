<?php
// This file is part of the Navigation Buttons plugin for Moodle - http://moodle.org/
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

function xmldb_block_navbuttons_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012070202) {

        // Define table navbuttons to be created.
        $table = new xmldb_table('navbuttons');

        // Adding fields to table navbuttons.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('backgroundcolour', XMLDB_TYPE_CHAR, '20', null, null, null, '#6666cc');
        $table->add_field('customusebackground', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('homebuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('homebuttontype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('firstbuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('firstbuttontype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '2');
        $table->add_field('prevbuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('nextbuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('lastbuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('lastbuttontype', XMLDB_TYPE_INTEGER, '4', null, null, null, '2');
        $table->add_field('extra1show', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('extra1link', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('extra1title', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('extra1openin', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('extra2show', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('extra2link', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('extra2title', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('extra2openin', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table navbuttons.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for navbuttons.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Navbuttons savepoint reached.
        upgrade_block_savepoint(true, 2012070202, 'navbuttons');
    }

    if ($oldversion < 2014070601) {

        // Define field buttonstype to be added to navbuttons.
        $table = new xmldb_table('navbuttons');
        $field = new xmldb_field('buttonstype', XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, 'icon', 'enabled');

        // Conditionally launch add field buttonstype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Navbuttons savepoint reached.
        upgrade_block_savepoint(true, 2014070601, 'navbuttons');
    }

    if ($oldversion < 2017030600) {

        // Define field completebuttonshow to be added to navbuttons.
        $table = new xmldb_table('navbuttons');
        $field = new xmldb_field('completebuttonshow', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'extra2openin');

        // Conditionally launch add field completebuttonshow.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Navbuttons savepoint reached.
        upgrade_block_savepoint(true, 2017030600, 'navbuttons');
    }

    return true;
}
