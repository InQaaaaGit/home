<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\domain\factdef_entity;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;


class factdef_read_use_case {
    private factdef_repository $factdefrepo;

    public function __construct(
        factdef_repository $factdefrepo
    ) {
        $this->factdefrepo = $factdefrepo;
    }

    public function execute(int $factdefid): factdef_entity {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        return $factdef;
    }
}
