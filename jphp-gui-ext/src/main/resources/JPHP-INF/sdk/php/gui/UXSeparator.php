<?php
namespace php\gui;

/**
 * Class UXSeparator
 * @package php\gui
 */
class UXSeparator extends UXControl
{
    /**
     * @var string HORIZONTAL or VERTICAL
     */
    public $orientation = 'HORIZONTAL';

    /**
     * @var string
     */
    public $hAlignment;

    /**
     * @var string
     */
    public $vAlignment;
}