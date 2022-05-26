<?php

namespace Kronos\Keystore\Cache;

use Kronos\Keystore\Exception;

class Memory implements ServiceInterface {

	private $cache = [];

	/**
	 * @param string $key
	 * @return mixed
	 * @throws Exception\KeyNotFoundException
	 */
	public function get($key) {
		if(isset($this->cache[$key])) {
			return $this->cache[$key];
		}
		else {
			throw new Exception\KeyNotFoundException();
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set($key, $value) {
		$this->cache[$key] = $value;
	}

	/**
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete($key) {
		if(isset($this->cache[$key])) {
			unset($this->cache[$key]);
		}
	}
}