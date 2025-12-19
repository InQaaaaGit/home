<?php
namespace local_cdo_unti2035bas\exceptions;


class xapi_error extends http_error {
    /** @readonly */
    public int $statuscode;
    /** @readonly */
    public string $text;
    /**
     * @readonly
     * @var array<string, mixed>
     */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        int $statuscode,
        string $text,
        array $data
    ) {
        $this->statuscode = $statuscode;
        $this->text = $text;
        $this->data = $data;
        /** @var string */
        $errorid = $data['errorId'] ?? '';
        /** @var string */
        $message = $data['message'] ?? '';
        parent::__construct(trim("[{$errorid}] {$message}"), $statuscode);
    }
}
