<?php

namespace Hennig\Common;

/**
 * Class ERecordNotFound
 *
 * Use class name to detect and translate
 *
 * @package Hennig\Common
 */
class ERecordNotFound extends ESimple
{
    public function __construct()
    {
        parent::__construct('Registro não encontrado');
    }
}
