<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\application\mapper;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class fact_extension_add_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private stream_repository $streamrepo;
    private factdef_repository $factdefrepo;
    private fact_repository $factrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        stream_repository $streamrepo,
        factdef_repository $factdefrepo,
        fact_repository $factrepo,
        fd_schema_vo $fdschema
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->factdefrepo = $factdefrepo;
        $this->factrepo = $factrepo;
        $this->streamrepo = $streamrepo;
        $this->fdschema = $fdschema;
    }

    public function execute(
        int $factid,
        string $extensionname,
        bool $notapplicable,
        ?string $textvalue,
        ?string $score,
        ?string $unit,
        ?string $bestresultselector,
        ?string $min,
        ?string $max,
    ): void {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        if (!$factdef = $this->factdefrepo->read($fact->factdefid)) {
            throw new consistency_error();
        }
        if (!$stream = $this->streamrepo->read($fact->streamid)) {
            throw new consistency_error();
        }
        if ($extschema = $this->fdschema->resultexts[$extensionname] ?? null) {
            if (!$notapplicable) {
                $extension = mapper::scalars_to_factdef_result_extension(
                    $extschema,
                    $score,
                    $unit,
                    $bestresultselector,
                    $min,
                    $max,
                );
            } else {
                $extension = new factdef_result_extension_vo($extschema->name, $extschema->schemaref, null);
            }
            $fact->add_resultextension(
                $this->fdschema,
                $stream,
                $factdef,
                $extension,
                $this->timedateservice->now(),
            );
        } else if ($extschema = $this->fdschema->contextexts[$extensionname] ?? null) {
            if (!$notapplicable) {
                if (is_null($textvalue)) {
                    throw new \InvalidArgumentException();
                }
                $extension = mapper::str_to_factdef_context_extension($extschema, $textvalue);
            } else {
                $extension = new factdef_context_extension_vo($extschema->name, null);
            }
            $fact->add_contextextension(
                $this->fdschema,
                $stream,
                $factdef,
                $extension,
                $this->timedateservice->now(),
            );
        } else {
            throw new \InvalidArgumentException();
        }
        $this->factrepo->save($fact);
        $this->logger->info(
            "Fact Extension created, factid: {$factid}, extension: {$extension->name}",
            'fact_entity',
            $fact->id,
            null,
        );
    }
}
