<?php

namespace Kronos\Keystore;

class Factory {

	/**
	 * @param Repository\RepositoryInterface $repository
	 * @return Store
	 */
	public function createStore(Repository\RepositoryInterface $repository) {
		return new Store($repository);
	}

	/**
	 * @param Repository\RepositoryInterface $repository
	 * @param Encryption\ServiceInterface $encryptionService
	 * @return Store
	 */
	public function createStoreWithEncryption(Repository\RepositoryInterface $repository, Encryption\ServiceInterface $encryptionService) {
		$store = $this->createStore($repository);
		$store->setEncryptionService($encryptionService);
		return $store;
	}

	public function createFakeEncryptionService() {
		return new Encryption\FakeService();
	}
}