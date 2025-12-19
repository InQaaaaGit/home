<?php
namespace local_cdo_unti2035bas\application\factdef;

use DateTime;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\factdef_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_activity_factdef;
use local_cdo_unti2035bas\infrastructure\xapi\builders\host_assessment_factdef;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class factdef_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(moodle_service $moodleservice) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(
        stream_entity $stream,
        ?block_entity $block,
        ?module_entity $module,
        ?theme_entity $theme,
        ?activity_entity $activity,
        ?assessment_entity $assessment,
        factdef_entity $factdef
    ): statement_schema {
        if ($activity && $block && $module && $theme) {
            $builder = new host_activity_factdef();
            $builder->with_blocktype($block->type);
            $builder->with_moodlemoduleid($module->moodle->sectionid);
            $builder->with_moodlethemeid($theme->moodle->sectionid);
            $builder->with_moodleactivityid($activity->moodle->modid);
            $builder->with_admittanceform($activity->config->admittanceform);
            $builder->with_instructorname($factdef->instructoruntiid ? (string)$factdef->instructoruntiid : null);
        } else if ($assessment) {
            $builder = new host_assessment_factdef();
            $builder->with_moodleactivityid($assessment->moodle->modid);
            $builder->with_resultcomparability((bool)$assessment->config->resultcomparability);
            if ($assessment->parentobject == 'block_entity' && $block) {
                $builder->with_assessmentlevel('block');
                $builder->with_blocktype($block->type);
            } else if ($assessment->parentobject == 'stream_entity') {
                $builder->with_assessmentlevel('final');
            } else {
                throw new \InvalidArgumentException();
            }
        } else {
            throw new \InvalidArgumentException();
        }
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_timestamp(new DateTime("@{$factdef->timestamp}"));
        if ($factdef->lrid) {
            $builder->with_lrid($factdef->lrid);
        }
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $builder->with_fdcontextextensions(array_values($factdef->contextextensions));
        $builder->with_fdresultextensions(array_values($factdef->resultextensions));

        return $builder->build();
    }
}
