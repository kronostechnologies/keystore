<?php

namespace Kronos\Keystore;

class Factory {

	/**
	 * @param RepositoryInterface $repository
	 * @return Store
	 */
	public function createStore(RepositoryInterface $repository) {
		return new Store($repository);
	}

	/**
	 * @param RepositoryInterface $repository
	 * @param EncryptionServiceInterface $encryptionService
	 * @return Store
	 */
	public function createStoreWithEncryption(RepositoryInterface $repository, EncryptionServiceInterface $encryptionService) {
		$store = $this->createStore($repository);
		$store->setEncryptionService($encryptionService);
		return $store;
	}
}