<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when NIP input data are not valid
 * @package MWojtowicz\Exception
 */
class InvalidNip extends InvalidData
{
    protected $message = "Invalid NIP.";
}