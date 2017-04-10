<?php

namespace Kronos\Tests\Keystore;

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

	public function test_get_ShouldCallGetOnRespositoryAndReturnValue() {
		$this->repository
			->expects(self::once())
			->method('get')
			->with(self::KEY)
			->willReturn(self::VALUE);

		$value = $this->store->get(self::KEY);

		$this->assertSame(self::VALUE, $value);
	}

	public function test_delete_ShouldCallUnsetOnRepository() {
		$this->repository
			->expects(self::once())
			->method('delete')
			->with(self::KEY)
			->willReturn(self::VALUE);

		$this->store->delete(self::KEY);
	}

	public function test_exists_ShouldCallExistsOnRespositoryAndReturnValue() {
		$this->repository
			->expects(self::once())
			->method('exists')
			->with(self::KEY)
			->willReturn(true);

		$value = $this->store->exists(self::KEY);

		$this->assertTrue($value);
	}
}