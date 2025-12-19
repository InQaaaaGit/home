<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\exceptions\consistency_error;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class factdef_get_unused_extensions_use_case {
    private stream_repository $streamrepo;
    private factdef_repository $factdefrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        stream_repository $streamrepo,
        factdef_repository $factdefrepo,
        fd_schema_vo $fdschema
    ) {
        $this->streamrepo = $streamrepo;
        $this->factdefrepo = $factdefrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return array<array<fd_result_extension_schema_vo>|array<fd_context_extension_schema_vo>>
     */
    public function execute(int $factdefid): array {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        /** @var int $streamid */
        $streamid = $factdef->streamid;
        if (!$stream = $this->streamrepo->read($streamid)) {
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
            $contextexts[] = $ext;
        }
        return [$resultexts, $contextexts];
    }
}
