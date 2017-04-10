<?php

namespace Kronos\Keystore;

use Kronos\Keystore\Repository\RepositoryInterface;

class Store {

	/**
	 * @var RepositoryInterface
	 */
	private $repository;

	/**
	 * Store constructor.
	 * @param RepositoryInterface $repository
	 */
	public function __construct(RepositoryInterface $repository) {
		$this->repository = $repository;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$this->repository->set($key, $value);
	}

	public function get($key) {
		return $this->repository->get($key);
	}

	/**
	 * @param string $key
	 */
	public function delete($key) {
		$this->repository->delete($key);
	}

	public function exists($key) {
		return $this->repository->exists($key);
	}
}