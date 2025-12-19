<?php
namespace local_cdo_unti2035bas\application;

use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\domain\fd_context_extension_schema_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;
use local_cdo_unti2035bas\utils;


class mapper {
    public static function scalars_to_factdef_result_extension(
        fd_result_extension_schema_vo $schema,
        ?string $score,
        ?string $unit,
        ?string $bestresultselector,
        ?string $min,
        ?string $max
    ): factdef_result_extension_vo {
        $value = $score;
        if ($schema->schemaref == "YesNoSchema") {
            $value = utils::str_to_bool($value);
        }
        $valueclassname = fd_result_extension_schema_vo::ALLOWED_SCHEMAREF_MAP[$schema->schemaref];
        $valueclassname = "local_cdo_unti2035bas\\domain\\fd_extensions\\{$valueclassname}";
        $value = new $valueclassname($value, $unit, $bestresultselector, $min, $max);
        return new factdef_result_extension_vo($schema->name, $schema->schemaref, $value);
    }

    public static function str_to_factdef_context_extension(
        fd_context_extension_schema_vo $schema,
        string $textvalue
    ): factdef_context_extension_vo {
        $value = $textvalue;
        if ($schema->type == 'integer') {
            $value = (int)$value;
        } else if ($schema->type == 'number') {
            $value = utils::str_to_float($value);
        } else if ($schema->type == 'boolean') {
            $value = utils::str_to_bool($value);
        } else if ($schema->type == 'array') {
            $value = array_map(fn($v) => trim($v), explode(';', trim($value, '; ')));
            if ($schema->itemtype == 'integer') {
                $value = array_map(fn($v) => (int)$v, $value);
            } else if ($schema->itemtype == 'number') {
                $value = array_map(fn($v) => utils::str_to_float($v), $value);
            } else if ($schema->itemtype == 'boolean') {
                $value = array_map(fn($v) => utils::str_to_bool($v), $value);
            }
        }
        return new factdef_context_extension_vo($schema->name, $value);
    }
}
