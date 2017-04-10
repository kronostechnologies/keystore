<?php

namespace Kronos\Tests\Keystore;

use Kronos\Keystore\Exception\KeyNotFoundException;
use Kronos\Keystore\Exception\StoreException;
use Kronos\Keystore\Repository\RepositoryInterface;
use Kronos\Keystore\Store;

class StoreTest extends \PHPUnit_Framework_TestCase {
	const KEY = 'key';
	const VALUE = 'value';

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $repository;

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

		$this->store->exists(self::KEY);
	}

	public function test_ValueReturned_exists_ShouldReturnTrue() {
		$this->repository
			->method('get')
			->willReturn(self::VALUE);

		$value = $this->store->exists(self::KEY);

		$this->assertTrue($value);
	}

	public function test_KeyNotFoundException_exists_ShouldReturnFalse() {
		$this->repository
			->method('get')
			->willThrowException(new KeyNotFoundException());

		$value = $this->store->exists(self::KEY);

		$this->assertFalse($value);
	}

	public function test_Exception_exists_ShouldThrowStoreException() {
		$this->repository
			->method('get')
			->willThrowException(new \Exception());
		$this->expectException(StoreException::class);

		$this->store->exists(self::KEY);
	}
}