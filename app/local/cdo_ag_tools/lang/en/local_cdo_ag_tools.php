<?php

$string['pluginname'] = 'Ag tools';
$string['privacy:metadata'] = 'The Ag tools plugin does not store any personal data.';
$string['settings_availability'] = 'Additional availability settings';
$string['chose_user'] = 'Choose user';
$string['grade_report'] = 'File with completed grades';
$string['header'] = 'Settings for assigning the storage of control works';
$string['header_desc'] = '';
$string['file_repository'] = 'Repository';
$string['file_repository_desc'] = 'Select the repository that contains the required file structure';
$string['qr_code_y'] = 'Y coordinate';
$string['qr_code_y_desc'] = 'Specify the Y coordinate of the QR code';
$string['qr_code_x'] = 'X coordinate';
$string['qr_code_x_desc'] = 'Specify the X coordinate of the QR code';
$string['qr_code_size'] = 'QR code size';
$string['qr_code_size_desc'] = 'Specify the size of the QR code in pixels';
$string['fio_y'] = 'Y coordinate for full name';
$string['fio_y_desc'] = 'Specify the Y coordinate of the full name';
$string['fio_x'] = 'X coordinate for full name';
$string['fio_x_desc'] = 'Specify the X coordinate of the full name';

// New strings for qr_view.php
$string['qrcodegenerator'] = 'QR code generator';
$string['qrcode'] = 'Generation of control works';
$string['yourpersonalqrcode'] = 'After some time, the file will be generated and you can download it';
$string['uniqueqrcode'] = 'This QR code is unique to your account.';

// New string for the checkbox setting
$string['only_final_works'] = 'Upload only final works';
$string['only_final_works_desc'] = 'If checked, only final works will be uploaded.';

$string['new_numeric_setting'] = 'New numeric setting';
$string['new_numeric_setting_desc'] = 'Description for the new numeric setting.';
$string['button_text'] = 'Do something';

$string['accumulate_grades_task_name'] = 'Accumulate grades task';

$string['update_grades_for_doubling_title'] = 'Update grades for doubling';
$string['update_grades_for_doubling_heading'] = 'Update grades for doubling';
$string['course_id_label'] = 'Course ID';
$string['course_id_required'] = 'Please enter the course ID';
$string['course_id_numeric'] = 'Please enter a valid number';
$string['submit_button'] = 'Submit';
$string['notify_success'] = 'Regrading has been scheduled. Course for regrading: ';

$string['clear_zero_grades_link'] = 'Start clearing 0-grades';
$string['run_regrade_course_link'] = 'Start regrading in the course (doubling grades)';
$string['availability_settings_link'] = 'Availability settings';
$string['choose_course'] = 'Choose course';
$string['open_all_quarter'] = 'Open all quarter';
$string['close_all_quarter'] = 'Close all quarter';
$string['open'] = 'Open';
$string['close'] = 'Close';
$string['st_quarter'] = '1st quarter';
$string['nd_quarter'] = '2nd quarter';
$string['rd_quarter'] = '3rd quarter';
$string['th_quarter'] = '4th quarter';
$string['quarter'] = 'Quarter';
$string['actions'] = 'Actions';
$string['enter_surname'] = 'Enter surname';

$string['grade_notification_subject'] = 'New grade received';
$string['grade_notification_message'] = 'Hello! You have received a new grade {$grade} for {$activityname} in the course "{$coursename}".';
$string['usernotfound'] = 'User not found';
$string['noemail'] = 'User has no email address';

$string['course_list'] = 'Course list';

$string['gradeitemnotfound'] = 'Grade item not found';

$string['messageprovider:grade_update'] = 'Grade update notifications';
$string['messageprovider:grade_update_subject'] = 'New grade received';
$string['messageprovider:grade_update_message'] = 'Hello! You have received a new grade {$grade} for {$activityname} in the course "{$coursename}".';

// Assignment notifications
$string['messageprovider:grade_update_assign_subject'] = 'Assignment grade';
$string['messageprovider:grade_update_assign_message'] = 'Dear {$a->fullname}, your assignment "{$a->activityname}" in the course "{$a->coursename}" has been graded with {$a->grade}.';

// Quiz notifications
$string['messageprovider:grade_update_quiz_subject'] = 'Quiz results';
$string['messageprovider:grade_update_quiz_message'] = 'Dear {$a->fullname}, you have completed the quiz "{$a->activityname}" in the course "{$a->coursename}" with a score of {$a->grade}.';

