<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;


class fact_read_use_case {
    private fact_repository $factrepo;

    public function __construct(
        fact_repository $factrepo
    ) {
        $this->factrepo = $factrepo;
    }

    public function execute(int $factid): fact_entity {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        return $fact;
    }
}
