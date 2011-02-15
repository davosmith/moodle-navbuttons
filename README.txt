Navigation buttons (for Moodle 2.0)

This block adds navigation buttons (first/previous/next/etc.) to the bottom of each activity/resource page in a course.
It is based on an idea from Penny Mondani: http://pennymondani.com
Thanks also to the US company that sponsored the development, who wish to remain anonymous. 

Installation:

1. Download the file and unzip it somewhere convenient.
2. On your server, in the 'blocks' folder, create a subfolder called 'navbuttons'
3. Upload all the files inside the 'davosmith-moodle-navbuttons-???????' folder to this new 'navbuttons' folder on your server.
4. Log in to your Moodle site as an administrator and click on the 'Notifications' option in the Admin settings block.
5. You need to edit one Moodle core file to make this work:

Find the file 'moodle/lib/outputrenderers.php'.
Find the line:

$output = $this->container_end_all(true);

(It should only be there once, shortly after the line 'public function footer() {')
REPLACE the line you found with this code:

require_once($CFG->dirroot.'/blocks/navbuttons/footer.php');  // Add this line to enable the navigation buttons
$output = draw_navbuttons().$this->container_end_all(true);   // Change this line to enable the navigation buttonsgit

6. Add the block to a course (turn editing on, select 'Navigation Buttons' from the 'Blocks - Add' menu)
7. Customise the button appearance by clicking on 'Edit the Navigation Button settings' in the newly created block.
8. Check the message at the bottom of the screen (under the 'Save changes' button) - it should read "Navbuttons self-test: required core modifications have been completed successfully". If not, then go back to step 5 above and check carefully.

Note: The block is only visible to users who are able to edit courses modules. Deleting the block will remove the navigation buttons (they can also be disabled through the settings).


If you have any questions about this block, suggestions for improvement (or you want to employ me for any custom Moodle plugin development), drop me an email at:
davo@davodev.co.uk


