<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\agent_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\object_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\verb_schema;


class connection_check extends base {
    public function build(): statement_schema {
        return new statement_schema(
            null,
            $this->timestamp,
            agent_schema::from_agentname('0'),
            verb_schema::from_verbid('https://api.2035.university/connection.check'),
            new object_schema(
                'https://api.2035.university/connection.check',
                'Activity',
                null,
            ),
            null,
        );
    }
}
