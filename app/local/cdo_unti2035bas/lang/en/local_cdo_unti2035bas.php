<?php
$string['pluginname'] = 'CDO UNTI2035 BAS';
$string['pluginsettings'] = 'CDO UNTI2035 BAS: Settings';
$string['xapiendpoint'] = 'xAPI Endpoint';
$string['xapiendpointhelp'] = 'Put URL for put LRS statements';
$string['xapikey'] = 'xAPI Key';
$string['xapikeyhelp'] = 'Put Auth key';
$string['xapisecret'] = 'xAPI Secret';
$string['xapisecrethelp'] = 'Put Auth secret';
$string['xapitest'] = 'Test xAPI configuration';
$string['xapitestdetail'] = 'Note: Before testing, please save your configuration.<br />{$a}';
$string['xapitestok'] = 'xAPI Connection Check: OK';
$string['s3endpoint'] = 'S3 Endpoint';
$string['s3endpointhelp'] = 'Url for S3 service';
$string['s3accesskey'] = 'S3 Access Key';
$string['s3accesskeyhelp'] = 'Put Access key';
$string['s3secretkey'] = 'S3 Secret Key';
$string['s3secretkeyhelp'] = 'Put Secret key';
$string['s3bucket'] = 'S3 Bucket';
$string['s3buckethelp'] = 'Put Bucket name';
$string['s3baseurl'] = 'S3 base url';
$string['s3baseurlhelp'] = 'Put public S3 base url';
$string['s3test'] = 'Test S3 configuration';
$string['s3testdetail'] = 'Note: Before testing, please save your configuration.<br />{$a}';
$string['s3testok'] = 'S3 Connection Check: OK';
$string['streamcreate'] = 'Create Stream';
$string['streamcomment'] = 'Comment';
$string['untiprogramid'] = 'Program UntiID';
$string['untiflowid'] = 'Flow UntiID';
$string['untimethodistid'] = 'Methodist UntiID';
$string['untimoduleid'] = 'Module UntiID';
$string['untithemeid'] = 'Theme UntiID';
$string['streamstitle'] = 'Streams';
$string['streamsdetails'] = 'CDO UNTI2035 BAS: Streams control';
$string['streamedit'] = 'Edit Stream';
$string['streamcreate'] = 'Create Stream';
$string['moduleedit'] = 'Edit Module';
$string['modulecreate'] = 'Create Module';
$string['themeedit'] = 'Edit Theme';
$string['themecreate'] = 'Create Theme';
$string['activityedit'] = 'Edit Activity';
$string['activitycreate'] = 'Create Activity';
$string['assessmentedit'] = 'Edit Assessment';
$string['assessmentcreate'] = 'Create Assessment';
$string['lrid'] = 'Learning Record ID';
$string['timestamp'] = 'Timestamp';
$string['actions'] = 'Actions';
$string['render'] = 'Render';
$string['send'] = 'Send';
$string['details'] = 'Details';
$string['isonline'] = 'Online';
$string['log'] = 'Log';
$string['object_'] = 'Object';
$string['objectid'] = 'Object ID';
$string['objectversion'] = 'Object Version';
$string['level'] = 'Level';
$string['message'] = 'Message';
$string['title'] = 'Title';
$string['name'] = 'Name';
$string['type'] = 'Type';
$string['value'] = 'Value';
$string['stream'] = 'Stream';
$string['flow'] = 'Flow';
$string['block'] = 'Block';
$string['module'] = 'Module';
$string['theme'] = 'Theme';
$string['assessment'] = 'Assessment';
$string['sendok'] = 'Send: OK';
$string['academichourminutes'] = 'Minutes in academic hour';
$string['sync'] = 'Sync';
$string['required'] = 'Required';
$string['collaborative'] = 'Collaborative';
$string['lectureshours'] = 'Lectures academic hours';
$string['workshopshours'] = 'Workshops academic hours';
$string['independentworkhours'] = 'Independentwork academic hours';
$string['deleted'] = '[Deleted]';
$string['pathtomediainfo'] = 'Path to mediainfo';
$string['pathtomediainfodesc'] = 'Path to your executable Mediainfo';
$string['download'] = 'Download';
$string['execute'] = 'Execute script';
$string['cancel'] = 'Cancel';
$string['cancelconfirmtitle'] = 'Cancel statement';
$string['cancelconfirmquestion'] = 'Really cancel statement with learning record id: {$a}?';
$string['cancelok'] = 'Cancel OK';
$string['resultcomparability'] = 'Result comparability';
$string['admittanceform'] = 'Admittance form';
$string['haspractice'] = 'Has practice';
$string['documenttype'] = 'Document type';
$string['hours'] = 'Hours';

