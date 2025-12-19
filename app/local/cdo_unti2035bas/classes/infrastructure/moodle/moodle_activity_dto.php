<?php
namespace local_cdo_unti2035bas\infrastructure\moodle;


class moodle_activity_dto {
    /** @readonly */
    public string $activitytype;
    /** @readonly */
    public int $activityid;
    /** @readonly */
    public string $name;
    /** @readonly */
    public string $intro;
    /** @readonly */
    public int $order;
    /** @readonly */
    public ?moodle_fileinfo_dto $fileinfo;

    public function __construct(
        string $activitytype,
        int $activityid,
        string $name,
        string $intro,
        ?moodle_fileinfo_dto $fileinfo,
        int $order
    ) {
        $this->activitytype = $activitytype;
        $this->activityid = $activityid;
        $this->name = $name;
        $this->intro = $intro;
        $this->fileinfo = $fileinfo;
        $this->order = $order;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function from_array(array $data): self {
        return new self(
            $data['activitytype'],
            $data['activityid'],
            $data['name'],
            $data['intro'],
            $data['fileinfo'],
            $data['order'],
        );
    }
}
