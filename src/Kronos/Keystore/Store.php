<?php

namespace Kronos\Keystore;

use Kronos\Keystore\Exception\EncryptionException;
use Kronos\Keystore\Exception\KeyNotFoundException;
use Kronos\Keystore\Exception\StoreException;
use Kronos\Keystore\Repository\RepositoryInterface;

class Store {

	/**
	 * @var RepositoryInterface
	 */
	private $repository;

	/**
	 * @var Encryption\ServiceInterface
	 */
	private $encryptionService;

	/**
	 * @var Cache\ServiceInterface
	 */
	private $cacheService;

	/**
	 * Store constructor.
	 * @param RepositoryInterface $repository
	 */
	public function __construct(RepositoryInterface $repository) {
		$this->repository = $repository;
	}

	/**
	 * @param Encryption\ServiceInterface $encryptionService
	 */
	public function setEncryptionService($encryptionService) {
		$this->encryptionService = $encryptionService;
	}

	/**
	 * @param Cache\ServiceInterface $cacheService
	 */
	public function setCacheService($cacheService) {
		$this->cacheService = $cacheService;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param bool $encrypt
	 * @throws EncryptionException
	 * @throws StoreException
	 */
	public function set($key, $value, $encrypt=false) {
		$this->storeInCache($key, $value);

		$storageValue = $this->encrypt($value, $encrypt);

		try {
			$this->repository->set($key, $storageValue);
		}
		catch(\Exception $exception) {
			throw new StoreException('An error occured while setting key/value pair : '.$key, 0, $exception);
		}
	}

	/**
	 * @param $key
	 * @param bool $decrypt
	 * @return mixed
	 * @throws EncryptionException
	 * @throws KeyNotFoundException
	 * @throws StoreException
	 */
	public function get($key, $decrypt=false) {
		if($this->cacheService) {
			try {
				return $this->cacheService->get($key);
			}
			catch(KeyNotFoundException $exception) {
				// Key not in cache, get from repository then
			}
		}

		try {
			$value = $this->decrypt($this->repository->get($key), $decrypt);
			$this->storeInCache($key, $value);
			return $value;
		}
		catch(KeyNotFoundException $exception) {
			throw $exception;
		}
		catch(EncryptionException $exception) {
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

			if($this->cacheService) {
				$this->cacheService->delete($key);
			}
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
	public function has($key) {
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

	/**
	 * @param mixed $value
	 * @param bool $encrypt
	 * @return mixed
	 * @throws EncryptionException
	 */
	private function encrypt($value, $encrypt) {
		if($encrypt) {
			if(!$this->encryptionService) {
				throw new EncryptionException('No encryption service specified');
			}

			try {
				return base64_encode($this->encryptionService->encrypt($value));
			}
			catch(\Exception $exception) {
				throw new EncryptionException('An error occured while encrypting value', 0, $exception);
			}
		}
		else {
			return $value;
		}
	}

	/**
	 * @param mixed $value
	 * @param bool $decrypt
	 * @return mixed
	 * @throws EncryptionException
	 */
	private function decrypt($value, $decrypt) {
		if($decrypt) {
			if(!$this->encryptionService) {
				throw new EncryptionException('No encryption service specified');
			}

			try {
				return $this->encryptionService->decrypt(base64_decode($value));
			}
			catch(\Exception $exception) {
				throw new EncryptionException('An error occured while decrypting value', 0, $exception);
			}
		}
		else {
			return $value;
		}
	}

	/**
	 * @param $key
	 * @param $value
	 */
	private function storeInCache($key, $value) {
		if($this->cacheService) {
			$this->cacheService->set($key, $value);
		}
	}
}