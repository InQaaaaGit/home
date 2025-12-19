<?php
namespace local_cdo_unti2035bas\infrastructure\mediainfo;


class track_item_schema {
    /** @readonly */
    public string $type;
    /** @readonly */
    public ?string $filesize;
    /** @readonly */
    public ?string $streamsize;
    /** @readonly */
    public ?string $fileextension;
    /** @readonly */
    public ?string $duration;

    public function __construct(
        string $type,
        ?string $filesize,
        ?string $streamsize,
        ?string $fileextension,
        ?string $duration
    ) {
        $this->type = $type;
        $this->filesize = $filesize;
        $this->streamsize = $streamsize;
        $this->fileextension = $fileextension;
        $this->duration = $duration;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function validate(array $data): self {
        return new self(
            $data['@type'],
            $data['FileSize'] ?? null,
            $data['StreamSize'] ?? null,
            $data['FileException'] ?? null,
            $data['Duration'] ?? null,
        );
    }
}
