<?php

namespace Hennig\Common;

class Session
{
    /**
     * Initialize Session class
     */
    static public function init()
    {
        session_set_save_handler(new class implements \SessionHandlerInterface
        {
            private $key;
            private $iv;
            public function __construct()
            {
                $this->key = Config::getOrError('app_secretkey');
                $this->iv = substr(md5($this->key), 0, openssl_cipher_iv_length('aes-128-cbc'));
            }

            public function close()
            {
                return true;
            }

            public function destroy($session_id)
            {
                if (headers_sent()) return;
                setcookie($session_id, '', time() - 3600);
                unset($_COOKIE[$session_id]);
            }

            public function gc($maxlifetime)
            {
                return true;
            }

            public function open($save_path, $name)
            {
                return true;
            }

            public function read($session_id)
            {
                if (isset($_COOKIE[$session_id])) {
                    $decoded = \base64_decode($_COOKIE[$session_id]);
                    return \openssl_decrypt($decoded, 'aes-128-cbc', $this->key, $options = OPENSSL_RAW_DATA, $this->iv);
                }

                return '';
            }

            public function write($session_id, $session_data)
            {
                if (headers_sent()) return true;
                $ciphertext = \openssl_encrypt($session_data, 'aes-128-cbc', $this->key, $options = OPENSSL_RAW_DATA, $this->iv);
                setcookie($session_id, \base64_encode($ciphertext));
                return true;
            }
        });

        session_start();
    }

    /**
     * Get data from browsers session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static public function get($key, $default = '')
    {
        return \json_decode($_SESSION[$key] ?? '' ?: \json_encode($default));
    }

    /**
     * Set data into browsers session
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    static public function set($key, $value = '')
    {
        $_SESSION[$key] = \json_encode($value);
    }
}