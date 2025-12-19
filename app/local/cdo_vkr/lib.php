<?php
defined('MOODLE_INTERNAL') || die();

/**
 * @throws coding_exception
 * @throws dml_exception
 */
function local_cdo_vkr_extend_navigation(global_navigation $navigation)
{
    /*if (!has_capability("local/cdo_vkr:view", context_system::instance())) {
        return;
    }*/

   /* $node = navigation_node::create(
        get_string('pluginname', 'local_cdo_vkr'),
        new moodle_url('/local/cdo_vkr/index.php'),
        navigation_node::TYPE_CONTAINER,
        'shortname',
        'cdo_vkr_plan',
        new pix_icon('i/folder', get_string('pluginname', 'local_cdo_vkr'))
    );

    $node->showinflatnavigation = true;
    $navigation->add_node($node, 'myprofile');*/
}

function local_cdo_vkr_pluginfile(
    $course,
    $cm,
    $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options
)
{
    global $DB;
    if ($context->contextlevel != 10) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'vkr_area') {
        return false;
    }
    require_login();

    $itemid = array_shift($args);
    $filename = array_pop($args); // The last item in the $args array.
    if (empty($args)) {
        // $args is empty => the path is '/'.
        $filepath = '/';
    } else {
        // $args contains the remaining elements of the filepath.
        $filepath = '/' . implode('/', $args) . '/';
    }
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_cdo_vkr', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        // The file does not exist.
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);

}
