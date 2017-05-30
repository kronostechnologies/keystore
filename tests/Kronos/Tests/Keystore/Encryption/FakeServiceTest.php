<?php

namespace Kronos\Tests\Keystore\Encryption;

use Kronos\Keystore\Encryption\FakeService;

class FakeServiceTest extends \PHPUnit_Framework_TestCase {
	const VALUE = 'value';

	/**
	 * @var FakeService
	 */
	private $service;

	public function setUp() {
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