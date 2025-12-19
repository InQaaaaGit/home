<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\block_repository;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class practice_diary_render_use_case {
    private stream_repository $streamrepo;
    private block_repository $blockrepo;
    private practice_diary_repository $practicediaryrepo;
    private practice_diary_xapi_service $practicediaryxapiservice;

    public function __construct(
        stream_repository $streamrepo,
        block_repository $blockrepo,
        practice_diary_repository $practicediaryrepo,
        practice_diary_xapi_service $practicediaryxapiservice
    ) {
        $this->streamrepo = $streamrepo;
        $this->blockrepo = $blockrepo;
        $this->practicediaryrepo = $practicediaryrepo;
        $this->practicediaryxapiservice = $practicediaryxapiservice;
    }

    public function execute(int $diaryid): string {
        if (!$diary = $this->practicediaryrepo->read($diaryid)) {
            throw new \InvalidArgumentException();
        }
        if (!$stream = $this->streamrepo->read($diary->streamid)) {
            throw new consistency_error();
        }
        $blocks = $this->blockrepo->read_by_streamid($diary->streamid);
        $blockspractice = array_values(
            array_filter($blocks, fn($b) => $b->type == 'practical' && $b->deleted == false)
        );
        if (count($blockspractice) != 1) {
            throw new \Exception("practice block not found");
        }
        $block = $blockspractice[0];
        if (!$block->lrid) {
            throw new \Exception("lrid not present in block");
        }
        $statement = $this->practicediaryxapiservice->execute(
            $stream,
            $block,
            $diary,
        );
        return json_encode($statement->dump(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
