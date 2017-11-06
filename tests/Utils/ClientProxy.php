<?php

namespace Tests\MWojtowicz\GusClient\Utils;

use MWojtowicz\GusClient\Client;

class ClientProxy extends Client
{
    /**
     * ClientProxy constructor.
     *
     * @param string $userKey
     * @param string $mode
     * @param array $soapOptions
     */
    public function __construct($userKey = "XYZ", $mode = "TEST", array $soapOptions = [])
    {
        parent::__construct($userKey, $mode, $soapOptions);
    }

    /**
     * @inheritdoc
     */
    public function clearInput(&$input)
    {
        parent::clearInput($input);
    }

    /**
     * @inheritdoc
     */
    public function clearItem($item)
    {
        return parent::clearItem($item);
    }

    /**
     * @inheritdoc
     */
    public function findBy($paramName, $paramValue)
    {
        return parent::findBy($paramName, $paramValue);
    }

    /**
     * @inheritdoc
     */
    public function callApi(string $method, array $params)
    {
        return parent::callApi($method, $params);
    }

    /**
     * Fake function for testing Client::callApi()
     * @see Client::callApi()
     * @param mixed $parameter
     * @return mixed
     */
    public static function fakeFunction($parameter)
    {
        return $parameter;
    }
}
