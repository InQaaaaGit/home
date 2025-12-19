<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;



class actor_account_schema {
    /** @readonly */
    public string $homepage;
    /** @readonly */
    public string $name;

    public function __construct(string $homepage, string $name) {
        $this->homepage = $homepage;
        $this->name = $name;
    }

    /**
     * @return array<string, string>
     */
    public function dump(): array {
        return [
            'homePage' => $this->homepage,
            'name' => $this->name,
        ];
    }
}