// xAPI Results
$string['xapi_results'] = 'xAPI Results';
$string['statistics'] = 'Statistics';
$string['total_records'] = 'Total Records';
$string['last_24h'] = 'Last 24 Hours';
$string['cleanup_old_records'] = 'Cleanup Old Records';
$string['recent_results'] = 'Recent Results';
$string['id'] = 'ID';
$string['status_code'] = 'Status Code';
$string['query_preview'] = 'Query Preview';
$string['view_details'] = 'View Details';
$string['delete'] = 'Delete';
$string['confirm_delete'] = 'Are you sure you want to delete this record?';
$string['no_results'] = 'No results found';
$string['record_deleted'] = 'Record deleted successfully';
$string['records_cleaned'] = '{$a} old records cleaned up';
$string['record_not_found'] = 'Record not found';
$string['query'] = 'Query';
$string['result'] = 'Result';
$string['curl_error'] = 'cURL Error';
$string['response'] = 'Response';
$string['request_info'] = 'Request Information';

// Video statements settings
$string['send_video_statements_head'] = 'Send xAPI statements for video';
$string['send_video_statements_desc'] = 'Settings for sending xAPI statements when viewing video content';
$string['send_video_statements_link'] = 'SEND';
$string['send_video_statements_result'] = 'Result is OK';
$string['xapi_sent_count'] = 'Sent: {$a}';
$string['xapi_skipped_count'] = 'Skipped (duplicates): {$a}';
$string['xapi_errors_count'] = 'Errors: {$a}';
$string['xapi_total_processed'] = 'Total processed: {$a}';
$string['xapi_no_data_to_send'] = 'No data to send xAPI statements';

// Grade statements settings
$string['send_grade_statements_head'] = 'Send xAPI statements for grades';
$string['send_grade_statements_desc'] = 'Settings for sending xAPI statements when grades are assigned: {$a}';
$string['send_grade_statements_link'] = 'Send grades';
$string['send_grade_statements_result'] = 'Grade sending result: {$a}';
$string['grade_sent_count'] = 'Grades sent: {$a}';
$string['grade_skipped_count'] = 'Grades skipped (duplicates): {$a}';
$string['grade_errors_count'] = 'Grade errors: {$a}';
$string['grade_total_processed'] = 'Total grades processed: {$a}';
$string['grade_no_data_to_send'] = 'No grade data to send xAPI statements';

// JSON schema sending settings
$string['send_json_scheme_head'] = 'Send JSON schemas';
$string['send_json_scheme_desc'] = 'Form for manual sending of arbitrary JSON schemas to xAPI: {$a}';
$string['send_json_scheme_link'] = 'Send JSON schema';
$string['grade_course_selection'] = 'Select course for grade sending';
$string['grade_course_selection_help'] = 'Select course from available streams list to send xAPI statements with grades';
$string['grade_send_for_course'] = 'Send grades for course ID: {$a}';

// Additional strings for detailed display
$string['coursenotfound'] = 'Course not found';
$string['detailed_results'] = 'Detailed results';
$string['successful_submissions'] = 'Successful submissions';
$string['error_submissions'] = 'Error submissions';
$string['user'] = 'User';
$string['email'] = 'Email';
$string['grade_item'] = 'Grade item';
$string['grade'] = 'Grade';
$string['time'] = 'Time';
$string['error'] = 'Error';
$string['error_type'] = 'Error type';
$string['download_error_report'] = 'Download error report';

// Error types
$string['mapping_error'] = 'Grade item mapping errors';
$string['user_mapping_error'] = 'User mapping errors';
$string['course_mapping_error'] = 'Course mapping errors';
$string['network_error'] = 'Network errors';
$string['permission_error'] = 'Permission errors';
$string['unknown_error'] = 'Other errors';

// Error descriptions
$string['mapping_error_help'] = 'Grade items not found in UNTI mapping system';
$string['user_mapping_error_help'] = 'UNTI user IDs not found';
$string['course_mapping_error_help'] = 'UNTI course IDs not found';
$string['network_error_help'] = 'Network connection problems';
$string['permission_error_help'] = 'Insufficient access rights';
$string['unknown_error_help'] = 'Unclassified errors';
$string['json_data_help'] = 'Enter valid JSON to send to xAPI. In test mode, data is sent as is, without xAPI schema validation.';

