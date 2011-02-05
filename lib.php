<?php

function block_navbuttons_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload) {
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    require_course_login($course);

    if ($filearea !== 'icons') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $filename = $args[1];
    $iconid = $args[0];

    if (!$file = $fs->get_file($context->id, 'block_navbuttons', 'icons', $iconid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 60*60, 0, $forcedownload);
}

?>