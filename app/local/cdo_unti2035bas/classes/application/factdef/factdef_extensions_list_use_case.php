<?php
namespace local_cdo_unti2035bas\application\factdef;

use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\persistence\factdef_repository;


class factdef_extensions_list_use_case {
    private factdef_repository $factdefrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        factdef_repository $factdefrepo,
        fd_schema_vo $fdschema
    ) {
        $this->factdefrepo = $factdefrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(int $factdefid): array {
        if (!$factdef = $this->factdefrepo->read($factdefid)) {
            throw new \InvalidArgumentException();
        }
        $contextrows = [];
        $resultrows = [];
        foreach ($factdef->resultextensions as $ext) {
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
                'factdefid' => $factdefid,
                'name' => $ext->name,
                'title' => $extschema ? $extschema->description : get_string('fdextensionnotfound', 'local_cdo_unti2035bas'),
                'type' => get_string('fdresultextension', 'local_cdo_unti2035bas'),
                'value' => $value,
            ];
        }
        foreach ($factdef->contextextensions as $ext) {
            $extschema = $this->fdschema->contextexts[$ext->name] ?? null;
            if (!is_null($ext->value)) {
                $value = json_encode($ext->value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $value = get_string('fdextensionnotapplicable', 'local_cdo_unti2035bas');
            }
            $contextrows[] = [
                'factdefid' => $factdefid,
                'name' => $ext->name,
                'title' => $extschema ? $extschema->description : get_string('fdextensionnotfound', 'local_cdo_unti2035bas'),
                'type' => get_string('fdcontextextension', 'local_cdo_unti2035bas'),
                'value' => $value,
            ];
        }
        return [...$contextrows, ...$resultrows];
    }
}
