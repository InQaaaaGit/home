<?php
namespace local_cdo_unti2035bas\infrastructure\s3;

use S3;

require_once("{$CFG->dirroot}/local/cdo_unti2035bas/libs/S3.php");


class client {
    private string $endpoint;
    private string $accesskey;
    private string $secretkey;

    public function __construct(string $endpoint, string $accesskey, string $secretkey) {
        $this->endpoint = $endpoint;
        $this->accesskey = $accesskey;
        $this->secretkey = $secretkey;
    }

    public function list_buckets(): array {
        $s3 = new S3($this->accesskey, $this->secretkey, false, $this->endpoint);
        $s3->setExceptions(true);
        return $s3->listbuckets();
    }

    public function send_file(string $filepath, string $remotepath): void {
        $s3 = new S3($this->accesskey, $this->secretkey, false, $this->endpoint);
        $s3->setExceptions(true);
        $s3->putObjectFile(
            $filepath,
            '',
            $remotepath,
            S3::ACL_PUBLIC_READ,
        );
    }

    public function get_info(string $remotepath): array {
        $s3 = new S3($this->accesskey, $this->secretkey, false, $this->endpoint);
        $s3->setExceptions(true);
        $headers = $s3->getObjectInfo('', $remotepath);
        return $headers;
    }
}
