<?php

//main
$string['pluginname'] = "Certification Sheets";
$string['short_name'] = "Sheets";
$string['access_denied'] = 'You do not have access to the {$a} section!<br>Contact your administrator!';
$string['settings_page'] = "General settings";
//main
//form
$string['setting_form_main'] = "Basic information";
$string['setting_form_code'] = "Sheet code with commission";
$string['required_param'] = "Required!";
$string['setting_form_code_help'] = "<p>
This code will determine whether this sheet is a sheet with a commission. This code is taken from the type_code field.
</p>";
//form

//tabs
$string['tabs_agreement'] = "Sign";
//tabs

//agreed js
$string['grades_confirm_change_agreed_title'] = "Confirmation";
$string['grades_confirm_change_agreed_message'] = "Are you sure you want to approve the sheet?";
$string['grades_confirm_change_agreed_yes'] = "Confirm";
$string['grades_confirm_change_agreed_no'] = "Cancel";
$string['grades_alert_change_agreed_title'] = "Sheet approval";
$string['grades_alert_change_agreed_message'] = "Approval successfully saved";
$string['grades_alert_change_agreed_yes'] = "Close";
//agreed js

//close js
$string['grades_confirm_change_close_title'] = "Confirmation";
$string['grades_confirm_change_close_message'] = "Are you sure you want to close the sheet?";
$string['grades_confirm_change_close_yes'] = "Confirm";
$string['grades_confirm_change_close_no'] = "Cancel";
$string['grades_alert_change_close_title'] = "Closing the sheet";
$string['grades_alert_change_close_message'] = "The sheet has been successfully closed!";
$string['grades_alert_change_close_yes'] = "Close";
//close js

//grades js
$string['grades_confirm_change_grade_title'] = "Confirmation";
$string['grades_confirm_change_grade_message'] = "Are you sure you want to change the grade for the user?";
$string['grades_confirm_change_grade_yes'] = "Confirm";
$string['grades_confirm_change_grade_no'] = "Cancel";
$string['grades_alert_change_grade_title'] = "Grading";
$string['grades_alert_change_grade_message'] = "Grade successfully changed";
$string['grades_alert_change_grade_yes'] = "Close";
//grades js

//sheet api
$string['sheet_api_guid_grade'] = "Grade GUID";
$string['sheet_api_guid_save_grade'] = "Saved grade GUID";
$string['sheet_api_guid_student'] = "Student GUID";
$string['sheet_api_guid_sheet'] = "Sheet GUID";
$string['sheet_api_change_current_grade'] = "Parameters for changing the current grade";
$string['sheet_api_structure_info_about_grade'] = "Grade information structure";
$string['sheet_api_list_current_grades'] = "List of current grades";
$string['sheet_api_user_id'] = "User ID";
$string['sheet_api_agreed_sheet'] = "Parameters for sheet approval";
$string['sheet_api_sheet_is_empty'] = "Could not find the sheet";
$string['sheet_api_count_student_change'] = "The number of students in the original and saved sheets is different";
$string['sheet_api_student_not_found'] = "Could not find the student";
$string['sheet_api_grades_not_match'] = "Grades do not match";
$string['sheet_api_structure_close_sheet'] = "Parameters for closing the sheet";
//sheet api

//close sheet
$string['close_sheet_close_button'] = "Close sheet";
$string['close_sheet_reload_button'] = "Reload sheet";
//close sheet
//commission sheet
$string['commission_sheet_agreed_message_yes'] = "Signed";
$string['commission_sheet_agreed_message_no'] = "Not signed";
$string['commission_sheet_user_full_name'] = "Full name";
$string['commission_sheet_activity'] = "Confirmation";
$string['commission_sheet_agreed'] = "Approve";
$string['commission_sheet_chairman'] = "Chairman";
//commission sheet
//list sheet
$string['list_sheet_not_found_open_sheet'] = "Could not find open sheets";
//list sheet
//grade element
$string['grade_element_not_grades'] = "Not graded";
$string['point_control_event'] = "Points for control event";
$string['point_semester'] = "Points for semester";
$string['absence'] = "Absence";

//grade element
//table sheet
$string['table_sheet_user_full_name'] = "Student's full name";
$string['table_sheet_grade_book'] = "Grade book";
$string['table_sheet_grade'] = "Grade";
$string['table_sheet_teacher_grade'] = "Graded by";
//table sheet
//info sheet
$string['sheet_name_plan'] = "Curriculum";
$string['sheet_group'] = "Group";
$string['sheet_profile'] = "Profile";
$string['sheet_semester'] = "Semester";
$string['sheet_division'] = "Division";
$string['sheet_form_education'] = "Form of study";
$string['sheet_level_education'] = "Level of education";
$string['sheet_specialty'] = "Specialty";
$string['sheet_course'] = "Course";
$string['sheet_guid'] = "Unique identifier";
$string['sheet_discipline'] = "Discipline";
$string['sheet_type_control'] = "Control type";
$string['sheet_name_sheet'] = "Sheet name";
$string['sheet_type'] = "Sheet type";
$string['sheet_type_code'] = "Sheet type code";
$string['sheet_points_semester'] = "Points for semester";
$string['sheet_control_event'] = "Points for control event";
$string['sheet_theme_placeholder'] = "Enter the course work topic";
$string['sheet_theme'] = "Course work topic";
$string['sheet_download'] = "Download sheet";
$string['sheet_date'] = "Sheet date";