// Lesson notifications
$string['messageprovider:grade_update_lesson_subject'] = 'Lesson grade';
$string['messageprovider:grade_update_lesson_message'] = 'Dear {$a->fullname}, you have received a grade of {$a->grade} for the lesson "{$a->activityname}" in the course "{$a->coursename}".';

$string['accumulate_grades_single_task_name'] = 'Accumulate grades for single user task';
$string['alt_pix_navigation'] = 'Navigation icon';
$string['sender_notifications'] = 'Grade notifications sending';

// Strings for 1C mass sync functionality
$string['sync_all_grades_to_1c_link'] = 'Sync all grades to 1C';
$string['sync_all_grades_to_1c_title'] = 'Mass sync all grades to 1C';
$string['sync_all_grades_to_1c_heading'] = 'Mass synchronization of grades with 1C';
$string['current_statistics'] = 'Current statistics';
$string['total_grades_in_table'] = 'Total grades in table: {$a}';
$string['unique_users'] = 'Unique users: {$a}';
$string['unique_courses'] = 'Unique courses: {$a}';
$string['warning'] = 'Warning';
$string['sync_warning_message'] = 'This operation will send ALL grades from the local_cdo_ag_tools_grades_1c table to 1C system. This process may take some time and cannot be undone. Make sure the 1C integration is properly configured.';
$string['start_sync_to_1c'] = 'Start synchronization with 1C';
$string['confirm_sync_all'] = 'Are you sure you want to send ALL grades to 1C? This operation cannot be undone.';
$string['sync_completed_successfully'] = 'Synchronization completed successfully. Processed: {$a->total_processed}, Successful: {$a->successful_sends}, Failed: {$a->failed_sends}';
$string['sync_failed'] = 'Synchronization failed: {$a}';
$string['sync_1c_disabled'] = '1C integration is disabled. Please enable it in plugin settings before using this feature.';
$string['sync_from_date'] = 'Sync grades from date';
$string['sync_from_date_help'] = 'Leave empty to sync all grades or specify a date to filter grades created from the specified date';
$string['error_future_date'] = 'Date cannot be in the future';
$string['sync_results_title'] = 'Synchronization Results';
$string['sync_total_processed'] = 'Total records processed: {$a}';
$string['sync_successful_sends'] = 'Successfully sent: {$a}';
$string['sync_failed_sends'] = 'Failed to send: {$a}';
$string['sync_success_rate'] = 'Success rate: {$a}%';
$string['sync_error_message'] = 'Synchronization error: {$a}';

// Missing strings found in code
$string['use_sequence'] = 'Use sequence';
$string['availability_settings_button_text'] = 'Go to availability settings';
$string['availability_settings_link'] = 'IP availability settings';
$string['test_send_grade_link'] = 'Test grade sending to 1C';
$string['test_send_grade_page_title'] = 'Test grade sending to 1C';
$string['send_test_grade_button'] = 'Send test grade';
$string['test_send_success'] = 'Test grade sent successfully.';
$string['test_send_failed'] = 'Failed to send test grade.';
$string['test_send_error'] = 'Error sending test grade';
$string['grade_digest_example_link'] = 'Grade Digest Example';
$string['import_grades_from_json_link'] = 'Import Grades from JSON';

// Grade digest strings
$string['grade_digest'] = 'Grade Digest';
$string['statistics'] = 'Statistics';
$string['total_grades'] = 'Total grades';
$string['average_grade'] = 'Average grade';
$string['max_grade'] = 'Maximum grade';
$string['min_grade'] = 'Minimum grade';
$string['section_topic'] = 'Topic/Section';
$string['section'] = 'Topic';
$string['module_name'] = 'Module name';
$string['module_type'] = 'Module type';
$string['grade'] = 'Grade';
$string['date'] = 'Date';
$string['digest_for_user'] = 'Grade digest for user: {$a}';
$string['no_grades_found'] = 'No grades found for the specified period';
$string['grades_by_courses'] = 'Grades by courses';
$string['from'] = 'from';
$string['to'] = 'to';

