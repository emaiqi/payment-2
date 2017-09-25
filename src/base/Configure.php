<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 15:54
 */

namespace king\payment\base;

class Configure implements \ArrayAccess
{
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function set($key, $value)
    {
        if ($key == '') {
            throw new InvalidArgumentException('Invalid config key.');
        }

        $keys = explode('.', $key);
        switch (count($keys)) {
            case 1:
                $this->config[$key] = $value;
                break;
            case 2:
                $this->config[$keys[0]][$keys[1]] = $value;
                break;
            case 3:
                $this->config[$keys[0]][$keys[1]][$keys[2]] = $value;
                break;
            default:
                throw new InvalidArgumentException('Invalid config key.');
        }

        return $this->config;
    }

    public function get($key = null, $default = null)
    {
        $config = $this->config;

        if (is_null($key)) {
            return $config;
        }

        if (isset($config[$key])) {
            return $config[$key];
        }


        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (!is_array($this->config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }
}