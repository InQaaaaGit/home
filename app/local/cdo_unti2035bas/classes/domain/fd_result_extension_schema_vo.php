<?php
namespace local_cdo_unti2035bas\domain;


class fd_result_extension_schema_vo {
    const ALLOWED_SCHEMAREF_MAP = [
        'ScoreSchema' => 'result_value_score_vo',
        'WeightSchema' => 'result_value_weight_vo',
        'DurationSchema' => 'result_value_duration_vo',
        'YesNoSchema' => 'result_value_yesno_vo',
        'CountSchema' => 'result_value_count_vo',
        'SpeedSchema' => 'result_value_speed_vo',
        'PercentsSchema' => 'result_value_percents_vo',
        'DimensionSchema' => 'result_value_dimension_vo',
        'RankSchema' => 'result_value_rank_vo',
        'TextSchema' => 'result_value_text_vo',
    ];
    /** @readonly */
    public string $name;

    /** @readonly */
    public string $description;

    /** @readonly */
    public string $schemaref;

    public function __construct(string $name, string $description, string $schemaref) {
        $this->name = $name;
        $this->description = $description;
        if (!isset(self::ALLOWED_SCHEMAREF_MAP[$schemaref])) {
            throw new \InvalidArgumentException("Wrong schemaref: {$schemaref}");
        }
        $this->schemaref = $schemaref;
    }
}
