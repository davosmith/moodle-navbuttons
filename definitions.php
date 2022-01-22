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

/**
 * Define statements
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package block_navbuttons
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG; // Fix codechecker complaining whether or not the MOODLE_INTERNAL check is present.

/** Home button to front page */
define("BLOCK_NAVBUTTONS_HOME_FRONTPAGE", 1);
/** Home button to course */
define("BLOCK_NAVBUTTONS_HOME_COURSE", 2);

/** First goes to course page */
define("BLOCK_NAVBUTTONS_FIRST_COURSE", 1);
/** First goes to first activity in course */
define("BLOCK_NAVBUTTONS_FIRST_IN_COURSE", 2);
/** First goes to first activity in section */
define("BLOCK_NAVBUTTONS_FIRST_IN_SECTION", 3);

/** Last goes to course page */
define("BLOCK_NAVBUTTONS_LAST_COURSE", 1);
/** Last goes to last activity in course */
define("BLOCK_NAVBUTTONS_LAST_IN_COURSE", 2);
/** Last goes to last activity in section */
define("BLOCK_NAVBUTTONS_LAST_IN_SECTION", 3);

/** Open in new window */
define("BLOCK_NAVBUTTONS_NEWWINDOW", 1);
/** Open in same window */
define("BLOCK_NAVBUTTONS_SAMEWINDOW", 0);

/** Home icon */
define("BLOCK_NAVBUTTONS_HOMEICON", 0);
/** First icon */
define("BLOCK_NAVBUTTONS_FIRSTICON", 1);
/** Previous icon */
define("BLOCK_NAVBUTTONS_PREVICON", 2);
/** Next icon */
define("BLOCK_NAVBUTTONS_NEXTICON", 3);
/** Last icon */
define("BLOCK_NAVBUTTONS_LASTICON", 4);
/** Extra icon 1 */
define("BLOCK_NAVBUTTONS_EXTRA1ICON", 5);
/** Extra icon 2 */
define("BLOCK_NAVBUTTONS_EXTRA2ICON", 6);

/** Button type: icon */
define("BLOCK_NAVBUTTONS_TYPE_ICON", 'icon');
/** Button type: text */
define("BLOCK_NAVBUTTONS_TYPE_TEXT", 'text');
/** Button type: simple */
define("BLOCK_NAVBUTTONS_TYPE_TEXT_SIMPLE", 'simple');
