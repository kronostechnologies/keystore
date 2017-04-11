<?php

namespace Kronos\Tests\Keystore;

use Kronos\Keystore\EncryptionServiceInterface;
use Kronos\Keystore\Exception\EncryptionException;
use Kronos\Keystore\Exception\KeyNotFoundException;
use Kronos\Keystore\Exception\StoreException;
use Kronos\Keystore\Repository\RepositoryInterface;
use Kronos\Keystore\Store;

class StoreTest extends \PHPUnit_Framework_TestCase {
	const KEY = 'key';
	const VALUE = 'value';
	const ENCRYPTED_VALUE = 'encrypted value';

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $repository;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $encryptionService;

	public function setUp() {
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

	public function test_EncryptionServiceAndEncrypt_set_ShoulSetEncryptedValue() {
		$this->givenEncryptionService();
		$this->encryptionService
			->method('encrypt')
			->willReturn(self::ENCRYPTED_VALUE);
		$this->repository
			->expects(self::once())
			->method('set')
			->with(self::KEY, self::ENCRYPTED_VALUE);

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

	public function test_EncryptionServiceAndDecrypt_get_ShouldCallDecrypt() {
		$this->givenEncryptionService();
		$this->givenRepositoryReturnEncryptedValue();
		$this->encryptionService
			->expects(self::once())
			->method('decrypt')
			->with(self::ENCRYPTED_VALUE);

		$this->store->get(self::KEY, true);
	}

	public function test_EncryptionServiceException_get_ShouldThrowEncryptionException() {
		$this->givenEncryptionService();
		$this->encryptionService
			->method('decrypt')
			->willThrowException(new \Exception());
		$this->expectException(EncryptionException::class);

		$this->store->get(self::KEY, true);
	}

	public function test_EncryptionServiceAndDecrypt_get_ShoulReturnDecryptedValue() {
		$this->givenEncryptionService();
		$this->encryptionService
			->method('decrypt')
			->willReturn(self::VALUE);

		$value = $this->store->get(self::KEY, true);

		$this->assertSame(self::VALUE, $value);
	}

	public function test_EncryptionServiceAndDoNotDecrypt_get_ShouldNotDecrypt() {
		$this->givenEncryptionService();
		$this->encryptionService
			->expects(self::never())
			->method('decrypt')
			->with(self::VALUE);

		$this->store->get(self::KEY);
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

	public function test_delete_ShouldCallUnsetOnRepository() {
		$this->repository
			->expects(self::once())
			->method('delete')
			->with(self::KEY)
			->willReturn(self::VALUE);

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

	public function test_exists_ShouldCallGetOnRespository() {
		$this->repository
			->expects(self::once())
			->method('get')
			->with(self::KEY);

		$this->store->has(self::KEY);
	}

	public function test_ValueReturned_exists_ShouldReturnTrue() {
		$this->repository
			->method('get')
			->willReturn(self::VALUE);

		$value = $this->store->has(self::KEY);

		$this->assertTrue($value);
	}

	public function test_KeyNotFoundException_exists_ShouldReturnFalse() {
		$this->repository
			->method('get')
			->willThrowException(new KeyNotFoundException());

		$value = $this->store->has(self::KEY);

		$this->assertFalse($value);
	}

	public function test_Exception_exists_ShouldThrowStoreException() {
		$this->repository
			->method('get')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->has(self::KEY);
	}

	private function givenEncryptionService() {
		$this->encryptionService = $this->createMock(EncryptionServiceInterface::class);
		$this->store->setEncryptionService($this->encryptionService);
	}

	private function givenRepositoryReturnEncryptedValue() {
		$this->repository
			->method('get')
			->willReturn(self::ENCRYPTED_VALUE);
	}
}