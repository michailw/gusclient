<?php

namespace MWojtowicz\GusClient\Exception;
use MWojtowicz\GusClient\Exception;

/**
 * Thrown, when searched company is not found
 * @package MWojtowicz\Exception
 */
class NotFound extends Exception
{
    protected $message = 'Searched company has not been found';
}