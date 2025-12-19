<?php

use mod_assign\downloader;

require_once(__DIR__ . "/../../../config.php");
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/user/lib.php');
defined('MOODLE_INTERNAL') || die();

$curl = new curl();
$auth = base64_encode("useru:1");
$curl->setopt([
    'CURLOPT_USERPWD' => $auth,

]);
new stored_file()
$curl->setHeader([
    "Authorization: Basic {$auth}"
]);

try {
    list ($course, $cm) = get_course_and_cm_from_cmid(2, 'assign');
    $context = context_module::instance($cm->id);
} catch (moodle_exception $e) {
}
$users = user_get_users_by_id([3]);


try {
    $assign = new assign($context, $cm, $course);

    foreach ($assign->get_submission_plugins() as $plugin) {
        if (!$plugin->is_enabled() || !$plugin->is_visible()) {
            continue;
        }

        foreach ($users as $id => $student) {
            $sending_submissions = [];
            $submission = $assign->get_user_submission(3, false);
            if (!is_bool($submission)) {

            }
            $pluginfiles = $plugin->get_files($submission, $student);
            $files_of_submissions = [];

            foreach ($pluginfiles as $key => $pluginfile) {

                $file_info = [
                    'extension' => '',
                    'filename' => $pluginfile->get_filename(),
                    'body64' => base64_encode($pluginfile->get_content())
                ];
                $files_of_submissions[] = $file_info;
            }

            $sending_submissions[] = [
                'files_of_submissions' => $files_of_submissions,
                'user_id' => 3,
            ];

            $status = $curl->post(
                'http://demo.cdo-global.ru/sgua_univer_goryshkin/hs/test_case/post_file',
                json_encode(['JSON' => $sending_submissions])
            );

        }

        //$this->load_submissionplugin_filelist($student, $plugin, $submission, '6d');
    }
} catch (Exception $exception) {
    echo $exception->getCode();
}


/*$downloader = new downloader($assign, [] ?: null);
$downloader->load_filelist();*/
//$f = $downloader->filesforzipping;
$f = 1;
