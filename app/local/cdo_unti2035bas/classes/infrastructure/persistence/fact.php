<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\domain\fact_result_vo;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\domain\fd_result_extension_schema_vo;



class fact extends persistent {
    const TABLE = 'cdo_unti2035bas_fact';

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
            'factdefid' => [
                'type' => PARAM_INT,
            ],
            'actoruntiid' => [
                'type' => PARAM_INT,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'timesent' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'resultscoreraw' => [
                'type' => PARAM_INT,
            ],
            'resultscoremin' => [
                'type' => PARAM_INT,
            ],
            'resultscoremax' => [
                'type' => PARAM_INT,
            ],
            'resultscoretarget' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'resultsuccess' => [
                'type' => PARAM_BOOL,
            ],
            'resultduration' => [
                'type' => PARAM_RAW,
            ],
            'resultattemptsmax' => [
                'type' => PARAM_INT,
            ],
            'resultattemptnum' => [
                'type' => PARAM_INT,
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

    public static function from_domain(fact_entity $entity): self {
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
            'factdefid' => $entity->factdefid,
            'actoruntiid' => $entity->actoruntiid,
            'timestamp' => $entity->timestamp,
            'timesent' => $entity->timesent,
            'resultscoreraw' => $entity->result->scoreraw,
            'resultscoremin' => $entity->result->scoremin,
            'resultscoremax' => $entity->result->scoremax,
            'resultscoretarget' => $entity->result->scoretarget,
            'resultsuccess' => $entity->result->success,
            'resultduration' => $entity->result->duration,
            'resultattemptsmax' => $entity->result->attemptsmax,
            'resultattemptnum' => $entity->result->attemptnum,
            'resultextensions' => json_encode($resultextensionsdata),
            'contextextensions' => json_encode($contextextensiondata),
            'instructoruntiid' => $entity->instructoruntiid,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): fact_entity {
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
        return new fact_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('streamid'),
            $this->get('factdefid'),
            $this->get('actoruntiid'),
            $this->get('timestamp'),
            new fact_result_vo(
                $this->get('resultscoreraw'),
                $this->get('resultscoremin'),
                $this->get('resultscoremax'),
                $this->get('resultscoretarget'),
                $this->get('resultsuccess'),
                $this->get('resultduration'),
                $this->get('resultattemptsmax'),
                $this->get('resultattemptnum'),
            ),
            $resultextensions,
            $contextextensions,
            $this->get('instructoruntiid'),
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