// Practice data sending
$string['sendpractice'] = 'Send practice data';
$string['sendpracticetitle'] = 'Send practice data to UNTI system';
$string['sendpracticedescription'] = 'Send practice completion information to UNTI system for group users';
$string['practiceactivity'] = 'Practice activity';
$string['streaminfo'] = 'Stream information';
$string['usersinfo'] = 'Users information';
$string['sendresults'] = 'Send results';
$string['totalsent'] = 'Total sent';
$string['totalerrors'] = 'Errors';
$string['usersprocessed'] = 'Users processed';
$string['successfulsends'] = 'Successful sends';
$string['failedusers'] = 'Users with errors';
$string['nousersfound'] = 'No users found in group';
$string['noactivityfound'] = 'Activity with specified LRID not found';
$string['sendstarted'] = 'Sending process started...';
$string['backtomain'] = 'Back to main';
$string['parentcourseid'] = 'Parent course ID';
$string['flowid'] = 'Flow ID';
$string['courseid'] = 'Course ID';
$string['groupid'] = 'Group ID';
$string['untiidfound'] = 'UNTI ID found';
$string['untiidnotfound'] = 'UNTI ID not found';
$string['sendingdata'] = 'Sending data';
$string['processingusers'] = 'Processing users';
$string['completedsuccessfully'] = 'Completed successfully';
$string['processingfailed'] = 'Processing completed with errors';
$string['startsending'] = 'Start sending';
$string['nouserswithuntid'] = 'No users with UNTI ID to send';

// Activity data sending
$string['sendactivity'] = 'Send activity data';
$string['sendactivitytitle'] = 'Send activity data to UNTI system';
$string['sendactivitydescription'] = 'Send activity information to UNTI system for group users';
$string['activityinfo'] = 'Activity information';
$string['sendvideo'] = 'Send video statements';

// Statement
$string['statementsendtitle'] = 'Send statement';
$string['statementsendquestion'] = 'A your sure?';

// FD
$string['streamfdlist'] = 'Stream fd list';
$string['fdresultextension'] = 'Result extension';
$string['fdcontextextension'] = 'Context extension';
$string['fdextensionnotfound'] = 'Not found';
$string['fdextensiondeletetitle'] = 'FD Extension delete';
$string['fdextensiondeletequestion'] = 'Really delete extension: {$a}?';
$string['fdextension'] = 'FD Extension';
$string['factdefedit'] = 'Edit Fact Definition';
$string['factdeftitle'] = 'Fact Definition';
$string['fdcontextextensionvalue'] = 'Extension value, use semicolon for array';
$string['fdresultextensionscore'] = 'Score';
$string['fdresultextensionunit'] = 'Unit';
$string['fdresultextensionmin'] = 'Min';
$string['fdresultextensionmax'] = 'Max';
$string['fdresultextensionresultselector'] = 'Best result selector';
$string['fdextensionnotapplicable'] = 'Not applicable';
$string['streamfacts'] = 'Facts';
$string['factstitle'] = 'Student facts';
$string['factcreate'] = 'Create Fact';
$string['factedit'] = 'Edit Fact';
$string['factedittitle'] = 'Edit Fact';
$string['scoreraw'] = 'Score RAW';
$string['scoremin'] = 'Score Min';
$string['scoremax'] = 'Score Max';
$string['scoretarget'] = 'Score target';
$string['success'] = 'Success';
$string['attemptsmax'] = 'Attempts limit';
$string['attemptnum'] = 'Attempt num';
$string['factdeletetitle'] = 'Fact delete';
$string['factdeletequestion'] = 'Are you sure?';
$string['instructoruntiid'] = 'Instructor UNTIID';

//Etc
$string['videostatementsmanagement'] = 'Video statements management';
$string['flowinfo'] = 'Flow information';
$string['flowid'] = 'Flow ID';
$string['streamusers'] = 'Stream users';
$string['nousersinstream'] = 'No users in stream';
$string['userid'] = 'User ID';
$string['viewstatements'] = 'View statements';
$string['videostatements'] = 'Video statements work';
$string['sendstatements'] = 'Send statements';
$string['bulkactions'] = 'Bulk actions';
$string['bulksendstatements'] = 'Bulk send statements';
$string['bulkreport'] = 'Statements report';
$string['backtostreams'] = 'Back to streams';
$string['backtoflow'] = 'Back to flow';
$string['streamnotfound'] = 'Stream with ID "{$a}" not found';
$string['userinfo'] = 'User information';
$string['novideoactivities'] = 'No video activities in this course section';
$string['statements'] = 'Statements';
$string['bulksendallstatements'] = 'Send all statements';
$string['userreport'] = 'User report';
$string['none'] = 'None';
$string['cmid'] = 'Module ID';
$string['records'] = 'Records';
$string['maxprogress'] = 'Max progress';
$string['duration'] = 'Duration';
$string['nodata'] = 'No data';
$string['createprogress'] = 'Create progress';
$string['createprogressdesc'] = 'Creating a new video progress record with random value from 85% to 100%.';
$string['progressrange'] = 'Progress range';
$string['confirmatecreate'] = 'Are you sure you want to create a new video progress record? Progress will be randomly selected from 85% to 100%.';
$string['progresscreated'] = 'Progress created successfully! Progress: {$a->progress}%, Duration: {$a->duration}';
$string['progressalreadyexists'] = 'Progress record already exists for this user and module';
$string['errorcreateprogress'] = 'Error creating progress record';
$string['backtouser'] = 'Back to user';

