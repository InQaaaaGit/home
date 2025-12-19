<?php
namespace local_cdo_unti2035bas\domain;


class fd_context_extension_schema_vo {
    /** @readonly */
    public string $name;

    /** @readonly */
    public string $description;

    /** @readonly */
    public string $type;

    /** @readonly */
    public ?string $itemtype;

    /**
     * @var ?array<mixed>
     * @readonly
     */
    public ?array $examples;

    /**
     * @var ?array<mixed>
     * @readonly
     */
    public ?array $enum;

    /**
     * @var ?array<mixed>
     * @readonly
     */
    public ?array $itemexamples;

    /**
     * @var ?array<mixed>
     * @readonly
     */
    public ?array $itemenum;


    /**
     * @param ?array<mixed> $examples
     * @param ?array<mixed> $enum
     * @param ?array<mixed> $itemexamples
     * @param ?array<mixed> $itemenum
     */
    public function __construct(
        string $name,
        string $description,
        string $type,
        ?string $itemtype,
        ?array $examples,
        ?array $enum,
        ?array $itemexamples,
        ?array $itemenum
    ) {
        $this->name = $name;
        $this->description = $description;
        if (!in_array($type, ["array", "string", "integer", "number", "boolean"])) {
            throw new \InvalidArgumentException();
        }
        if (!in_array($itemtype, [null, "string", "integer", "number", "boolean"])) {
            throw new \InvalidArgumentException();
        }
        if ($type != "array" && ($itemtype || $itemexamples || $itemenum)) {
            throw new \InvalidArgumentException();
        }
        if ($type == "array" && !$itemtype) {
            throw new \InvalidArgumentException();
        }
        $this->type = $type;
        $this->itemtype = $itemtype;
        $this->examples = $examples;
        $this->enum = $enum;
        $this->itemexamples = $itemexamples;
        $this->itemenum = $itemenum;
    }

    /**
     * @param mixed $value
     */
    public function validate_value($value): bool {
        if ($this->enum) {
            return in_array($value, $this->enum);
        }
        if ($this->type == 'array') {
            if (!is_array($value)) {
                return false;
            }
            if ($this->itemenum) {
                foreach ($value as $valueitem) {
                    if (!in_array($valueitem, $this->itemenum)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
