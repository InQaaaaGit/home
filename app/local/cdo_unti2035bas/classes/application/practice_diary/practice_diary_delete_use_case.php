<?php
namespace local_cdo_unti2035bas\application\practice_diary;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\infrastructure\persistence\practice_diary_repository;


class practice_diary_delete_use_case {
    private log_service $logger;
    private practice_diary_repository $practicediaryrepo;

    public function __construct(
        log_service $logger,
        practice_diary_repository $practicediaryrepo
    ) {
        $this->logger = $logger;
        $this->practicediaryrepo = $practicediaryrepo;
    }

    public function execute(int $diaryid): void {
        if (!$diary = $this->practicediaryrepo->read($diaryid)) {
            throw new \InvalidArgumentException();
        }
        if (!$diary->can_delete()) {
            throw new \Exception();
        }
        $this->practicediaryrepo->delete($diaryid);
        $this->logger->info(
            'Practice diary deleted',
            'practice_diary_entity',
            $diary->id,
        );
    }
}
