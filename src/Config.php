<?php

namespace Hennig\Common;

class Config
{
    /**
     * Get a key from config file or throws an error when not found
     *
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    static public function getOrError($key)
    {
        $value = static::get($key);
        if (empty($value)) {
            throw new EKeyNotFound($key);
        }

        return $value;
    }

    /**
     * Get a key from config file or use defautl when not found
     *
     * @param string $key
     * @param string $default
     * @return mixed
     * @throws \Exception
     */
    static public function get($key, $default = '')
    {
        static $return = null;
        if ($return === null) {
            if (!defined('BASE_DIR')) {
                throw new EWrongSetting('BASE_DIR');
            }
            
            if (!\file_exists(BASE_DIR . 'config.json')) {
                return $default;
            }
            
            $return = json_decode(file_get_contents(BASE_DIR . 'config.json'), true);        
        }

        return $return[$key] ?: '' ?? $default;
    }
}
