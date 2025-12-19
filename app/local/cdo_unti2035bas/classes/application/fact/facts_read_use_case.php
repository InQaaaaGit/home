<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;


class facts_read_use_case {
    private fact_repository $factrepo;

    public function __construct(
        fact_repository $factrepo
    ) {
        $this->factrepo = $factrepo;
    }

    /**
     * @return array<fact_entity>
     */
    public function execute(int $factdefid, int $actoruntiid): array {
        return $this->factrepo->read_by_factdefid_actoruntiid($factdefid, $actoruntiid);
    }
}
