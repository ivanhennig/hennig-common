<?php

namespace Hennig\Common;

class Config
{
    /**
     * Must be called first to init the project
     *
     * @param array $params
     * @throws \Exception
     */
    static public function init($params = [])
    {
        if (defined('BASE_DIR')) return; //Avoid re-init

        global $argv;
        date_default_timezone_set($params['timezone'] ?? 'America/Sao_Paulo');
        define('BASE_DIR', $params['base_dir'] ?? realpath(__DIR__ . '/../../../../') . '/');
        if (php_sapi_name() === 'cli') {
            define('DEBUG', !empty($argv[1]) && $argv[1] === 'debug');
        } else {
            define('DEBUG', !!filter_input(INPUT_GET, 'debug'));
        }

        self::initLang('pt_BR');
    }

    /**
     * @param string $lang
     */
    static protected function initLang($lang)
    {
        putenv("LANGUAGE=$lang");
        //Default locale
        setlocale(LC_MESSAGES, "C.UTF-8");
        //.po .mo name
        $domain = 'messages';
        bindtextdomain($domain, BASE_DIR . 'lang');
        bind_textdomain_codeset($domain, "UTF-8");
        textdomain($domain);
    }

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
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    static public function env($key, $default = '')
    {
        return defined($key) ? constant($key) : $default;
    }

    /**
     * Get a key from config file or use defautl when not found
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    static public function get($key, $default = '')
    {
        static $return = null;
        if ($return === null) {
//            if (!defined('BASE_DIR')) {
//                throw new EWrongSetting('BASE_DIR');
//            }
            
            if (!\file_exists(BASE_DIR . 'config.json')) {
                return $default;
            }
            
            $return = json_decode(file_get_contents(BASE_DIR . 'config.json'), true);        
        }

        return $return[$key] ?? '' ?: $default;
    }
}
