<?php
namespace local_cdo_unti2035bas\infrastructure\mediainfo;


class mediainfo_schema {
    /** @readonly */
    public media_schema $media;

    public function __construct(
        media_schema $media
    ) {
        $this->media = $media;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function validate(array $data): self {
        return new self(
            media_schema::validate($data['media']),
        );
    }
}
