<?php

namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\application\statement\video_activity_use_case;
use local_cdo_unti2035bas\application\stream\stream_fd_sync_service;
use local_cdo_unti2035bas\application\stream\stream_sync_service;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\config\fd_schema_service;
use local_cdo_unti2035bas\infrastructure\mediainfo\mediainfo_service;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\moodle\unti_mapping_service;
use local_cdo_unti2035bas\infrastructure\moodle\user_field_service;
use local_cdo_unti2035bas\infrastructure\moodle\xapi_sent_repository;
use local_cdo_unti2035bas\infrastructure\persistence\activity_repository;
use local_cdo_unti2035bas\infrastructure\persistence\assessment_repository;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\log_record_repository;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\s3\client as s3_client;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;
use stdClass;

class dependencies_base {
    private ?timedate_service $timedateservice = null;
    private ?moodle_service $moodleservice = null;
    private ?stdClass $pluginconfig = null;
    private ?xapi_client $xapiclient = null;
    private ?s3_client $s3client = null;
    private ?mediainfo_service $mediainfoservice = null;
    private ?log_record_repository $logrecordrepo = null;
    private ?stream_repository $streamrepo = null;
    private ?block_repository $blockrepo = null;
    private ?module_repository $modulerepo = null;
    private ?theme_repository $themerepo = null;
    private ?activity_repository $activityrepo = null;
    private ?assessment_repository $assessmentrepo = null;
    private ?factdef_repository $factdefrepo = null;
    private ?fact_repository $factrepo = null;
    private ?practice_diary_repository $practicediaryrepo = null;
    private ?log_service $logservice = null;
    private ?stream_sync_service $streamsyncservice = null;
    private ?stream_fd_sync_service $streamfdsyncservice = null;
    private ?video_activity_use_case $videoactivityusecase = null;
    private ?user_field_service $userfieldservice = null;
    private ?unti_mapping_service $untimappingservice = null;
    private ?xapi_sent_repository $xapisentrepo = null;
    private ?fd_schema_vo $fdschema = null;

    public function get_timedate_service(): timedate_service {
        if (!$this->timedateservice) {
            $this->timedateservice = new timedate_service();
        }
        return $this->timedateservice;
    }

    public function get_moodle_service(): moodle_service {
        if (!$this->moodleservice) {
            $this->moodleservice = new moodle_service();
        }
        return $this->moodleservice;
    }

    public function get_xapi_client(): xapi_client {
        if (!$this->xapiclient) {
            $config = $this->get_plugin_config();
            $this->xapiclient = new xapi_client(
                $config->xapiendpoint,
                $config->xapikey,
                $config->xapisecret,
            );
        }
        return $this->xapiclient;
    }

    public function get_s3_client(): s3_client {
        if (!$this->s3client) {
            $config = $this->get_plugin_config();
            $this->s3client = new s3_client(
                $config->s3endpoint,
                $config->s3accesskey,
                $config->s3secretkey,
            );
        }
        return $this->s3client;
    }

    public function get_plugin_config(): stdClass {
        if (!$this->pluginconfig) {
            $moodleservice = $this->get_moodle_service();
            $this->pluginconfig = $moodleservice->get_plugin_config();
        }
        return $this->pluginconfig;
    }

    public function get_mediainfo_service(): mediainfo_service {
        if (!$this->mediainfoservice) {
            $config = $this->get_plugin_config();
            $this->mediainfoservice = new mediainfo_service(
                $config->pathtomediainfo,
            );
        }
        return $this->mediainfoservice;
    }


    public function get_log_record_repo(): log_record_repository {
        if (!$this->logrecordrepo) {
            $this->logrecordrepo = new log_record_repository();
        }
        return $this->logrecordrepo;
    }

    public function get_log_service(): log_service {
        if (!$this->logservice) {
            $this->logservice = new log_service(
                $this->get_timedate_service(),
                $this->get_log_record_repo(),
            );
        }
        return $this->logservice;
    }


