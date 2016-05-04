<?php

namespace MWojtowicz\GusClient\Exception;
use MWojtowicz\GusClient\Exception;

/**
 * Thrown, when input data are not valid
 * @package MWojtowicz\Exception
 */
class InvalidData extends Exception
{
    protected $message = "Input data are not valid.";
}