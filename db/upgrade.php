<?php

function xmldb_block_navbuttons_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012070202) {

        // Define table navbuttons to be created
        $table = new xmldb_table('navbuttons');

        // Adding fields to table navbuttons
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('backgroundcolour', XMLDB_TYPE_CHAR, '20', null, null, null, '#6666cc');
        $table->add_field('customusebackground', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('homebuttonshow', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('homebuttontype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('firstbuttonshow', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('firstbuttontype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_field('prevbuttonshow', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('nextbuttonshow', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('lastbuttonshow', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('lastbuttontype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '2');
        $table->add_field('extra1show', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('extra1link', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('extra1title', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('extra1openin', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('extra2show', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('extra2link', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('extra2title', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('extra2openin', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');

        // Adding keys to table navbuttons
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for navbuttons
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // navbuttons savepoint reached
        upgrade_block_savepoint(true, 2012070202, 'navbuttons');
    }

    return true;
 }
