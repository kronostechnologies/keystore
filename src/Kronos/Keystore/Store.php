<?php

namespace Kronos\Keystore;

use Kronos\Keystore\Exception\KeyNotFoundException;
use Kronos\Keystore\Exception\StoreException;
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
	 * @throws StoreException
	 */
	public function set($key, $value) {
		try {
			$this->repository->set($key, $value);
		}
		catch(\Exception $exception) {
			throw new StoreException('An error occured while setting key/value pair : '.$key, 0, $exception);
		}
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws KeyNotFoundException
	 * @throws StoreException
	 */
	public function get($key) {
		try {
			return $this->repository->get($key);
		}
		catch(KeyNotFoundException $exception) {
			throw $exception;
		}
		catch(\Exception $exception) {
			throw new StoreException('An error occured while getting value : '.$key, 0, $exception);
		}
	}

	/**
	 * @param string $key
	 * @throws KeyNotFoundException
	 * @throws StoreException
	 */
	public function delete($key) {
		try {
			$this->repository->delete($key);
		}
		catch(KeyNotFoundException $exception) {
			throw $exception;
		}
		catch(\Exception $exception) {
			throw new StoreException('An error occured while deleting key : '.$key, 0, $exception);
		}
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws StoreException
	 */
	public function exists($key) {
		try {
			$this->repository->get($key);

			return true;
		}
		catch(KeyNotFoundException $exception) {
			return false;
		}
		catch(\Exception $exception) {
			throw new StoreException('An error occured while verifying if key exists : '.$key, 0, $exception);
		}
	}
}