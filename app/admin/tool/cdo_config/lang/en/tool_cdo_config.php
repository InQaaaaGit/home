<?php

//errors
$string['info_not_found'] = 'Information not found';
$string['nosectionmodules'] = 'No modules found in section {$a}';
$string['noassignmentfound'] = 'No assignment found in section {$a}';
$string['gradeitemnotfound'] = 'Grade item not found for assignment';

//plugin info

$string['plugin_short_name'] = 'EIEE';
$string['pluginname'] = 'EIEE config';
$string['plugin_full_name'] = 'Electronic information and educational environment';
$string['tool_cdo_config_integrations'] = 'Integration';
$string['name_cdo_config_integrations'] = 'Requests list';

$string['root_category'] = 'EIEE';

//plugin info

//setting_integration_form

//main group
$string['setting_integration_form_main'] = 'Base information';

$string['setting_integration_form_name'] = 'Request name';
$string['setting_integration_form_name_help'] = 'Name for request';

$string['setting_integration_form_description'] = 'Request description';
$string['setting_integration_form_description_help'] = 'Description for request';

$string['setting_integration_form_method'] = 'Used request method';
$string['setting_integration_form_method_help'] = 'Method used for request';

$string['setting_integration_form_endpoint'] = 'Request address';
$string['setting_integration_form_endpoint_help'] = 'Url of endpoint';

$string['setting_integration_form_port'] = 'Request port';
$string['setting_integration_form_port_help'] = 'Port used for request';
//main group
//auth group
$string['setting_integration_form_auth'] = 'Authorization';

$string['setting_integration_form_no_auth'] = 'No authorization';
$string['setting_integration_form_no_auth_help'] = "Don't use authorization";

$string['setting_integration_form_auth_token'] = 'Token authorization';
$string['setting_integration_form_auth_token_help'] = 'Use token authorization mechanism';

$string['setting_integration_form_login'] = 'Login';
$string['setting_integration_form_login_help'] = 'Remote user login';

$string['setting_integration_form_password'] = 'Password';
$string['setting_integration_form_password_help'] = 'Remote user password';

$string['setting_integration_form_type_token'] = 'Token type';
$string['setting_integration_form_type_token_help'] = 'Type of token';

$string['setting_integration_form_token'] = 'Token';
$string['setting_integration_form_token_help'] = 'Please input token';
//auth group
//call group
$string['setting_integration_form_call'] = 'Performing';

$string['setting_integration_form_code'] = 'Integration call code';
$string['setting_integration_form_code_help'] = 'Code for integration call';

$string['setting_integration_form_dto'] = 'DTO class path';
$string['setting_integration_form_dto_help'] = 'Please point to DTO class path';
//call group
//other group
$string['setting_integration_form_other'] = 'Additional';

$string['setting_integration_form_headers'] = 'Headers';
$string['setting_integration_form_headers_help'] = 'Please put headers';

$string['setting_integration_form_use_mock'] = 'Use test data';
$string['setting_integration_form_use_mock_help'] = 'Please set if you want test data use';

$string['setting_integration_form_mock'] = 'Test data';
$string['setting_integration_form_mock_help'] = 'Please put test data';
//other group

//error messages
$string['setting_integration_form_required_param'] = 'Required parameter';
$string['setting_integration_form_is_use_param'] = "This code already used by other function";
$string['setting_integration_form_empty_class'] = "Given class not found";
//error messages

//setting_integration_form

//pages

//settings/integrations/list
$string['settings_integrations_list_list_request'] = "Requests list";
//settings/integrations/list
//settings/integrations/single
$string['settings_integrations_single_deleted'] = "Record deleted!";
$string['settings_integrations_single_saved'] = "Record saved!";
$string['settings_integrations_single_create_request'] = "Make request";
$string['settings_integrations_single_edit_request'] = 'Editing {$a}';
//settings/integrations/single

//pages

//js

$string['js_modal_title_deleted'] = 'Delete';
$string['js_modal_question_deleted'] = "Deleting the selected item, are you sure?";
$string['js_modal_yes_label_deleted'] = "Continue";
$string['js_modal_no_label_deleted'] = "Cancel";

//js

$string['col_name'] = 'Name';
$string['col_method'] = 'Method';
$string['col_code'] = 'Request code';
$string['col_actions'] = 'Actions';

$string['no_records'] = 'No records';
$string['act_create'] = 'Create';
$string['act_update'] = 'Update';
$string['act_delete'] = 'Delete';

$string['wrong_code_in_exc'] = 'Wrong exception code: {$a}';
$string['exc_gradebook_notfound'] = 'Gradebook not found';
$string['exc_token_notfound'] = 'Token not found';
$string['exc_login_notfound'] = 'Login not found';
$string['exc_password_notfound'] = 'Password not found';
$string['exc_empty_token'] = 'Empty token';

$string['exc_record_not_found_by_id'] = 'Not found record by the id!';
$string['exc_request_not_found_by_code'] = 'Not found request by the code!';
$string['exc_byte_formation'] = 'Byte formation error';
$string['exc_wrong_string_for_decode'] = 'wrong string recieved for decode';
$string['exc_service_not_found'] = 'Not found requested service for jump';
$string['exc_not_filled_required_fields'] = 'Not filled required parameters for receive link for jump';
$string['exc_close_statement'] = 'Error on statement close';
$string['exc_db_record_not_found'] = 'DB record not found';
$string['exc_resource_not_found'] = 'Resource not found';
$string['exc_access_denied'] = 'Access denied';
$string['exc_timed_out'] = 'Timed out';
