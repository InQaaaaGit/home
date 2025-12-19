<?php
namespace local_cdo_unti2035bas\application\theme;

use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;


class theme_read_use_case {
    private theme_repository $themerepo;

    public function __construct(
        theme_repository $themerepo
    ) {
        $this->themerepo = $themerepo;
    }

    public function execute(int $themeid): theme_entity {
        $theme = $this->themerepo->read($themeid);
        if (!$theme) {
            throw new \InvalidArgumentException();
        }
        return $theme;
    }
}
