<?php
namespace local_cdo_unti2035bas\infrastructure\mediainfo;


class media_schema {
    /**
        @var array<track_item_schema>
        @readonly
     */
    public array $track;

    /**
        @param array<track_item_schema> $track
     */
    public function __construct(array $track) {
        $this->track = $track;
    }

    /**
        @param array<string, mixed> $data
        @return self
     */
    public static function validate(array $data): self {
        /** @var array<array<string, mixed>> $trackraw */
        $trackraw = $data['track'];
        return new self(
            array_map(fn($t) => track_item_schema::validate($t), $trackraw),
        );
    }
}
