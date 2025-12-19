<?php
namespace local_cdo_unti2035bas\application;

use local_cdo_unti2035bas\infrastructure\s3\client;


class s3_test_use_case {
    private client $client;

    public function __construct(client $client) {
        $this->client = $client;
    }

    public function execute(): void {
        $this->client->list_buckets();
    }
}
