<?php

namespace tool_cdo_config\tools;

use Exception;
use JsonException;
use tool_cdo_config\exceptions\cdo_config_exception;

class cryptographer {

	/**
	 * Метод шифрования
	 *
	 * @var string
	 */
	protected string $method;

	/**
	 * Ключ шифрования
	 *
	 * @var string
	 */
	protected string $key;

	public function __construct($key, $method = 'AES-256-CBC')
	{
		$this->key    = $key;
		$this->method = $method;
	}

	/**
	 * @param $value
	 * @return string
	 * @throws JsonException
	 * @throws cdo_config_exception
	 */
	public function encrypt($value): string {
		try {
			$iv = random_bytes(16);
		} catch (Exception $e) {
			throw new cdo_config_exception(3003);
		}
		$value = openssl_encrypt($value, $this->method, $this->key, 0, $iv);
		$json = json_encode(['iv' => base64_encode($iv), 'value' => $value], JSON_THROW_ON_ERROR);
		return base64_encode($json);
	}

	/**
	 * @param $value
	 * @return false|string
	 * @throws JsonException
	 * @throws cdo_config_exception
	 */
	public function decrypt($value)
	{
		$data = json_decode(base64_decode($value), true, 512, JSON_THROW_ON_ERROR);
		if (!$this->validOptions($data)) {
			throw new cdo_config_exception(3004);
		}
		$iv = base64_decode($data['iv']);
		return openssl_decrypt($data['value'], $this->method, $this->key, 0, $iv);
	}

	/**
	 * @param $options
	 * @return bool
	 */
	protected function validOptions($options): bool {
		if (!is_array($options)) {
			return false;
		}
		return isset($options['iv'], $options['value']);
	}
}
