<?php
namespace local_cdo_unti2035bas\application\stream;

use DateTime;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\infrastructure\moodle\moodle_service;
use local_cdo_unti2035bas\infrastructure\xapi\builders\author_course;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


class stream_xapi_service {
    private moodle_service $moodleservice;

    public function __construct(
        moodle_service $moodleservice
    ) {
        $this->moodleservice = $moodleservice;
    }

    public function execute(stream_entity $stream): statement_schema {
        $builder = new author_course();
        if ($stream->lrid) {
            $builder->with_lrid($stream->lrid);
        }
        $builder->with_timestamp(new DateTime("@{$stream->timestamp}"));
        $builder->with_prefix($this->moodleservice->get_config()->wwwroot);
        $builder->with_actorname((string)$stream->unti->methodistid);
        $builder->with_uniqid($stream->unti->uniqid);
        $builder->with_unticourseid($stream->unti->programid);
        $coursename = $stream->override->name;
        $coursedesc = $stream->override->description;
        if (!$stream->override->ismanual) {
            [$course] = array_values($this->moodleservice->get_courses([$stream->moodle->courseid]));
            $coursename = $coursename ?: $course['fullname'];
            $coursedesc = $coursedesc ?: $course['summary'];
        }
        $builder->with_coursename($coursename);
        $builder->with_coursedescription($coursedesc);
        $builder->with_courseonline($stream->isonline);
        return $builder->build();
    }
}
