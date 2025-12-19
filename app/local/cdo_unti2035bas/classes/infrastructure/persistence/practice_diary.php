<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\practice_diary_entity;
use local_cdo_unti2035bas\domain\s3_file_vo;


class practice_diary extends persistent {
    const TABLE = 'cdo_unti2035bas_diary';

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
            'diaryfiles3url' => [
                'type' => PARAM_RAW,
            ],
            'diaryfilemimetype' => [
                'type' => PARAM_RAW,
            ],
            'diaryfilefilesize' => [
                'type' => PARAM_INT,
            ],
            'diaryfilesha256' => [
                'type' => PARAM_RAW,
            ],
            'diaryfiletimeupload' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    public static function from_domain(practice_diary_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'streamid' => $entity->streamid,
            'actoruntiid' => $entity->actoruntiid,
            'timestamp' => $entity->timestamp,
            'timesent' => $entity->timesent,
            'diaryfiles3url' => $entity->diaryfile->s3url,
            'diaryfilemimetype' => $entity->diaryfile->mimetype,
            'diaryfilefilesize' => $entity->diaryfile->filesize,
            'diaryfilesha256' => $entity->diaryfile->sha256,
            'diaryfiletimeupload' => $entity->diaryfile->timeupload,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): practice_diary_entity {
        return new practice_diary_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('streamid'),
            $this->get('actoruntiid'),
            $this->get('timestamp'),
            new s3_file_vo(
                $this->get('diaryfiles3url'),
                $this->get('diaryfilemimetype'),
                $this->get('diaryfilefilesize'),
                $this->get('diaryfilesha256'),
                $this->get('diaryfiletimeupload'),
            ),
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
