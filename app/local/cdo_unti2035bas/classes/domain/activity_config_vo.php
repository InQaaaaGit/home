<?php
namespace local_cdo_unti2035bas\domain;


class activity_config_vo {
    /** @readonly */
    public bool $required;
    /** @readonly */
    public bool $collaborative;
    /** @readonly */
    public float $lectureshours;
    /** @readonly */
    public float $workshopshours;
    /** @readonly */
    public float $independentworkhours;
    /** @readonly */
    public int $resultcomparability;
    /** @readonly */
    public ?string $admittanceform;

    public function __construct(
        bool $required,
        bool $collaborative,
        float $lectureshours,
        float $workshopshours,
        float $independentworkhours,
        int $resultcomparability,
        ?string $admittanceform
    ) {
        $this->required = $required;
        $this->collaborative = $collaborative;
        $this->lectureshours = $lectureshours;
        $this->workshopshours = $workshopshours;
        $this->independentworkhours = $independentworkhours;
        $this->resultcomparability = $resultcomparability;
        if (!validator::validate_activity_admittance_form($admittanceform)) {
            throw new \InvalidArgumentException("Wrong admittanceform: {$admittanceform}");
        }
        $this->admittanceform = $admittanceform;
    }
}
