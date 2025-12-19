<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\application\log\log_service;
use local_cdo_unti2035bas\application\mapper;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;
use local_cdo_unti2035bas\infrastructure\timedate_service;


class factdef_extension_add_use_case {
    private log_service $logger;
    private timedate_service $timedateservice;
    private factdef_repository $factdefrepo;
    private stream_repository $streamrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        log_service $logger,
        timedate_service $timedateservice,
        factdef_repository $factdefrepo,
        stream_repository $streamrepo,
        fd_schema_vo $fdschema
    ) {
        $this->logger = $logger;
        $this->timedateservice = $timedateservice;
        $this->factdefrepo = $factdefrepo;
        $this->streamrepo = $streamrepo;
        $this->fdschema = $fdschema;
    }

    public function execute(
        int $factdefid,
        string $extensionname,
        bool $notapplicable,
        ?string $textvalue,
        ?string $score,
        ?string $unit,
        ?string $bestresultselector,
        ?string $min,
        ?string $max,
    ): void {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        /** @var int $streamid */
        $streamid = $factdef->streamid;
        if (!$stream = $this->streamrepo->read($streamid)) {
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
            $factdef->add_resultextension(
                $this->fdschema,
                $stream,
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
            $factdef->add_contextextension(
                $this->fdschema,
                $stream,
                $extension,
                $this->timedateservice->now(),
            );
        } else {
            throw new \InvalidArgumentException();
        }
        $this->factdefrepo->save($factdef);
        $this->logger->info(
            "FD Extension created, factdefid: {$factdefid}, extension: {$extension->name}",
            'factdef_entity',
            $factdef->id,
            $factdef->version,
        );
    }
}
