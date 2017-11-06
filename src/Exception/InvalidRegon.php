<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when Regon input data are not valid
 * @package MWojtowicz\Exception
 */
class InvalidRegon extends InvalidData
{
    protected $message = "Invalid Regon.";
}