<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;



class actor_schema {
    /** @readonly */
    public string $objecttype;
    /** @readonly */
    public actor_account_schema $account;

    public function __construct(string $objecttype, actor_account_schema $account) {
        $this->objecttype = $objecttype;
        $this->account = $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return [
            'objectType' => $this->objecttype,
            'account' => $this->account->dump(),
        ];
    }

    public static function from_actorname(string $actorname): self {
        return new actor_schema(
            'Agent',
            new actor_account_schema(
                'https://my.2035.university',
                $actorname,
            )
        );
    }
}
