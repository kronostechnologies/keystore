<?php

namespace Kronos\Keystore\Encryption;

class FakeService implements ServiceInterface
{
    public function encrypt($value)
    {
        return $value;
    }

    public function decrypt($value)
    {
        return $value;
    }
}