// New strings for progress deletion
$string['deleteprogress'] = 'Delete progress';
$string['confirmdeleteprogress'] = 'Are you sure you want to delete all progress records for this user and activity?';
$string['confirmdeleteprogressmessage'] = 'You are about to delete {$a->count} progress record(s) for user "{$a->username}" and activity "{$a->activity}". This action cannot be undone.';
$string['confirmdelete'] = 'Confirm deletion';
$string['confirmdeletion'] = 'Deletion confirmation';
$string['progressdeletedsuccessfully'] = 'Progress records deleted successfully! Deleted progress records: {$a->progresscount}, xAPI statements: {$a->statementscount}';
$string['errorprogressdeletion'] = 'Error deleting progress records';
$string['noprogressdata'] = 'No progress data to delete';
$string['progressdata'] = 'Progress data';
$string['timecreated'] = 'Time created';

// Bulk actions
$string['bulkcreateprogress'] = 'Create progress for all videos';
$string['noprogressneeded'] = 'Progress records already exist for all video activities';
$string['activitiestocreate'] = 'Activities to create progress for';
$string['bulkprogresscreated'] = 'Successfully created progress records: {$a->count}';
$string['bulkprogresserrors'] = 'Errors during progress creation:';
$string['errorbulkcreateprogress'] = 'Error during bulk progress creation';
$string['confirmbulkcreation'] = 'Confirm bulk creation';
$string['confirmbulkcreateprogressmessage'] = 'You are about to create progress records for {$a->count} video activities for user "{$a->username}". This action cannot be undone.';
$string['confirmcreate'] = 'Confirm creation';

// Send statements
$string['sendstatements'] = 'Send statements';
$string['confirmsending'] = 'Send confirmation';
$string['confirmsendstatementsmessage'] = 'You are about to send xAPI statements for {$a->count} progress record(s) for user "{$a->username}" and activity "{$a->activity}".';
$string['confirmsend'] = 'Confirm send';
$string['sentcount'] = 'Sent: {$a}';
$string['skippedcount'] = 'Skipped: {$a}';
$string['errorscount'] = 'Errors: {$a}';
$string['totalprocessed'] = 'Total processed: {$a}';
$string['errordetails'] = 'Error details:';
$string['errorsendingstatements'] = 'Error sending statements';

// Bulk send statements
$string['progressdatatosend'] = 'Progress data to send';
$string['confirmbulksending'] = 'Bulk send confirmation';
$string['confirmbulksendstatementsmessage'] = 'You are about to send xAPI statements for {$a->count} progress record(s) for user "{$a->username}".';
$string['errorbulksendingstatements'] = 'Error during bulk sending statements';

// Send grades for stream
$string['sendgradesforstream'] = 'Send grades';
$string['streaminfo'] = 'Stream information';
$string['confirmgradesending'] = 'Confirm grade sending';
$string['confirmgradesendingmessage'] = 'You are about to send xAPI statements for grades of course "{$a->coursename}" (flow {$a->flowid}).';

// Grade statistics
$string['gradestatistics'] = 'Grade statistics';
$string['totalgrades'] = 'Total grades';
$string['gradedstudents'] = 'Graded students';
$string['gradeitems'] = 'Grade items';
$string['willbesent'] = 'Will send {$a->count} grades for {$a->students} students';
$string['error_type'] = 'Error type';
$string['error_message'] = 'Error message';

$string['detailedresults'] = 'Detailed Results';
$string['sent_statements'] = 'Successfully Sent';
$string['skipped_statements'] = 'Skipped (Already Sent)';
$string['reason'] = 'Reason';

// Strings for send_grades.php
$string['selectuserstosendgrades'] = 'Select users to send grades for';
$string['sendselected'] = 'Send to Selected';
$string['sendforallusers'] = 'Send to All Users';
$string['confirmsendforallusers'] = 'Are you sure you want to send grades for ALL users in the list?';
$string['managevideo'] = 'managevideo';

$string['practicediariestitle'] = 'Practice diaries';
$string['practicediaries'] = 'Practice diaries';
$string['student'] = 'Student';
$string['practicediaryfile'] = 'Practice diary file';
$string['practicediarydeletetitle'] = 'Practice diary delete';
$string['practicediarydeletequestion'] = 'Are you sure?';
