<?php
namespace local_cdo_unti2035bas\infrastructure\moodle;


class moodle_user_dto {
    /** @readonly */
    public int $userid;
    /** @readonly */
    public ?string $untiid;
    /** @readonly */
    public string $fullname;

    public function __construct(
        int $userid,
        ?string $untiid,
        string $fullname
    ) {
        $this->userid = $userid;
        $this->untiid = $untiid;
        $this->fullname = $fullname;
    }
}
