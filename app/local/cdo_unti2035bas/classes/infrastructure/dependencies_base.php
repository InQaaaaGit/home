<?php

namespace local_cdo_unti2035bas\infrastructure;

use local_cdo_unti2035bas\application\statement\video_activity_use_case;
use local_cdo_unti2035bas\infrastructure\moodle\unti_mapping_service;
use local_cdo_unti2035bas\infrastructure\moodle\user_field_service;
use local_cdo_unti2035bas\infrastructure\moodle\xapi_sent_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\xapi\client;

class dependencies_base {
    private static $instance = null;
    private $unti_mapping_service = null;

    public static function get_instance(): dependencies_base {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Private constructor for singleton
    }

    public function get_video_activity_use_case(): video_activity_use_case
    {
        $endpoint = get_config('local_cdo_unti2035bas', 'xapiendpoint');
        $key = get_config('local_cdo_unti2035bas', 'xapikey');
        $secret = get_config('local_cdo_unti2035bas', 'xapisecret');
        
        $xapi_client = new client($endpoint, $key, $secret);
        return new video_activity_use_case($xapi_client);
    }

    public function get_user_field_service(): user_field_service {
        return new user_field_service();
    }

    public function get_unti_mapping_service(): unti_mapping_service {
        if ($this->unti_mapping_service === null) {
            $stream_repo = new stream_repository();
            $module_repo = new module_repository();
            $this->unti_mapping_service = new unti_mapping_service($stream_repo, $module_repo);
        }
        return $this->unti_mapping_service;
    }

    public function get_xapi_sent_repository(): xapi_sent_repository {
        return new xapi_sent_repository();
    }
} 