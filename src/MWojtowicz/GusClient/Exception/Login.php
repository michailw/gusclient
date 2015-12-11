<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when can't log in into GUS
 * @package MWojtowicz\Exception
 */
class Login extends \Exception
{
    protected $message = 'Can\'t log into GUS';
}