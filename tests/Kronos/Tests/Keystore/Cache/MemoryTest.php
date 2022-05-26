<?php

namespace Kronos\Tests\Keystore\Cache;

use Kronos\Keystore\Cache\Memory;
use Kronos\Keystore\Exception;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase {
	const KEY = 'key';
	const VALUE = 'value';
	const LAST_VALUE = 'last_value';

	/**
	 * @var Memory
	 */
	private $cache;

    protected function setUp(): void {
		$this->cache = new Memory();
	}

	public function test_EmptyCache_get_ShouldThrowKeyNotFoundException() {
		$this->expectException(Exception\KeyNotFoundException::class);

		$this->cache->get(self::KEY);
	}

	public function test_CachedKeyValue_get_ShouldReturnValue() {
		$this->cache->set(self::KEY, self::VALUE);

		$actualValue = $this->cache->get(self::KEY);

		$this->assertEquals(self::VALUE, $actualValue);
	}

	public function test_KeySetTwice_get_ShouldReturnLastSetValue() {
		$this->cache->set(self::KEY, self::VALUE);
		$this->cache->set(self::KEY, self::LAST_VALUE);

		$actualValue = $this->cache->get(self::KEY);

		$this->assertEquals(self::LAST_VALUE, $actualValue);
	}

	public function test_KeyDeleted_get_ShouldThrowKeyNotFoundException() {
		$this->cache->set(self::KEY, self::VALUE);
		$this->cache->delete(self::KEY);
		$this->expectException(Exception\KeyNotFoundException::class);

		$this->cache->get(self::KEY);
	}
}