// Strings for reset overridden grades page
$string['reset_overridden_grades'] = 'Reset overridden grades status';
$string['reset_overridden_grades_title'] = 'Reset Overridden Grades Status';
$string['reset_overridden_grades_heading'] = 'Reset Overridden Grades Status';
$string['reset_overridden_grades_description'] = 'This page allows you to reset the "overridden" status for course and category final grades. Only grades that have the overridden status will be processed.';
$string['show_overridden_grades'] = 'Show overridden grades';
$string['reset_selected_grades'] = 'Reset selected grades';
$string['select_all'] = 'Select all';
$string['deselect_all'] = 'Deselect all';
$string['course_name'] = 'Course name';
$string['student_name'] = 'Student name';
$string['email'] = 'Email';
$string['grade_item_name'] = 'Grade item';
$string['final_grade'] = 'Final grade';
$string['overridden'] = 'Overridden';
$string['action'] = 'Action';
$string['no_overridden_grades'] = 'No overridden grades found';
$string['reset_success'] = 'Overridden status successfully reset for {$a} grades';
$string['reset_failed'] = 'Error resetting overridden status';
$string['confirm_reset'] = 'Are you sure you want to reset the overridden status for selected grades?';

// Strings for grade regrading
$string['regrade_grades'] = 'Regrade grades';
$string['regrade_grades_title'] = 'Regrade category and final grades';
$string['regrade_course'] = 'Course for regrading';
$string['regrade_all_courses'] = 'All courses with overridden grades';
$string['regrade_success'] = 'Grade regrading successfully started for {$a} courses';
$string['regrade_failed'] = 'Error starting grade regrading';
$string['confirm_regrade'] = 'Are you sure you want to start grade regrading? This operation may take some time.';

// Strings for work notification system
$string['weekly_quiz_report_task'] = 'Weekly quiz report task';

// Message providers for work notifications
$string['messageprovider:work_uploaded'] = 'Written work upload notifications';
$string['messageprovider:work_graded'] = 'Written work grading notifications';
$string['messageprovider:weekly_quiz_report'] = 'Weekly quiz reports';

// Notification subjects and messages
$string['work_uploaded_subject'] = '✅ Work Upload Confirmation';
$string['work_uploaded_message'] = '<div class="info-badge">Student Code: {$a->usercode}</div>
<p><strong>Hello!</strong></p>
<p>We confirm receipt of your completed work:</p>
<div class="work-item">
    📚 <strong>Course:</strong> <span class="highlight">{$a->coursename}</span><br>
    ✅ <strong>Status:</strong> <span class="success">Work successfully uploaded</span>
</div>
<p>Your work will be reviewed by our teachers and a grade will be assigned.</p>
<p>You will receive an additional notification when the review is complete.</p>';

$string['work_graded_subject'] = '⭐ Work Review Completed';
$string['work_graded_message'] = '<div class="info-badge">Student Code: {$a->usercode}</div>
<p><strong>Hello!</strong></p>
<p>Your work has been reviewed by the teachers of <strong>"Academic Gymnasium"</strong>:</p>
<div class="work-item">
    📚 <strong>Course:</strong> <span class="highlight">{$a->coursename}</span><br>
    <div class="grade-badge">Grade: {$a->grade}</div>
</div>
<p>Congratulations on completing the work! 🎉</p>';

$string['weekly_quiz_report_subject'] = '📊 Weekly Completed Work Report';
$string['weekly_quiz_report_message'] = '<div class="info-badge">Student Code: {$a->usercode}</div>
<p><strong>Hello!</strong></p>
<p>We confirm receipt of your completed work(s) for the period <strong>«{$a->datefrom} - {$a->dateto}»</strong></p>
<p>The following work(s) have been successfully recorded:</p>
<div class="work-list">
{$a->workslist}
</div>
<p>Keep up the great work! 💪</p>';

// Strings for user grades with categories page
$string['user_grades_title'] = 'User Grades with Categories';
$string['user_grades_heading'] = 'User Grades';
$string['grade_item'] = 'Grade Item';
$string['category'] = 'Category';
$string['category_total'] = 'Category Total';
$string['nogrades'] = 'No grades available';
$string['courses_enrolled'] = 'Courses you are enrolled in';
$string['select_user'] = 'Select User';
$string['grades_for_user'] = 'Grades for user';
$string['recent_users_hint'] = 'Enter User ID or email to view their grades';
$string['no_grades_explanation'] = 'This user has no grades yet. Possible reasons: assignments not completed, teacher has not graded the work yet, or the user is not enrolled in courses with grades.';
$string['user_grades_search_title'] = 'Search Users to View Grades';
$string['user_grades_search_description'] = 'Find a user by name, surname, email or username to view their grades';
$string['search_user_placeholder'] = 'Enter name, surname, email or username';
$string['found_users'] = 'Users found: {$a}';
$string['no_users_found'] = 'No users found';
$string['recent_active_users'] = 'Recently active users';
$string['view_grades'] = 'View grades';
$string['quick_access_by_id'] = 'Quick access by User ID';
