<?php

namespace Kronos\Keystore;

use Kronos\Keystore\Exception\EncryptionException;

interface EncryptionServiceInterface {

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