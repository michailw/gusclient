<?php

namespace Tests\MWojtowicz\GusClient;

use PHPUnit\Framework\TestCase;
use MWojtowicz\GusClient\Client;

class ClientTest extends TestCase
{
    /**
     * @var Client $client
     */
    private $client;

    private const CLIENT_ID = "TEST_CLIENT";
    private const CLIENT_MODE = "TEST";

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder(Client::class)
            ->setConstructorArgs([
                self::CLIENT_ID, self::CLIENT_MODE
            ])
            ->setMethods([
                "DaneSzukaj", "DanePobierzPelnyRaport", "Zaloguj", "Wyloguj", "GetValue"
            ])
            ->getMockForAbstractClass();
    }

    /**
     * @see Client::setResultClassName()
     */
    public function testSetResultClassName()
    {
        $result = $this->client->setResultClassName(\stdClass::class);

        $this->assertInstanceOf(Client::class, $result);
    }

    /**
     * @see Client::setResultClassName()
     */
    public function testSetResultClassNameNonExistingClass()
    {
        $this->expectException(\LogicException::class);

        $this->client->setResultClassName("suchClassDoesNotExists");
    }

    /**
     * @see Client::logout()
     */
    public function testLogout()
    {

    }

    /**
     * @see Client::getServiceStatus()
     */
    public function testGetServiceStatus()
    {

    }

    /**
     * @see Client::getSessionStatus()
     */
    public function testGetSessionStatus()
    {

    }

    /**
     * @see Client::getApiValue()
     */
    public function testGetApiValue()
    {

    }

    /**
     * @see Client::__doRequest()
     */
    public function testDoRequest()
    {

    }
}
