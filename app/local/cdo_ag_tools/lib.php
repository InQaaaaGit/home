<?php
defined('MOODLE_INTERNAL') || die;

function local_ag_tools_extend_navigation_course(
    navigation_node $navigation,
    stdClass        $course,
    context_course  $context
)
{

}

/**
 * Serves files from the cdo_ag_tools plugin.
 *
 * @param stdClass      $course
 * @param stdClass      $cm
 * @param stdClass      $context
 * @param string        $filearea
 * @param array         $args
 * @param bool          $forcedownload
 * @param array         $options
 * @return bool|void
 */
function local_cdo_ag_tools_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($filearea !== 'works') {
        return false;
    }

    $itemid = (int)array_shift($args);
    $filepath = '/';
    $filename = array_shift($args);

    if (empty($filename)) {
        return false;
    }

    // Use the context ID provided.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_cdo_ag_tools', 'works', $itemid, $filepath, $filename);

    if (!$file) {
        return false; // Or handle the error as appropriate.
    }

    // Send the file.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
