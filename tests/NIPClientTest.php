<?php

namespace Tests\MWojtowicz\GusClient;

use PHPUnit\Framework\TestCase;
use MWojtowicz\GusClient\NIPClient;

class NIPClientTest extends TestCase
{
    /**
     * @see NIPClient::find()
     */
    public function testFindManyElements()
    {
        $data = ["1234", "4567", "8910"];
        $expectedResult = uniqid();

        $client = $this
            ->getMockBuilder(NIPClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["clearInput", "validateNip", "findBy"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("validateNip")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("findBy")
            ->with("Nipy", implode(",", $data))
            ->willReturn($expectedResult);

        $result = $client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see KrsClient::find()
     */
    public function testFindOneElement()
    {
        $data = "1234";
        $expectedResult = uniqid();

        $client = $this
            ->getMockBuilder(NIPClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["clearInput", "validateNip", "findBy"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("validateNip")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("findBy")
            ->with("Nip", $data)
            ->willReturn($expectedResult);

        $result = $client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see NIPClient::validateNip()
     */
    public function testValidateNip()
    {
        $data = [
            "4974095477",
            "5213331746",
            "4163608799"
        ];

        $client = $this
            ->getMockBuilder(NIPClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["validateNipItem"])
            ->getMock();

        $client
            ->expects($this->exactly(3))
            ->method("validateNipItem")
            ->willReturn(true);

        $client->validateNip($data);

        $this->assertEquals(3, count($data));
    }

    /**
     * @see NIPClient::validateNipItem()
     */
    public function testValidateNipItem()
    {
        $data = [
            "4974095477",
            "5213331746",
            "4163608799"
        ];

        $client = new NIPClient("TEST");

        foreach ($data as $item) {
            $this->assertTrue($client->validateNipItem($item), "Bad regon: {$item}");
        }
    }

    /**
     * @see NIPClient::validateNipItem()
     */
    public function testValidateNipItemWrongNumbers()
    {
        $data = [
            "4974095476",
            "5213331745",
            "4163608798"
        ];

        $client = new NIPClient("TEST");

        foreach ($data as $item) {
            $this->assertFalse($client->validateNipItem($item), "Bad regon: {$item}");
        }
    }
}
