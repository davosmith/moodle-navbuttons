Navigation buttons (for Moodle 1.9)

This block adds navigation buttons (first/previous/next/etc.) to the bottom of each activity/resource page in a course.

Installation:

1. Download the file and unzip it somewhere convenient.
2. Upload these extracted files to your Moodle server, creating a 'moodle/blocks/navbuttons' folder.
3. Log in to your Moodle site as an administrator and click on the 'Notifications' option in the Admin settings block.
4. You need to edit one Moodle core file to make this work:

Find the file 'moodle/lib/weblib.php'.
Find the lines:

    ob_start();
    include($CFG->footer);
    $output = ob_get_contents();

(These should only be there once, several lines below the line starting 'function print_footer('...)
You need to add one line of code into this, so it now reads:

    ob_start();
    include($CFG->dirroot.'/blocks/navbuttons/footer.php');
    include($CFG->footer);
    $output = ob_get_contents();

5. Add the block to a course (turn editing on, select 'Navigation Buttons' from the 'Blocks - Add' menu)
6. Customise the button appearance by clicking on 'Edit the Navigation Button settings' in the newly created block.

Note: The block is only visible to users who are able to edit courses modules. Deleting the block will remove the navigation buttons (they can also be disabled through the settings).


If you have any questions about this block, suggestions for improvement (or you want to employ me for any custom Moodle plugin development), drop me an email at:
davo@davodev.co.uk


