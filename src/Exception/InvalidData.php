<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when input data are not valid
 * @package MWojtowicz\Exception
 */
class InvalidData extends \RuntimeException
{
    protected $message = "Input data are not valid.";
}