//info sheet

//toast
$string['toast_success'] = "Success!";

//errors
$string['sheet_guid_not_found'] = "The sheet structure is missing a GUID or it is incorrect!";

//settings
$string['show_BRS'] = "Participating levels of training in BRS";
$string['division_for_BRS'] = "Participating Higher Schools in BRS";
$string['show_BRS_description'] = "Specify the names of the training levels from 1C, separated by commas (,)";
$string['division_for_BRS_description'] = "Specify the names of the schools from 1C, separated by commas (,)";
$string['guid_absence'] = "GUID of the 'Absence' grade from 1C";
$string['guid_absence_description'] = "Get the 36-character GUID from 1C";
$string['guid_absence_not_set'] = "The 'Absence' grade GUID is not set. Please contact the administration";

//grades
$string['grade_unsatisfactory'] = 'Unsatisfactory';
$string['grade_satisfactory'] = 'Satisfactory';
$string['grade_good'] = 'Good';
$string['grade_excellent'] = 'Excellent';
$string['average_discipline_rating'] = 'Average discipline rating (ADR)';
$string['rating_intermediate_certification_discipline'] = 'Rating of intermediate certification in the discipline (RICD)';
$string['final_rating_discipline'] = 'Final discipline rating (FDR)';
$string['ysc_competence_level'] = 'Level of competence formation within the discipline';
$string['commission_sheet_title'] = 'Commission';
$string['current_grade'] = 'Current grade';
$string['absence_grade'] = 'Absence';
$string['sheet_tab_name'] = 'Sheet';
$string['loading'] = 'Loading...';

//vue settings
$string['enable_vue_components'] = "Use Vue components";
$string['enable_vue_components_description'] = "Enable Vue.js components for rendering the main application";

// layout settings
$string['layout_settings'] = 'Layout Settings';
$string['layout_settings_desc'] = 'Here you can configure the layout of the certification sheets.';
$string['show_left_panel'] = 'Show left panel';
$string['show_left_panel_desc'] = 'Show/hide the left panel with main information.';
$string['show_right_panel'] = 'Show right panel';
$string['show_right_panel_desc'] = 'Show/hide the right panel with additional information.';
$string['layout_type'] = 'Layout type';
$string['layout_type_desc'] = 'Select the layout type for the certification sheets.';
$string['layout_type_default'] = 'Default';
$string['layout_type_two_rows'] = 'Two rows';
$string['layout_type_vertical'] = 'Vertical';
$string['advanced_layout'] = 'Advanced layout';
$string['advanced_layout_desc'] = 'Advanced layout settings for custom components.';
$string['enable_custom_components'] = 'Enable custom components';
$string['enable_custom_components_desc'] = 'Enable/disable custom Vue.js components in the layout.';
$string['show_download_button'] = 'Show download button';
$string['show_download_button_description'] = 'Show/hide the download button for the certification sheet.';

//error page
$string['error_page_title'] = 'Error';
$string['error_download_failed'] = 'Failed to download the file. Please try again later or contact the administrator.';
$string['error_connection'] = 'Connection error. Please check your internet connection and try again.';
$string['error_invalid_certificate'] = 'Invalid certificate. Please check the data and try again.';
$string['error_permission_denied'] = 'Access denied. You do not have permission to perform this action.';
$string['error_general'] = 'An error occurred. Please try again later or contact the administrator.';
$string['error_occurred'] = 'An error occurred';
$string['error_type_download'] = 'Download error';
$string['error_type_connection'] = 'Connection error';
$string['error_type_invalid'] = 'Invalid data';
$string['error_type_permission'] = 'Access denied';
$string['error_type_general'] = 'General error';
$string['error_troubleshooting'] = 'Troubleshooting tips:';
$string['error_tip_network'] = 'Check your internet connection';
$string['error_tip_retry'] = 'Try again in a few minutes';
$string['error_tip_contact'] = 'If the problem persists, contact the system administrator';
$string['error_return'] = 'Go back';
$string['error_retry'] = 'Retry';
$string['error_debug_info'] = 'Debug information (administrators only)';
$string['error_code'] = 'Error code';
$string['error_message'] = 'Error message';
$string['error_time'] = 'Error time';
