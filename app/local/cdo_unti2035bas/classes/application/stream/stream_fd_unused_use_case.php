<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_fd_unused_use_case {
    private stream_repository $streamrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        stream_repository $streamrepo,
        fd_schema_vo $fdschema
    ) {
        $this->streamrepo = $streamrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return array<array<fd_result_extension_schema_vo>|array<fd_context_extension_schema_vo>>
     */
    public function execute(int $streamid): array {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $resultexts = array_values(
            array_filter($this->fdschema->resultexts, fn($ext) => !in_array($ext->name, $stream->fdextensions))
        );
        $contextexts = array_values(
            array_filter($this->fdschema->contextexts, fn($ext) => !in_array($ext->name, $stream->fdextensions))
        );
        return [$resultexts, $contextexts];
    }
}
