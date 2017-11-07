<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when searched company is not found
 * @package MWojtowicz\Exception
 */
class NotFound extends \RuntimeException
{
    protected $message = 'Searched company has not been found';
}
