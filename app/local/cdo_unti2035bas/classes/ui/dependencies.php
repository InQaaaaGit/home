<?php
namespace local_cdo_unti2035bas\ui;

use local_cdo_unti2035bas\application\activity\activity_read_use_case;
use local_cdo_unti2035bas\application\activity\activity_render_use_case;
use local_cdo_unti2035bas\application\activity\activity_send_use_case;
use local_cdo_unti2035bas\application\activity\activity_update_use_case;
use local_cdo_unti2035bas\application\activity\activity_xapi_service;
use local_cdo_unti2035bas\application\assessment\assessment_read_use_case;
use local_cdo_unti2035bas\application\assessment\assessment_render_use_case;
use local_cdo_unti2035bas\application\assessment\assessment_send_use_case;
use local_cdo_unti2035bas\application\assessment\assessment_update_use_case;
use local_cdo_unti2035bas\application\assessment\assessment_xapi_service;
use local_cdo_unti2035bas\application\block\block_render_use_case;
use local_cdo_unti2035bas\application\block\block_send_use_case;
use local_cdo_unti2035bas\application\block\block_xapi_service;
use local_cdo_unti2035bas\application\fact\fact_create_use_case;
use local_cdo_unti2035bas\application\fact\fact_delete_use_case;
use local_cdo_unti2035bas\application\fact\fact_extension_add_use_case;
use local_cdo_unti2035bas\application\fact\fact_extension_delete_use_case;
use local_cdo_unti2035bas\application\fact\fact_extensions_list_use_case;
use local_cdo_unti2035bas\application\fact\fact_get_unused_extensions_use_case;
use local_cdo_unti2035bas\application\fact\fact_read_use_case;
use local_cdo_unti2035bas\application\fact\fact_render_use_case;
use local_cdo_unti2035bas\application\fact\fact_send_use_case;
use local_cdo_unti2035bas\application\fact\fact_update_use_case;
use local_cdo_unti2035bas\application\fact\fact_xapi_service;
use local_cdo_unti2035bas\application\fact\facts_filter_data_read_use_case;
use local_cdo_unti2035bas\application\fact\facts_read_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_extension_add_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_extension_delete_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_extensions_list_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_get_unused_extensions_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_read_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_render_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_send_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_update_use_case;
use local_cdo_unti2035bas\application\factdef\factdef_xapi_service;
use local_cdo_unti2035bas\application\log\log_read_use_case;
use local_cdo_unti2035bas\application\module\module_read_use_case;
use local_cdo_unti2035bas\application\module\module_render_use_case;
use local_cdo_unti2035bas\application\module\module_send_use_case;
use local_cdo_unti2035bas\application\module\module_update_use_case;
use local_cdo_unti2035bas\application\module\module_xapi_service;
use local_cdo_unti2035bas\application\practice_diary\practice_diaries_read_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_create_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_delete_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_read_due_students_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_render_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_send_use_case;
use local_cdo_unti2035bas\application\practice_diary\practice_diary_xapi_service;
use local_cdo_unti2035bas\application\s3_test_use_case;
use local_cdo_unti2035bas\application\statement\statement_cancel_use_case;
use local_cdo_unti2035bas\application\statement\statement_download_use_case;
use local_cdo_unti2035bas\application\stream\stream_create_use_case;
use local_cdo_unti2035bas\application\stream\stream_details_use_case;
use local_cdo_unti2035bas\application\stream\stream_fd_add_use_case;
use local_cdo_unti2035bas\application\stream\stream_fd_delete_use_case;
use local_cdo_unti2035bas\application\stream\stream_fd_list_use_case;
use local_cdo_unti2035bas\application\stream\stream_fd_unused_use_case;
use local_cdo_unti2035bas\application\stream\stream_read_use_case;
use local_cdo_unti2035bas\application\stream\stream_render_use_case;
use local_cdo_unti2035bas\application\stream\stream_send_use_case;
use local_cdo_unti2035bas\application\stream\stream_students_read_service;
use local_cdo_unti2035bas\application\stream\stream_sync_use_case;
use local_cdo_unti2035bas\application\stream\stream_update_use_case;
use local_cdo_unti2035bas\application\stream\stream_xapi_service;
use local_cdo_unti2035bas\application\stream\streams_read_use_case;
use local_cdo_unti2035bas\application\theme\theme_read_use_case;
use local_cdo_unti2035bas\application\theme\theme_render_use_case;
use local_cdo_unti2035bas\application\theme\theme_send_use_case;
use local_cdo_unti2035bas\application\theme\theme_update_use_case;
use local_cdo_unti2035bas\application\theme\theme_xapi_service;
use local_cdo_unti2035bas\application\xapi_test_use_case;
use local_cdo_unti2035bas\dependencies_base;


