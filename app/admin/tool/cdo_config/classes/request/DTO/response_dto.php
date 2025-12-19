<?php

namespace tool_cdo_config\request\DTO;

use coding_exception;
use JsonException;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

final class response_dto implements \Iterator, i_iterator
{

    private int $_position = 0;
    private array $_data_array = [];
    private object $_data_object;
    private string $_class;

    /**
     * @param string $class
     * @param mixed $data
     * @return response_dto
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function transform(string $class, $data): response_dto
    {
        $instance = new self();

        $instance->_class = $class;
        if (is_array($data)) {
            foreach ($data as $item) {
                $instance->_data_array[] = $instance->add_data($item);
            }
        } elseif (is_string($data)) {
            $instance->_data_array = [];
            throw new cdo_type_response_exception(
                new \TypeError(
                    get_string('info_not_found', 'tool_cdo_config'),
                    404
                )
            ); // TODO - probably need refactor
        } else {
            $instance->_data_object = $instance->add_data($data);
        }

        return $instance;
    }

    /**
     * @param $data
     * @return base_dto
     * @throws cdo_type_response_exception
     */
    private function add_data($data): base_dto
    {
        try {

            return (new $this->_class())->build($data);
        } catch (\TypeError $e) {
            throw new cdo_type_response_exception($e);
        }
    }

    public function all(): array
    {
        return $this->_data_array;
    }

    public function current(): base_dto
    {
        return $this->_data_array[$this->_position];
    }

    public function next(): void
    {
        ++$this->_position;
    }

    public function key(): int
    {
        return $this->_position;
    }

    public function valid(): bool
    {
        return isset($this->_data_array[$this->_position]);
    }

    public function rewind(): void
    {
        $this->_position = 0;
    }

    public function to_array(): array
    {
        return $this->parse_data($this->_data_object ?? $this->_data_array);
    }

    /**
     * @return string
     * @throws cdo_config_exception
     */
    public function to_json(): string
    {
        try {
            return json_encode($this->to_array(), JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new cdo_config_exception(1012);
        }
    }

    /**
     * @param mixed $items
     * @return array
     */
    private function parse_data($items): array
    {
        $result = [];
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->parse_data($item);
            } else if (is_object($item)) {
                if (isset($item->_data_array) && count($item->_data_array)) {
                    $result[$key] = $this->parse_data($item->_data_array);
                } else if (isset($item->_data_object)) {
                    $result[$key] = $this->parse_data($item->_data_object);
                } else {
                    $result[$key] = $this->parse_data($item);
                }
            } else {
                $result[$key] = $item;
            }
        }
        return $result;
    }
}
