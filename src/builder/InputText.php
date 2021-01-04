<?php

namespace Hennig\Builder;

/**
 * Class InputText
 *
 * @package Hennig\Builder
 */
class InputText extends InputCommon
{
    /**
     * Constraint, tamanho aceito em tela
     * @var ?int
     */
    public $maxlength = null;

    /** @var string|string[] */
    public $mask = null;

    /**
     * https://github.com/RobinHerbots/Inputmask
     * Mask to be applied on component.
     * 9 : numeric
     * a : alphabetical
     * * : alphanumeric
     *
     * i.e
     * 99/99/9999
     * aaa-9999
     *
     * @param string|string[] $mask
     */
    public function setMask($mask): InputText
    {
        $this->mask = $mask;
        return $this;
    }
}
