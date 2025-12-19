<?php
namespace local_cdo_unti2035bas\domain;


class fd_schema_vo {
    /**
     * @var array<string, fd_result_extension_schema_vo>
     * @readonly
     */
    public array $resultexts;

    /**
     * @var array<string, fd_context_extension_schema_vo>
     * @readonly
     */
    public array $contextexts;

    /**
     * @param array<string, fd_result_extension_schema_vo> $resultexts
     * @param array<string, fd_context_extension_schema_vo> $contextexts
     */
    public function __construct(array $resultexts, array $contextexts) {
        foreach ($resultexts as $name => $vo) {
            if ($name != $vo->name) {
                throw new \InvalidArgumentException();
            }
        }
        foreach ($contextexts as $name => $vo) {
            if ($name != $vo->name) {
                throw new \InvalidArgumentException();
            }
        }
        $this->resultexts = $resultexts;
        $this->contextexts = $contextexts;
    }
}
