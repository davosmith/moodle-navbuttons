Navigation buttons (for Moodle 1.9)

This block adds navigation buttons (first/previous/next/etc.) to the bottom of each activity/resource page in a course.
It is based on an idea from Penny Mondani: http://pennymondani.com
Thanks also to the US company that sponsored the development, who wish to remain anonymous. 

Installation:

1. Download the file and unzip it somewhere convenient.
2. On your server, in the 'blocks' folder, create a subfolder called 'navbuttons'
3. Upload all the files inside the 'davosmith-moodle-navbuttons-???????' folder to this new 'navbuttons' folder on your server.
4. Log in to your Moodle site as an administrator and click on the 'Notifications' option in the Admin settings block.
5. You need to edit one Moodle core file to make this work:

Find the file 'moodle/lib/weblib.php'.
Find the lines:

    ob_start();
    include($CFG->footer);
    $output = ob_get_contents();

(These should only be there once, several lines below the line starting 'function print_footer('...)
You need to add one line of code into this, so it now reads:

    ob_start();
    include($CFG->dirroot.'/blocks/navbuttons/footer.php'); // Add this line to enable the navigation buttons
    include($CFG->footer);
    $output = ob_get_contents();
    
Now find the lines:

function navmenu($course, $cm=NULL, $targetwindow='self') {

    global $CFG, $THEME, $USER;

And add this extra line immediately after:

function navmenu($course, $cm=NULL, $targetwindow='self') {

    global $CFG, $THEME, $USER;
    $THEME->cm = $cm;  // Add this line to enable the navigation buttons

6. Add the block to a course (turn editing on, select 'Navigation Buttons' from the 'Blocks - Add' menu)
7. Customise the button appearance by clicking on 'Edit the Navigation Button settings' in the newly created block.
8. Check the message at the bottom of the screen (under the 'Save changes' button) - it should read "Navbuttons self-test: required core modifications have been completed successfully". If not, then go back to step 5 above and check carefully.

Note: The block is only visible to users who are able to edit courses modules. Deleting the block will remove the navigation buttons (they can also be disabled through the settings).


If you have any questions about this block, suggestions for improvement (or you want to employ me for any custom Moodle plugin development), drop me an email at:
davo@davodev.co.uk

