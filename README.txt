Navigation buttons (for Moodle 2.x)

This block adds navigation buttons (first/previous/next/etc.) to the bottom of each activity/resource page in a course.
As an enhancement it also adds completion button ('Mark complete'/'Mark incomplete') to the bottom of each activity/resource page in a course.
It is based on an idea from Penny Mondani: http://pennymondani.com
Thanks also to the US company that sponsored the development, who wish to remain anonymous.
Thanks to NHS Leadership Academy's Ishani Vardhan for contributing the "Complete button" enhancement

Changes:

2025-04-19 - 4.0.0.1 - M5.0 compatibility fixes, dropping support for M3.11 and below
2024-10-19 - 3.4.2.2 - Minor M4.5 compatibility fixes
2024-04-05 - 3.4.2.1 - Minor M4.4 compatibility fixes
2023-10-02 - 3.4.2.0 - update GitHub actions ready for 4.3 release
2023-04-14 - 3.4.1.0 - Minor M4.2 compatibility fixes
2022-05-28 - Fix 'Mark complete' button in M4.0
2022-01-22 - Minor M4.0 compatibility fixes
2021-04-09 - Minor internal documentation updates, switch to GitHub actions (from Travis)
2020-11-07 - If running Moodle 3.10, or above, you no longer need core changes to make this work
2020-04-10 - Do not show navbuttons on embedded questions
2019-10-18 - Fix compatibility with latest version of mod_questionnaire
2018-04-21 - Add GDPR declaration (no personal data stored). This requires M3.4 to work.
2017-10-09 - Add 'simplified text' option (removes the word 'activity' and the name of the activity).
2017-05-17 - Fix problems with earlier versions of Moodle caused by M3.3 compatibility
2017-05-12 - Moodle 3.3 compatibility fix
2017-03-15 - Added enhancement to mark an Acitivity/Resource complete or incomplete from within the activity using text buttons or icons with relevant settings
2016-05-20 - Minor fix to hide on the new assign grading page (as it doesn't work well there)
2015-08-05 - Adding block outside of course is no longer a fatal error (but it does nothing useful).
2014-07-06 - Added 'text button' option, checked M2.7 compatibility, minor styling tweak
2013-11-19 - Moodle 2.6 compatibility fixes
2013-10-21 - Extra note in README about custom / 3rd-party themes
2013-05-22 - Minor Moodle 2.5 compatibility fixes
2013-03-15 - Fixed PHP 5.3 compatibility issue
2012-12-30 - Fixed settings page in Moodle 2.4
2012-12-07 - Now compatible with Moodle 2.4
2012-07-02 - Confirmed all working fine with Moodle 2.3
2012-02-28 - New global settings to control which activities have navbuttons on them and when they should be shown

Installation:

1. Download the file and unzip it somewhere convenient.
2. On your server, in the 'blocks' folder, create a subfolder called 'navbuttons'
3. Upload all the files inside the 'davosmith-moodle-navbuttons-???????' folder to this new 'navbuttons' folder on your server.
4. Log in to your Moodle site as an administrator and click on the 'Notifications' option in the Admin settings block.
5. Add the block to a course (turn editing on, select 'Navigation Buttons' from the 'Blocks - Add' menu)
6. Customise the button appearance by clicking on 'Edit the Navigation Button settings' in the newly created block.
7. Check the message at the bottom of the screen (under the 'Save changes' button) - it should read "Navbuttons self-test: required core modifications have been completed successfully" (You don't actually need to make core modifications these days, but it confirms the code is working).

Note: The block is only visible to users who are able to edit courses modules. Deleting the block will remove the navigation buttons (they can also be disabled through the settings).

== Using with custom / 3rd-part themes ==

If the theme you are using overrides the page footer code (Essential theme is known to do this), then you will have to make some slightly different core changes in step 5.

Open up: theme/[theme name]/renderer.php
Look for a function called 'footer()'.
If you are lucky, you should be able to repeat the changes from step 5 within this function (I've had reports that this works with Essential theme).
If not, then you will have to try and figure out where best to put the code within the 'footer()' function.

== Any Problems ==

If you have a problem with the buttons not showing up as expected, then try the following:

1. Click on the 'Edit the Navigation Buttons settings' link in the block and scroll down to the bottom.
Check that the message reads 'Navbuttons self-test: required core modifications have been completed successfully'.
If not, go back up to step 5 of the installation instructions.

2. Visit 'Site admin > Plugins > Blocks > Navigation buttons' and check that the buttons are enabled for the activity
type you are viewing (the settings are 'Always show', 'Never show' or 'Show when complete', some activities have extra,
custom rules).

3. Visit a page that should have the navigation buttons on it, then use your browsers 'view source' menu option to see
the HTML code for the page. Search for '<!-- Navbuttons start -->' (without quotes). Look to see what message is displayed
after this line (and before '<!-- Navbuttons end -->'). It will be one of:
* Front page - navigation buttons are not displayed on the front page course
* No settings - the block has not been added to this course
* Not enabled - the block has been added, but the buttons are disabled in the settings
* No course module - this page is not part of a course module (activity/resource) within a course
* Activity not ready for navbuttons - the global settings mean that the buttons are not displayed for this activity (or
they will not be displayed until this activity is complete).

If you have any questions about this block, suggestions for improvement, drop me an email at:
moodle@davosmith.co.uk
