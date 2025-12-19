<?php
namespace local_cdo_unti2035bas\observer;

use local_cdo_unti2035bas\application\moodle\course_update_use_case;
use local_cdo_unti2035bas\application\moodle\section_update_use_case;
use local_cdo_unti2035bas\application\statement\content_activity_use_case;
use local_cdo_unti2035bas\application\statement\grade_activity_use_case;
use local_cdo_unti2035bas\dependencies_base;


class dependencies extends dependencies_base {
    public function get_course_update_use_case(): course_update_use_case {
        return new course_update_use_case(
            $this->get_stream_repo(),
            $this->get_stream_sync_service(),
            $this->get_stream_fd_sync_service(),
        );
    }

    public function get_section_update_use_case(): section_update_use_case {
        return new section_update_use_case(
            $this->get_log_service(),
            $this->get_timedate_service(),
            $this->get_stream_repo(),
            $this->get_block_repo(),
            $this->get_module_repo(),
            $this->get_theme_repo(),
        );
    }

    public function get_content_activity_use_case(): content_activity_use_case {
        return new content_activity_use_case(
            $this->get_xapi_client()
        );
    }

    public function get_grade_activity_use_case(): grade_activity_use_case {
        return new grade_activity_use_case(
            $this->get_xapi_client(),
            $this->get_unti_mapping_service(),
            $this->get_user_field_service(),
            $this->get_xapi_sent_repository()
        );
    }
}
