<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class fact_get_unused_extensions_use_case {
    private stream_repository $streamrepo;
    private factdef_repository $factdefrepo;
    private fact_repository $factrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        stream_repository $streamrepo,
        factdef_repository $factdefrepo,
        fact_repository $factrepo,
        fd_schema_vo $fdschema
    ) {
        $this->streamrepo = $streamrepo;
        $this->factdefrepo = $factdefrepo;
        $this->factrepo = $factrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return array<array<fd_result_extension_schema_vo>|array<fd_context_extension_schema_vo>>
     */
    public function execute(int $factid): array {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        if (!$stream = $this->streamrepo->read($fact->streamid)) {
            throw new consistency_error();
        }
        if (!$factdef = $this->factdefrepo->read($fact->factdefid)) {
            throw new consistency_error();
        }
        $resultexts = [];
        foreach ($this->fdschema->resultexts as $ext) {
            if (!in_array($ext->name, $stream->fdextensions)) {
                continue;
            }
            if (isset($factdef->resultextensions[$ext->name])) {
                continue;
            }
            if (isset($fact->resultextensions[$ext->name])) {
                continue;
            }
            $resultexts[] = $ext;
        }
        $contextexts = [];
        foreach ($this->fdschema->contextexts as $ext) {
            if (!in_array($ext->name, $stream->fdextensions)) {
                continue;
            }
            if (isset($factdef->contextextensions[$ext->name])) {
                continue;
            }
            if (isset($fact->contextextensions[$ext->name])) {
                continue;
            }
            $contextexts[] = $ext;
        }
        return [$resultexts, $contextexts];
    }
}
