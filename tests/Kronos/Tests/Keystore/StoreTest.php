<?php

namespace Kronos\Tests\Keystore;

use Kronos\Keystore\Encryption\ServiceInterface;
use Kronos\Keystore\Exception\EncryptionException;
use Kronos\Keystore\Exception\KeyNotFoundException;
use Kronos\Keystore\Exception\StoreException;
use Kronos\Keystore\Repository\RepositoryInterface;
use Kronos\Keystore\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase {
	const KEY = 'key';
	const VALUE = 'value';
	const ENCRYPTED_VALUE = 'encrypted value';

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var MockObject
	 */
	private $repository;

	/**
	 * @var MockObject
	 */
	private $encryptionService;

	/**
	 * @var MockObject
	 */
	private $cache;

	protected function setUp(): void {
		$this->repository = $this->createMock(RepositoryInterface::class);

		$this->store = new Store($this->repository);
	}

	public function test_set_ShouldCallSetOnRepository() {
		$this->repository
			->expects(self::once())
			->method('set')
			->with(self::KEY, self::VALUE);

		$this->store->set(self::KEY, self::VALUE);
	}

	public function test_Cache_set_ShouldCallSetOnCache() {
		$this->givenCacheService();
		$this->cache
			->expects(self::once())
			->method('set')
			->with(self::KEY, self::VALUE);

		$this->store->set(self::KEY, self::VALUE);
	}

	public function test_EncryptionServiceAndEncrypt_set_ShouldCallEncrypt() {
		$this->givenEncryptionService();
		$this->encryptionService
			->expects(self::once())
			->method('encrypt')
			->with(self::VALUE);

		$this->store->set(self::KEY, self::VALUE, true);
	}

	public function test_EncryptionServiceException_set_ShouldThrowEncryptionException() {
		$this->givenEncryptionService();
		$this->encryptionService
			->method('encrypt')
			->willThrowException(new \Exception());
		$this->expectException(EncryptionException::class);

		$this->store->set(self::KEY, self::VALUE, true);
	}

	public function test_EncryptionServiceAndEncrypt_set_ShoulSetBase64EncodedEncryptedValue() {
		$this->givenEncryptionService();
		$this->encryptionService
			->method('encrypt')
			->willReturn(self::ENCRYPTED_VALUE);
		$this->repository
			->expects(self::once())
			->method('set')
			->with(self::KEY, base64_encode(self::ENCRYPTED_VALUE));

		$this->store->set(self::KEY, self::VALUE, true);
	}

	public function test_EncryptionServiceAndDoNotEncrypt_set_ShouldNotEncrypt() {
		$this->givenEncryptionService();
		$this->encryptionService
			->expects(self::never())
			->method('encrypt')
			->with(self::VALUE);

		$this->store->set(self::KEY, self::VALUE);
	}

	public function test_NoEncryptionServiceAndEncrypt_set_ShouldThrowEncryptionException() {
		$this->expectException(EncryptionException::class);

		$this->store->set(self::KEY, self::VALUE, true);
	}

	public function test_Exception_set_ShouldThrowStoreException() {
		$this->repository
			->method('set')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->set(self::KEY, self::VALUE);
	}

	public function test_get_ShouldCallGetOnRespositoryAndReturnValue() {
		$this->repository
			->expects(self::once())
			->method('get')
			->with(self::KEY)
			->willReturn(self::VALUE);

		$value = $this->store->get(self::KEY);

		$this->assertSame(self::VALUE, $value);
	}

	public function test_Cache_get_ShouldCallGetOnCacheAndReturnValue() {
		$this->givenCacheService();
		$this->cache
			->expects(self::once())
			->method('get')
			->with(self::KEY)
			->willReturn(self::VALUE);
		$this->repository
			->expects(self::never())
			->method('get');

		$value = $this->store->get(self::KEY);

		$this->assertSame(self::VALUE, $value);
	}

	public function test_KeyNotInCache_get_ShouldCallGetOnRepository() {
		$this->givenCacheService();
		$this->givenKeyNotInCache();
		$this->repository
			->expects(self::once())
			->method('get')
			->with(self::KEY);

		$this->store->get(self::KEY);
	}

	public function test_EncryptionServiceAndDecrypt_get_ShouldCallDecrypt() {
		$this->givenEncryptionService();
		$this->givenRepositoryReturnBase64EncodedEncryptedValue();
		$this->encryptionService
			->expects(self::once())
			->method('decrypt')
			->with(self::ENCRYPTED_VALUE);

		$this->store->get(self::KEY, true);
	}

	public function test_KeyNotInCache_get_ShouldStoreValueInCache() {
		$this->givenCacheService();
		$this->givenKeyNotInCache();
		$this->repository
			->method('get')
			->willReturn(self::VALUE);
		$this->cache
			->expects(self::once())
			->method('set')
			->with(self::KEY, self::VALUE);

		$this->store->get(self::KEY);
	}

	public function test_EncryptionServiceException_get_ShouldThrowEncryptionException() {
		$this->givenEncryptionService();
        	$this->repository
            		->method('get')
            		->willReturn(self::VALUE);
		$this->encryptionService
			->method('decrypt')
			->willThrowException(new \Exception());
		$this->expectException(EncryptionException::class);

		$this->store->get(self::KEY, true);
	}

	public function test_EncryptionServiceAndDecrypt_get_ShoulReturnDecryptedValue() {
		$this->givenEncryptionService();
        	$this->repository
            		->method('get')
            		->willReturn(self::KEY);
		$this->encryptionService
			->method('decrypt')
			->willReturn(self::VALUE);

		$value = $this->store->get(self::KEY, true);

		$this->assertSame(self::VALUE, $value);
	}

	public function test_KeyNotInCacheAndDecrypt_get_ShouldStoreDecryptedValueInCache() {
		$this->givenCacheService();
		$this->givenKeyNotInCache();
		$this->givenEncryptionService();
        	$this->repository
            		->method('get')
            		->willReturn(self::KEY);
		$this->encryptionService
			->method('decrypt')
			->willReturn(self::VALUE);
		$this->cache
			->expects(self::once())
			->method('set')
			->with(self::KEY, self::VALUE);

		$this->store->get(self::KEY, true);
	}

	public function test_EncryptionServiceAndDoNotDecrypt_get_ShouldNotDecrypt() {
		$this->givenEncryptionService();
		$this->encryptionService
			->expects(self::never())
			->method('decrypt');

		$this->store->get(self::KEY);
	}

	public function test_InCacheAndEncryption_get_ShouldNotDecryptValue() {
		$this->givenCacheService();
		$this->givenEncryptionService();
		$this->encryptionService
			->expects(self::never())
			->method('decrypt');

		$this->store->get(self::KEY, true);
	}

	public function test_NoEncryptionServiceAndDecrypt_get_ShouldThrowEncryptionException() {
		$this->expectException(EncryptionException::class);

		$this->store->get(self::KEY, true);
	}

	public function test_KeyNotFoundException_get_ShouldThrowException() {
		$this->repository
			->method('get')
			->willThrowException(new KeyNotFoundException());
		$this->expectException(KeyNotFoundException::class);

		$this->store->get(self::KEY);
	}

	public function test_OtherException_get_ShouldThrowStoreException() {
		$this->repository
			->method('get')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->get(self::KEY);
	}

	public function test_delete_ShouldCallDeleteOnRepository() {
		$this->repository
			->expects(self::once())
			->method('delete')
			->with(self::KEY);

		$this->store->delete(self::KEY);
	}

	public function test_Cache_delete_ShouldCallDeleteOnRepository() {
		$this->givenCacheService();
		$this->cache
			->expects(self::once())
			->method('delete')
			->with(self::KEY);

		$this->store->delete(self::KEY);
	}

	public function test_KeyNotFoundException_delete_ShouldThrowException() {
		$this->repository
			->method('delete')
			->willThrowException(new KeyNotFoundException());
		$this->expectException(KeyNotFoundException::class);

		$this->store->delete(self::KEY);
	}

	public function test_OtherException_delete_ShouldThrowStoreException() {
		$this->repository
			->method('delete')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->delete(self::KEY);
	}

	public function test_has_ShouldCallGetOnRespository() {
		$this->repository
			->expects(self::once())
			->method('get')
			->with(self::KEY);

		$this->store->has(self::KEY);
	}

	public function test_ValueReturned_has_ShouldReturnTrue() {
		$this->repository
			->method('get')
			->willReturn(self::VALUE);

		$value = $this->store->has(self::KEY);

		$this->assertTrue($value);
	}

	public function test_KeyNotFoundException_has_ShouldReturnFalse() {
		$this->repository
			->method('get')
			->willThrowException(new KeyNotFoundException());

		$value = $this->store->has(self::KEY);

		$this->assertFalse($value);
	}

	public function test_Exception_has_ShouldThrowStoreException() {
		$this->repository
			->method('get')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->has(self::KEY);
	}

	private function givenEncryptionService() {
		$this->encryptionService = $this->createMock(ServiceInterface::class);
		$this->store->setEncryptionService($this->encryptionService);
	}

	private function givenRepositoryReturnBase64EncodedEncryptedValue() {
		$this->repository
			->method('get')
			->willReturn(base64_encode(self::ENCRYPTED_VALUE));
	}

	private function givenCacheService() {
		$this->cache = $this->createMock(\Kronos\Keystore\Cache\ServiceInterface::class);
		$this->store->setCacheService($this->cache);
	}

	private function givenKeyNotInCache() {
		$this->cache
			->method('get')
			->willThrowException(new KeyNotFoundException());
	}
}
