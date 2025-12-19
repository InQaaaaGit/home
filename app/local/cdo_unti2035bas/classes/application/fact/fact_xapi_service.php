<?php
namespace local_cdo_unti2035bas\application\fact;

use DateTime;
use local_cdo_unti2035bas\domain\activity_entity;
use local_cdo_unti2035bas\domain\assessment_entity;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\domain\factdef_entity;
use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_activity_fact;
use local_cdo_unti2035bas\infrastructure\xapi\builders\passed_assessment_fact;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class fact_xapi_service {
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
        factdef_entity $factdef,
        fact_entity $fact
    ): statement_schema {
        if (!$factdef->lrid) {
            throw new \InvalidArgumentException('Fact Definition with not lrid');
        }
        if ($activity && $block && $module && $theme) {
            $builder = new passed_activity_fact();
            $builder->with_blocktype($block->type);
            $builder->with_moodlemoduleid($module->moodle->sectionid);
            $builder->with_moodlemodulepos($module->moodle->position);
            $builder->with_moodlethemeid($theme->moodle->sectionid);
            $builder->with_moodleactivityid($activity->moodle->modid);
            $builder->with_instructorname($fact->instructoruntiid ? (string)$fact->instructoruntiid : null);
        } else if ($assessment) {
            $builder = new passed_assessment_fact();
        } else {
            throw new \InvalidArgumentException();
        }
        $builder->with_factdeflrid($factdef->lrid);
        $builder->with_actorname((string)$fact->actoruntiid);
        $builder->with_timestamp(new DateTime("@{$fact->timestamp}"));
        if ($fact->lrid) {
            $builder->with_lrid($fact->lrid);
        }
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_unticourseid($stream->unti->programid);
        $builder->with_untiflowid($stream->unti->flowid);
        $builder->with_result($fact->result);
        $builder->with_fdcontextextensions(array_values($fact->contextextensions));
        $builder->with_fdresultextensions(array_values($fact->resultextensions));

        return $builder->build();
    }
}