    public function get_stream_repo(): stream_repository {
        if (!$this->streamrepo) {
            $this->streamrepo = new stream_repository();
        }
        return $this->streamrepo;
    }

    public function get_block_repo(): block_repository {
        if (!$this->blockrepo) {
            $this->blockrepo = new block_repository();
        }
        return $this->blockrepo;
    }

    public function get_module_repo(): module_repository {
        if (!$this->modulerepo) {
            $this->modulerepo = new module_repository();
        }
        return $this->modulerepo;
    }

    public function get_theme_repo(): theme_repository {
        if (!$this->themerepo) {
            $this->themerepo = new theme_repository();
        }
        return $this->themerepo;
    }

    public function get_activity_repo(): activity_repository {
        if (!$this->activityrepo) {
            $this->activityrepo = new activity_repository();
        }
        return $this->activityrepo;
    }

    public function get_assessment_repo(): assessment_repository {
        if (!$this->assessmentrepo) {
            $this->assessmentrepo = new assessment_repository();
        }
        return $this->assessmentrepo;
    }

    public function get_factdef_repo(): factdef_repository {
        if (!$this->factdefrepo) {
            $this->factdefrepo = new factdef_repository();
        }
        return $this->factdefrepo;
    }

    public function get_fact_repo(): fact_repository {
        if (!$this->factrepo) {
            $this->factrepo = new fact_repository();
        }
        return $this->factrepo;
    }

    public function get_practice_diary_repo(): practice_diary_repository {
        if (!$this->practicediaryrepo) {
            $this->practicediaryrepo = new practice_diary_repository();
        }
        return $this->practicediaryrepo;
    }

    public function get_stream_sync_service(): stream_sync_service {
        if (!$this->streamsyncservice) {
            $this->streamsyncservice = new stream_sync_service(
                $this->get_log_service(),
                $this->get_timedate_service(),
                $this->get_block_repo(),
                $this->get_module_repo(),
                $this->get_theme_repo(),
                $this->get_activity_repo(),
                $this->get_assessment_repo(),
                $this->get_moodle_service(),
            );
        }
        return $this->streamsyncservice;
    }

    public function get_stream_fd_sync_service(): stream_fd_sync_service {
        if (!$this->streamfdsyncservice) {
            $this->streamfdsyncservice = new stream_fd_sync_service(
                $this->get_log_service(),
                $this->get_timedate_service(),
                $this->get_block_repo(),
                $this->get_activity_repo(),
                $this->get_assessment_repo(),
                $this->get_factdef_repo(),
            );
        }
        return $this->streamfdsyncservice;
    }

    public function get_video_activity_use_case(): video_activity_use_case {
        if (!$this->videoactivityusecase) {
            $this->videoactivityusecase = new video_activity_use_case(
                $this->get_xapi_client(),
                $this->get_moodle_service(),
                $this->get_user_field_service()
            );
        }
        return $this->videoactivityusecase;
    }

    public function get_unti_mapping_service(): unti_mapping_service {
        if (!$this->untimappingservice) {
            $this->untimappingservice = new unti_mapping_service(
                $this->get_stream_repo(),
                $this->get_module_repo()
            );
        }
        return $this->untimappingservice;
    }

    public function get_user_field_service(): user_field_service {
        if (!$this->userfieldservice) {
            $this->userfieldservice = new user_field_service();
        }
        return $this->userfieldservice;
    }

    public function get_xapi_sent_repository(): xapi_sent_repository {
        if (!$this->xapisentrepo) {
            $this->xapisentrepo = new xapi_sent_repository();
        }
        return $this->xapisentrepo;
    }

    public function get_fd_schema(): fd_schema_vo {
        global $CFG;
        if (!$this->fdschema) {
            $schemafilepath = join(DIRECTORY_SEPARATOR, [$CFG->dirroot, 'local', 'cdo_unti2035bas', 'config', 'fd.schema.json']);
            $service = new fd_schema_service($schemafilepath);
            $this->fdschema = $service->execute();
        }
        return $this->fdschema;
    }
}
