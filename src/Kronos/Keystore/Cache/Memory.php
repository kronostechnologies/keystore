<?php

namespace Kronos\Keystore\Cache;

use Kronos\Keystore\Exception;

class Memory implements ServiceInterface
{
    private array $cache = [];

    /**
     * @param string $key
     * @return mixed
     * @throws Exception\KeyNotFoundException
     */
    public function get($key)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        } else {
            throw new Exception\KeyNotFoundException();
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value): void
    {
        $this->cache[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function delete($key): void
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
        }
    }
}
