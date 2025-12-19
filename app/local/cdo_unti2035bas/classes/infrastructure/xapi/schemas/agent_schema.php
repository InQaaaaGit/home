<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;



class agent_schema {
    /** @readonly */
    public string $objecttype;
    /** @readonly */
    public agent_account_schema $account;

    public function __construct(string $objecttype, agent_account_schema $account) {
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

    public static function from_agentname(string $agentname): self {
        return new agent_schema(
            'Agent',
            new agent_account_schema(
                'https://my.2035.university',
                $agentname,
            )
        );
    }
}
