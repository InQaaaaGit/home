<?php
namespace local_cdo_unti2035bas\domain;


class assessment_config_vo {
    public float $lectureshours;
    /** @readonly */
    public float $workshopshours;
    /** @readonly */
    public float $independentworkhours;
    /** @readonly */
    public int $resultcomparability;
    /** @readonly */
    public bool $haspractice;
    /** @readonly */
    public ?string $documenttype;

    public function __construct(
        float $lectureshours,
        float $workshopshours,
        float $independentworkhours,
        int $resultcomparability,
        bool $haspractice,
        ?string $documenttype
    ) {
        $this->lectureshours = $lectureshours;
        $this->workshopshours = $workshopshours;
        $this->independentworkhours = $independentworkhours;
        $this->resultcomparability = $resultcomparability;
        $this->haspractice = $haspractice;
        $this->documenttype = $documenttype;
    }
}
