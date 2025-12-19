<?php
namespace local_cdo_unti2035bas\application\module;

use local_cdo_unti2035bas\domain\module_entity;
use local_cdo_unti2035bas\infrastructure\persistence\module_repository;


class module_read_use_case {
    private module_repository $modulerepo;

    public function __construct(
        module_repository $modulerepo
    ) {
        $this->modulerepo = $modulerepo;
    }

    public function execute(int $moduleid): module_entity {
        $module = $this->modulerepo->read($moduleid);
        if (!$module) {
            throw new \InvalidArgumentException();
        }
        return $module;
    }
}
