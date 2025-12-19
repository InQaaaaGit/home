<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_entity;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;


class factdef extends persistent {
    const TABLE = 'cdo_unti2035bas_factdef';

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function define_properties(): array {
        return [
            'lrid' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'streamid' => [
                'type' => PARAM_INT,
            ],
            'baseobject' => [
                'type' => PARAM_RAW,
                'choices' => ['activity_entity', 'assessment_entity'],
            ],
            'baseobjectid' => [
                'type' => PARAM_INT,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'deleted' => [
                'type' => PARAM_BOOL,
            ],
            'changed' => [
                'type' => PARAM_BOOL,
            ],
            'version' => [
                'type' => PARAM_INT,
            ],
            'timesent' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'resultextensions' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'default' => '[]',
            ],
            'contextextensions' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'default' => '[]',
            ],
            'instructoruntiid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    public static function from_domain(factdef_entity $entity): self {
        $resultextensionsdata = [];
        $contextextensiondata = [];
        foreach ($entity->resultextensions as $ext) {
            if ($ext->value) {
                $value = array_filter([
                    'score' => $ext->value->score,
                    'unit' => $ext->value->unit,
                    'bestresultselector' => $ext->value->bestresultselector,
                    'min' => $ext->value->min,
                    'max' => $ext->value->max,
                ], fn($v) => !is_null($v));
            } else {
                $value = null;
            }
            $resultextensionsdata[] = [
                "name" => $ext->name,
                "schemaref" => $ext->schemaref,
                "value" => $value,
            ];
        }
        foreach ($entity->contextextensions as $ext) {
            $contextextensiondata[] = ['name' => $ext->name, 'value' => $ext->value];
        }
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'streamid' => $entity->streamid,
            'baseobject' => $entity->baseobject,
            'baseobjectid' => $entity->baseobjectid,
            'timestamp' => $entity->timestamp,
            'resultextensions' => json_encode($resultextensionsdata),
            'contextextensions' => json_encode($contextextensiondata),
            'instructoruntiid' => $entity->instructoruntiid,
            'deleted' => $entity->deleted,
            'changed' => $entity->changed,
            'version' => $entity->version,
            'timesent' => $entity->timesent,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): factdef_entity {
        $resultextensionsdata = json_decode($this->get('resultextensions'), true);
        $resultextensions = [];
        foreach ($resultextensionsdata as $extdata) {
            $value = null;
            if (!is_null($extdata['value'])) {
                $valueclassname = fd_result_extension_schema_vo::ALLOWED_SCHEMAREF_MAP[$extdata['schemaref']];
                $valueclassname = "local_cdo_unti2035bas\\domain\\fd_extensions\\{$valueclassname}";
                $value = new $valueclassname(
                    $extdata['value']['score'],
                    $extdata['value']['unit'] ?? null,
                    $extdata['value']['bestresultselector'] ?? null,
                    $extdata['value']['min'] ?? null,
                    $extdata['value']['max'] ?? null,
                );
            }
            $resultextensions[$extdata['name']] = new factdef_result_extension_vo(
                $extdata['name'],
                $extdata['schemaref'],
                $value,
            );
        }
        $contextextensiondata = json_decode($this->get('contextextensions'), true);
        $contextextensions = [];
        foreach ($contextextensiondata as $extdata) {
            $contextextensions[$extdata['name']] = new factdef_context_extension_vo(
                $extdata['name'],
                $extdata['value'],
            );
        }
        return new factdef_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('streamid'),
            $this->get('baseobject'),
            $this->get('baseobjectid'),
            $this->get('timestamp'),
            $resultextensions,
            $contextextensions,
            $this->get('instructoruntiid'),
            $this->get('deleted'),
            $this->get('changed'),
            $this->get('version'),
            $this->get('timesent'),
        );
    }

    public static function get_sql_fields($alias, $prefix = null) {
        global $CFG;
        $fields = [];

        if ($prefix === null) {
            $prefix = str_replace('_', '', static::TABLE) . '_';
        }

        // Get the properties and move ID to the top.
        $properties = static::properties_definition();
        $id = $properties['id'];
        unset($properties['id']);
        $properties = ['id' => $id] + $properties;

        foreach ($properties as $property => $definition) {
            if (in_array($property, ['timecreated', 'timemodified', 'usermodified'])) {
                continue;
            }
            $as = $prefix . $property;
            $fields[] = $alias . '.' . $property . ' AS ' . $as;

            // Warn developers that the query will not always work.
            if ($CFG->debugdeveloper && strlen($as) > 30) {
                throw new coding_exception("The alias '$as' for column '$alias.$property' exceeds 30 characters" .
                    " and will therefore not work across all supported databases.");
            }
        }

        return implode(', ', $fields);
    }
}
