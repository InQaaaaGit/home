<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\builders;

use DateTime;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\statement_schema;


abstract class base {
    protected ?string $lrid = null;
    protected string $actorname;
    protected DateTime $timestamp;
    protected string $prefix;
    protected string $uniqid;
    protected int $unticourseid;
    protected int $untiflowid;

    public function with_lrid(string $value): self {
        $this->lrid = $value;
        return $this;
    }

    public function with_actorname(string $value): self {
        $this->actorname = $value;
        return $this;
    }

    public function with_timestamp(DateTime $value): self {
        $this->timestamp = $value;
        return $this;
    }

    public function with_prefix(string $value): self {
        $this->prefix = rtrim($value, '/');
        return $this;
    }

    public function with_uniqid(string $value): self {
        $this->uniqid = $value;
        return $this;
    }

    public function with_unticourseid(int $value): self {
        $this->unticourseid = $value;
        return $this;
    }

    public function with_untiflowid(int $value): self {
        $this->untiflowid = $value;
        return $this;
    }

    abstract public function build(): statement_schema;
}
