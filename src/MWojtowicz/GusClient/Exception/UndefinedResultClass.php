<?php

namespace MWojtowicz\GusClient\Exception;

/**
 * Thrown, when result class is undefined
 * @package MWojtowicz\Exception
 */
class UndefinedResultClass extends InvalidData
{
    protected $message = "Passed result class is undefined";
}