class dependencies extends dependencies_base {
    public function get_log_read_use_case(): log_read_use_case {
        return new log_read_use_case(
            $this->get_log_record_repo(),
        );
    }

    public function get_xapi_test_use_case(): xapi_test_use_case {
        return new xapi_test_use_case(
            $this->get_xapi_client(),
        );
    }

    public function get_s3_test_use_case(): s3_test_use_case {
        return new s3_test_use_case(
            $this->get_s3_client(),
        );
    }

    public function get_stream_xapi_service(): stream_xapi_service {
        return new stream_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_stream_render_use_case(): stream_render_use_case {
        return new stream_render_use_case(
            $this->get_stream_repo(),
            $this->get_stream_xapi_service(),
        );
    }

    public function get_stream_send_use_case(): stream_send_use_case {
        return new stream_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_stream_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_stream_read_use_case(): stream_read_use_case {
        return new stream_read_use_case(
            $this->get_stream_repo(),
        );
    }

    public function get_streams_read_use_case(): streams_read_use_case {
        return new streams_read_use_case(
            $this->get_stream_repo(),
            $this->get_moodle_service(),
        );
    }

    public function get_stream_create_use_case(): stream_create_use_case {
        return new stream_create_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_stream_sync_service(),
            $this->get_stream_fd_sync_service(),
            $this->get_moodle_service(),
        );
    }

    public function get_stream_update_use_case(): stream_update_use_case {
        return new stream_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
        );
    }

    public function get_stream_sync_use_case(): stream_sync_use_case {
        return new stream_sync_use_case(
            $this->get_stream_repo(),
            $this->get_stream_sync_service(),
            $this->get_stream_fd_sync_service(),
        );
    }

