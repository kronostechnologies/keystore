<?php

namespace Kronos\Keystore\Encryption;

use Kronos\Keystore\Exception\EncryptionException;

interface ServiceInterface {

	/**
	 * @param mixed $value
	 * @return mixed
	 * @throws EncryptionException
	 */
	public function encrypt($value);

	/**
	 * @param mixed $value
	 * @return mixed
	 * @throws EncryptionException
	 */
	public function decrypt($value);
}