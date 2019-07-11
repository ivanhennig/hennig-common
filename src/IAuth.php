<?php

namespace Hennig\Common;

interface IAuth
{
    /**
     * Return true when there is a logged user
     *
     * @return bool
     */
    static public function check();

    /**
     * @param string $key
     * @return mixed
     */
    static public function get(string $key);

    /**
     * Must return the user id
     * @return mixed
     */
    static public function id();
}