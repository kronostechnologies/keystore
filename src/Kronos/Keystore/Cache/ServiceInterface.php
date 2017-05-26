<?php

namespace Kronos\Keystore\Cache;

use Kronos\Keystore\Exception;

interface ServiceInterface {

	/**
	 * Get cached value for key
	 *
	 * @param string $key
	 * @return mixed
	 * @throws Exception\KeyNotFoundException
	 */
	public function get($key);

	/**
	 * Store key value in cache
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value);

	/**
	 * Remove key from cache
	 *
	 * @param string $key
	 */
	public function delete($key);
}
