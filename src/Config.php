<?php

namespace Hennig\Common;

class Config
{
    static public function getOrError($k)
    {
        $v = static::get($k);
        if (empty($v)) {
            throw new \Exception("Configuraçao $k está vazia");
        }
        return $v;
    }

    static public function get($k, $def = '')
    {
        $return = json_decode(file_get_contents(BASE_DIR . "config.json"), true);
        return $return[$k] ?? $def;
    }
}
