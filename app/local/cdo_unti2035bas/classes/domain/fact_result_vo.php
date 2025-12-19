<?php
namespace local_cdo_unti2035bas\domain;


class fact_result_vo {
    /** @readonly */
    public int $scoreraw;
    /** @readonly */
    public int $scoremin;
    /** @readonly */
    public int $scoremax;
    /** @readonly */
    public float $scorescaled;
    /** @readonly */
    public string $scoretarget;
    /** @readonly */
    public bool $success;
    /** @readonly */
    public string $duration;
    /** @readonly */
    public int $attemptsmax;
    /** @readonly */
    public int $attemptnum;


    public function __construct(
        int $scoreraw,
        int $scoremin,
        int $scoremax,
        string $scoretarget,
        bool $success,
        string $duration,
        int $attemptsmax,
        int $attemptnum
    ) {
        $this->scoreraw = $scoreraw;
        $this->scoremin = $scoremin;
        $this->scoremax = $scoremax;
        $this->scoretarget = $scoretarget;
        $this->success = $success;
        $this->duration = $duration;
        $this->attemptsmax = $attemptsmax;
        $this->attemptnum = $attemptnum;
        if (!in_array($this->scoretarget, ['min', 'max', 'None'])) {
            throw new \InvalidArgumentException('Wrong score target');
        }
        if ($this->scoremin > $this->scoremax) {
            throw new \InvalidArgumentException('Score min > max');
        }
        if ($this->scoreraw < $this->scoremin || $this->scoreraw > $this->scoremax) {
            throw new \InvalidArgumentException('Score out of min..max');
        }
        if (!validator::validate_duration($this->duration)) {
            throw new \InvalidArgumentException("Wrong duration value: {$this->duration}");
        }
        $this->scorescaled = ($this->scoreraw - $this->scoremin) / ($this->scoremax - $this->scoremin);
    }
}
