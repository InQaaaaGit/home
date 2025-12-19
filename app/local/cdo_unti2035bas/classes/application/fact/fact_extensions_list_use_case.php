<?php
namespace local_cdo_unti2035bas\application\fact;

use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\persistence\fact_repository;


class fact_extensions_list_use_case {
    private fact_repository $factrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        fact_repository $factrepo,
        fd_schema_vo $fdschema
    ) {
        $this->factrepo = $factrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(int $factid): array {
        if (!$fact = $this->factrepo->read($factid)) {
            throw new \InvalidArgumentException();
        }
        $contextrows = [];
        $resultrows = [];
        foreach ($fact->resultextensions as $ext) {
            $extschema = $this->fdschema->resultexts[$ext->name] ?? null;
            if (!is_null($ext->value)) {
                $value = json_encode(
                    array_filter((array)$ext->value, fn($v) => !is_null($v)),
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                );
            } else {
                $value = get_string('fdextensionnotapplicable', 'local_cdo_unti2035bas');
            }
            $resultrows[] = [
                'factid' => $factid,
                'factdefid' => $fact->factdefid,
                'name' => $ext->name,
                'title' => $extschema ? $extschema->description : get_string('fdextensionnotfound', 'local_cdo_unti2035bas'),
                'type' => get_string('fdresultextension', 'local_cdo_unti2035bas'),
                'value' => $value,
            ];
        }
        foreach ($fact->contextextensions as $ext) {
            $extschema = $this->fdschema->contextexts[$ext->name] ?? null;
            if (!is_null($ext->value)) {
                $value = json_encode($ext->value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $value = get_string('fdextensionnotapplicable', 'local_cdo_unti2035bas');
            }
            $contextrows[] = [
                'factid' => $factid,
                'factdefid' => $fact->factdefid,
                'name' => $ext->name,
                'title' => $extschema ? $extschema->description : get_string('fdextensionnotfound', 'local_cdo_unti2035bas'),
                'type' => get_string('fdcontextextension', 'local_cdo_unti2035bas'),
                'value' => $value,
            ];
        }
        return [...$contextrows, ...$resultrows];
    }
}
