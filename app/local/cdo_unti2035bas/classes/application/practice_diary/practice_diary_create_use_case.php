<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\practice_diary_entity;
use local_cdo_unti2035bas\domain\s3_file_vo;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\s3\client as s3_client;
use local_cdo_unti2035bas\infrastructure\timedate_service;
use local_cdo_unti2035bas\utils;

class practice_diary_create_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private practice_diary_repository $practicediaryrepo;
    private s3_client $s3client;
    private string $s3baseurl;
    private string $s3bucket;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        practice_diary_repository $practicediaryrepo,
        s3_client $s3client,
        string $s3baseurl,
        string $s3bucket
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->streamrepo = $streamrepo;
        $this->practicediaryrepo = $practicediaryrepo;
        $this->s3client = $s3client;
        $this->s3baseurl = $s3baseurl;
        $this->s3bucket = $s3bucket;
    }

    public function execute(int $streamid, int $studentuntiid, string $filepath): void {
        if (!$stream = $this->streamrepo->read($streamid)) {
            throw new \InvalidArgumentException();
        }
        if (!$filesize = filesize($filepath)) {
            throw new \Exception('Error on get filesize');
        }
        if (!$sha256 = hash_file('sha256', $filepath)) {
            throw new \Exception('Error on get sha256 hash');
        }
        if (!$mime = mime_content_type($filepath)) {
            throw new \Exception('Error on get mime');
        }
        if (!$extension = utils::mime_to_extension($mime)) {
            throw new \Exception('Error on get extension');
        }
        $remotepath = "{$this->s3bucket}/flow{$stream->unti->flowid}/practice/diary{$studentuntiid}.{$extension}";
        $s3url = "{$this->s3baseurl}/{$remotepath}";
        $filevo = new s3_file_vo(
            $s3url,
            $mime,
            $filesize,
            $sha256,
            null,
        );
        $diary = new practice_diary_entity(
            null,
            null,
            $streamid,
            $studentuntiid,
            $this->timedateservice->now(),
            $filevo,
        );
        $this->s3client->send_file($filepath, $remotepath);
        $diary->set_s3timeupload($this->timedateservice->now());
        $diary = $this->practicediaryrepo->save($diary);
        $this->logger->info(
            "Practice diary created, streamid: {$streamid}, actoruntiid: {$studentuntiid}",
            'practice_diary_entity',
            $diary->id,
        );
    }
}
