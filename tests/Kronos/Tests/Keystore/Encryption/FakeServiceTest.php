<?php

namespace Kronos\Tests\Keystore\Encryption;

use Kronos\Keystore\Encryption\FakeService;
use PHPUnit\Framework\TestCase;

class FakeServiceTest extends TestCase {
	const VALUE = 'value';

	/**
	 * @var FakeService
	 */
	private $service;

	protected function setUp(): void {
		$this->service = new FakeService();
	}

	public function test_encrypt_ShouldReturnValue() {
		$actualValue = $this->service->encrypt(self::VALUE);

		$this->assertEquals(self::VALUE, $actualValue);
	}

	public function test_decrypt_ShouldReturnValue() {
		$actualValue = $this->service->decrypt(self::VALUE);

		$this->assertEquals(self::VALUE, $actualValue);
	}
}
