<?php

namespace Tests\MWojtowicz\GusClient;

use PHPUnit\Framework\TestCase;
use MWojtowicz\GusClient\KrsClient;

class KrsClientTest extends TestCase
{
    /**
     * @var KrsClient $client
     */
    private $client;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder(KrsClient::class)
            ->disableOriginalConstructor()
            ->setMethods([
                "clearInput", "findBy"
            ])
            ->getMock();
    }

    /**
     * @see KrsClient::find()
     */
    public function testFindManyElements()
    {
        $data = ["1234", "4567", "8910"];
        $expectedResult = uniqid();

        $this->client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $this->client
            ->expects($this->once())
            ->method("findBy")
            ->with("Krsy", implode(",", $data))
            ->willReturn($expectedResult);

        $result = $this->client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see KrsClientX::find()
     */
    public function testFindOneElement()
    {
        $data = "1234";
        $expectedResult = uniqid();

        $this->client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $this->client
            ->expects($this->once())
            ->method("findBy")
            ->with("Krs", $data)
            ->willReturn($expectedResult);

        $result = $this->client->find($data);

        $this->assertEquals($expectedResult, $result);
    }
}
