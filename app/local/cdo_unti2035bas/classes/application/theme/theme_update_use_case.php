<?php
namespace local_cdo_unti2035bas\application\theme;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\domain\theme_unti_vo;
use local_cdo_unti2035bas\infrastructure\persistence\theme_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class theme_update_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private theme_repository $themerepo;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        theme_repository $themerepo
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->themerepo = $themerepo;
    }

    public function execute(int $themeid, int $themeuntiid): void {
        $theme = $this->themerepo->read($themeid);
        if (!$theme) {
            throw new \InvalidArgumentException();
        }
        $updated = $theme->set_untidata(
            new theme_unti_vo($themeuntiid),
            $this->timedateservice->now()
        );
        if ($updated) {
            $this->themerepo->save($theme);
            $this->logger->info(
                "Module udated, themeuntiid: {$themeuntiid}",
                'theme_entity',
                $theme->id,
                $theme->version,
            );
        }
    }
}