    public function get_stream_details_use_case(): stream_details_use_case {
        return new stream_details_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_factdef_repo(),
            $this->get_moodle_service(),
        );
    }

    public function get_stream_fd_list_use_case(): stream_fd_list_use_case {
        return new stream_fd_list_use_case(
            $this->get_stream_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_stream_fd_unused_use_case(): stream_fd_unused_use_case {
        return new stream_fd_unused_use_case(
            $this->get_stream_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_stream_fd_add_use_case(): stream_fd_add_use_case {
        return new stream_fd_add_use_case(
            $this->get_stream_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_stream_fd_delete_use_case(): stream_fd_delete_use_case {
        return new stream_fd_delete_use_case(
            $this->get_stream_repo(),
        );
    }

    public function get_stream_students_read_service(): stream_students_read_service {
        $pluginconfig = $this->get_plugin_config();
        $userfielduntiid = $pluginconfig->unti_user_field;
        return new stream_students_read_service(
            $this->get_log_service(),
            $this->get_moodle_service(),
            $userfielduntiid,
        );
    }

    public function get_block_xapi_service(): block_xapi_service {
        return new block_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_block_render_use_case(): block_render_use_case {
        return new block_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_block_xapi_service(),
        );
    }

    public function get_block_send_use_case(): block_send_use_case {
        return new block_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_block_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_module_xapi_service(): module_xapi_service {
        return new module_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_module_read_use_case(): module_read_use_case {
        return new module_read_use_case(
            $this->get_module_repo(),
        );
    }

    public function get_module_update_use_case(): module_update_use_case {
        return new module_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_module_repo(),
        );
    }

    public function get_module_render_use_case(): module_render_use_case {
        return new module_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_module_xapi_service(),
        );
    }

    public function get_module_send_use_case(): module_send_use_case {
        return new module_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_module_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_theme_xapi_service(): theme_xapi_service {
        return new theme_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_theme_read_use_case(): theme_read_use_case {
        return new theme_read_use_case(
            $this->get_theme_repo(),
        );
    }

    public function get_theme_update_use_case(): theme_update_use_case {
        return new theme_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_theme_repo(),
        );
    }

    public function get_theme_render_use_case(): theme_render_use_case {
        return new theme_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_theme_xapi_service(),
        );
    }

    public function get_theme_send_use_case(): theme_send_use_case {
        return new theme_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_theme_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_activity_xapi_service(): activity_xapi_service {
        return new activity_xapi_service(
            $this->get_moodle_service(),
            $this->get_mediainfo_service(),
        );
    }

    public function get_activity_render_use_case(): activity_render_use_case {
        return new activity_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_activity_xapi_service(),
        );
    }

    public function get_activity_send_use_case(): activity_send_use_case {
        return new activity_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_activity_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_activity_read_use_case(): activity_read_use_case {
        return new activity_read_use_case(
            $this->get_activity_repo(),
        );
    }

    public function get_activity_update_use_case(): activity_update_use_case {
        return new activity_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_activity_repo(),
        );
    }

    public function get_assessment_xapi_service(): assessment_xapi_service {
        return new assessment_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_assessment_render_use_case(): assessment_render_use_case {
        return new assessment_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_assessment_repo(),
            $this->get_assessment_xapi_service(),
        );
    }

    public function get_assessment_send_use_case(): assessment_send_use_case {
        return new assessment_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_assessment_repo(),
            $this->get_assessment_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_assessment_read_use_case(): assessment_read_use_case {
        return new assessment_read_use_case(
            $this->get_assessment_repo(),
        );
    }

    public function get_assessment_update_use_case(): assessment_update_use_case {
        return new assessment_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_assessment_repo(),
        );
    }

    public function get_statement_download_use_case(): statement_download_use_case {
        return new statement_download_use_case(
            $this->get_xapi_client(),
        );
    }

    public function get_statement_cancel_use_case(): statement_cancel_use_case {
        return new statement_cancel_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_moodle_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_practice_diary_xapi_service(): practice_diary_xapi_service {
        return new practice_diary_xapi_service(
            $this->get_timedate_service(),
        );
    }

    public function get_factdef_xapi_service(): factdef_xapi_service {
        return new factdef_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_factdef_render_use_case(): factdef_render_use_case {
        return new factdef_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_factdef_repo(),
            $this->get_factdef_xapi_service(),
        );
    }

    public function get_factdef_send_use_case(): factdef_send_use_case {
        return new factdef_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_factdef_repo(),
            $this->get_factdef_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_factdef_read_use_case(): factdef_read_use_case {
        return new factdef_read_use_case(
            $this->get_factdef_repo(),
        );
    }

    public function get_factdef_update_use_case(): factdef_update_use_case {
        return new factdef_update_use_case(
            $this->get_log_service(),
            $this->get_factdef_repo(),
        );
    }

    public function get_factdef_get_unused_extensions_use_case(): factdef_get_unused_extensions_use_case {
        return new factdef_get_unused_extensions_use_case(
            $this->get_stream_repo(),
            $this->get_factdef_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_factdef_extension_add_use_case(): factdef_extension_add_use_case {
        return new factdef_extension_add_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_factdef_repo(),
            $this->get_stream_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_factdef_extensions_list_use_case(): factdef_extensions_list_use_case {
        return new factdef_extensions_list_use_case(
            $this->get_factdef_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_factdef_extension_delete_use_case(): factdef_extension_delete_use_case {
        return new factdef_extension_delete_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_factdef_repo(),
        );
    }

    public function get_facts_filter_data_read_use_case(): facts_filter_data_read_use_case {
        return new facts_filter_data_read_use_case(
            $this->get_stream_repo(),
            $this->get_factdef_repo(),
            $this->get_stream_students_read_service(),
        );
    }

    public function get_facts_read_use_case(): facts_read_use_case {
        return new facts_read_use_case(
            $this->get_fact_repo(),
        );
    }

    public function get_fact_read_use_case(): fact_read_use_case {
        return new fact_read_use_case(
            $this->get_fact_repo(),
        );
    }

    public function get_fact_create_use_case(): fact_create_use_case {
        return new fact_create_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_factdef_repo(),
            $this->get_fact_repo(),
        );
    }

    public function get_fact_update_use_case(): fact_update_use_case {
        return new fact_update_use_case(
            $this->get_log_service(),
            $this->get_fact_repo(),
        );
    }

    public function get_fact_delete_use_case(): fact_delete_use_case {
        return new fact_delete_use_case(
            $this->get_log_service(),
            $this->get_fact_repo(),
        );
    }

    public function get_fact_xapi_service(): fact_xapi_service {
        return new fact_xapi_service(
            $this->get_moodle_service(),
        );
    }

    public function get_fact_render_use_case(): fact_render_use_case {
        return new fact_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_factdef_repo(),
            $this->get_fact_repo(),
            $this->get_fact_xapi_service(),
        );
    }

    public function get_fact_send_use_case(): fact_send_use_case {
        return new fact_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
            $this->get_activity_repo(),
            $this->get_assessment_repo(),
            $this->get_factdef_repo(),
            $this->get_fact_repo(),
            $this->get_fact_xapi_service(),
            $this->get_xapi_client(),
        );
    }

    public function get_fact_extensions_list_use_case(): fact_extensions_list_use_case {
        return new fact_extensions_list_use_case(
            $this->get_fact_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_fact_get_unused_extensions_use_case(): fact_get_unused_extensions_use_case {
        return new fact_get_unused_extensions_use_case(
            $this->get_stream_repo(),
            $this->get_factdef_repo(),
            $this->get_fact_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_fact_extension_add_use_case(): fact_extension_add_use_case {
        return new fact_extension_add_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_factdef_repo(),
            $this->get_fact_repo(),
            $this->get_fd_schema(),
        );
    }

    public function get_fact_extension_delete_use_case(): fact_extension_delete_use_case {
        return new fact_extension_delete_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_fact_repo(),
        );
    }

    public function get_practice_diaries_read_use_case(): practice_diaries_read_use_case {
        $pluginconfig = $this->get_plugin_config();
        $userfielduntiid = $pluginconfig->unti_user_field;
        return new practice_diaries_read_use_case(
            $this->get_log_service(),
            $this->get_moodle_service(),
            $this->get_stream_repo(),
            $this->get_practice_diary_repo(),
            $userfielduntiid,
        );
    }

    public function get_practice_diary_read_due_students_use_case(): practice_diary_read_due_students_use_case {
        $pluginconfig = $this->get_plugin_config();
        $userfielduntiid = $pluginconfig->unti_user_field;
        return new practice_diary_read_due_students_use_case(
            $this->get_log_service(),
            $this->get_moodle_service(),
            $this->get_stream_repo(),
            $this->get_practice_diary_repo(),
            $userfielduntiid,
        );
    }

    public function get_practice_diary_create_use_case(): practice_diary_create_use_case {
        $pluginconfig = $this->get_plugin_config();
        $s3baseurl = $pluginconfig->s3baseurl;
        $s3bucket = $pluginconfig->s3bucket;
        return new practice_diary_create_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_practice_diary_repo(),
            $this->get_s3_client(),
            $s3baseurl,
            $s3bucket,
        );
    }

    public function get_practice_diary_render_use_case(): practice_diary_render_use_case {
        return new practice_diary_render_use_case(
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_practice_diary_repo(),
            $this->get_practice_diary_xapi_service(),
        );
    }

    public function get_practice_diary_delete_use_case(): practice_diary_delete_use_case {
        return new practice_diary_delete_use_case(
            $this->get_log_service(),
            $this->get_practice_diary_repo(),
        );
    }

    public function get_practice_diary_send_use_case(): practice_diary_send_use_case {
        return new practice_diary_send_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_practice_diary_repo(),
            $this->get_practice_diary_xapi_service(),
            $this->get_xapi_client(),
        );
    }
}
