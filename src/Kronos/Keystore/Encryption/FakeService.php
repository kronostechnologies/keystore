<?php

namespace Kronos\Keystore\Encryption;


use Kronos\Keystore\Exception\EncryptionException;

class FakeService implements ServiceInterface {
	public function encrypt($value) {
		return $value;
	}

	public function decrypt($value) {
		return $value;
	}

}