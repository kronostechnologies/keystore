<?php

namespace Kronos\Keystore\Repository;

use Kronos\Keystore\Exception\KeyNotFoundException;

interface RepositoryInterface {
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value);

	/**
	 * @param string $key
	 * @return mixed
	 * @throws KeyNotFoundException
	 */
	public function get($key);

	/**
	 * @param string $key
	 * @throws KeyNotFoundException
	 */
	public function delete($key);
}