<?php
namespace local_cdo_unti2035bas\application\stream;

use local_cdo_unti2035bas\domain\fd_schema_vo;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;


class stream_fd_list_use_case {
    private stream_repository $streamrepo;
    private fd_schema_vo $fdschema;

    public function __construct(
        stream_repository $streamrepo,
        fd_schema_vo $fdschema
    ) {
        $this->streamrepo = $streamrepo;
        $this->fdschema = $fdschema;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(int $streamid): array {
        $stream = $this->streamrepo->read($streamid);
        if (!$stream) {
            throw new \InvalidArgumentException();
        }
        $contextrows = [];
        $resultrows = [];
        $notfoundrows = [];
        foreach ($stream->fdextensions as $fdname) {
            $row = ['streamid' => $streamid, 'name' => $fdname];
            if (isset($this->fdschema->resultexts[$fdname])) {
                $ext = $this->fdschema->resultexts[$fdname];
                $row['title'] = $ext->description;
                $row['type'] = get_string('fdresultextension', 'local_cdo_unti2035bas');
                $resultrows[] = $row;
            } else if (isset($this->fdschema->contextexts[$fdname])) {
                $ext = $this->fdschema->contextexts[$fdname];
                $row['title'] = $ext->description;
                $row['type'] = get_string('fdcontextextension', 'local_cdo_unti2035bas');
                $contextrows[] = $row;
            } else {
                $row['title'] = '';
                $row['type'] = get_string('fdextensionnotfound', 'local_cdo_unti2035bas');
                $notfoundrows[] = $row;
            }
        }
        return [...$notfoundrows, ...$contextrows, ...$resultrows];
    }
}
