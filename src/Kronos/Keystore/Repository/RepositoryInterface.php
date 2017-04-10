<?php

namespace Kronos\Keystore\Repository;

interface RepositoryInterface {
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value);

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * @param string $key
	 */
	public function delete($key);

	/**
	 * @param string $key
	 * @return bool
	 */
	public function exists($key);
}