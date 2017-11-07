<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Exception\InvalidRegon;
use PHPUnit\Framework\TestCase;
use MWojtowicz\GusClient\RegonClient;

class RegonClientTest extends TestCase
{
    /**
     * @see RegonClient::find()
     */
    public function testFindManyElementsLongNumbers()
    {
        $data = ["1234", "4567", "8910"];
        $expectedResult = uniqid();

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["clearInput", "validateRegon", "findBy"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("validateRegon")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("findBy")
            ->with("Regony14zn", implode(",", $data))
            ->willReturn($expectedResult);

        $result = $client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see RegonClient::find()
     */
    public function testFindManyElementsShortNumbers()
    {
        $data = ["123456789", "987654321"];
        $expectedResult = uniqid();

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["clearInput", "validateRegon", "findBy"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("validateRegon")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("findBy")
            ->with("Regony9zn", implode(",", $data))
            ->willReturn($expectedResult);

        $result = $client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see RegonClient::find()
     */
    public function testFindOneElement()
    {
        $data = "1234";
        $expectedResult = uniqid();

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["clearInput", "validateRegon", "findBy"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("clearInput")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("validateRegon")
            ->with($data);

        $client
            ->expects($this->once())
            ->method("findBy")
            ->with("Regon", $data)
            ->willReturn($expectedResult);

        $result = $client->find($data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @see RegonClient::validateRegon()
     */
    public function testValidateRegon()
    {
        $data = [
            "919566776",
            "291839056",
            "834951698"
        ];

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["validateRegonItem"])
            ->getMock();

        $client
            ->expects($this->exactly(3))
            ->method("validateRegonItem")
            ->willReturn(true);

        $client->validateRegon($data);

        $this->assertEquals(3, count($data));
    }

    /**
     * @see RegonClient::validateRegon()
     */
    public function testValidateRegonWithEmptyData()
    {
        $this->expectException(InvalidRegon::class);

        $data = [];

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["validateRegonItem"])
            ->getMock();

        $client
            ->expects($this->never())
            ->method("validateRegonItem");

        $client->validateRegon($data);
    }

    /**
     * @see RegonClient::validateRegon()
     */
    public function testValidateRegonWithInvalidDataArray()
    {
        $this->expectException(InvalidRegon::class);

        $regon = "12345678512345";
        $data = [$regon];

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["validateRegonItem"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("validateRegonItem")
            ->with($regon)
            ->willReturn(false);

        $client->validateRegon($data);
    }

    /**
     * @see RegonClient::validateRegon()
     */
    public function testValidateRegonWithInvalidDataString()
    {
        $this->expectException(InvalidRegon::class);

        $regon = "12345678512345";

        $client = $this
            ->getMockBuilder(RegonClient::class)
            ->disableOriginalConstructor()
            ->setMethods(["validateRegonItem"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("validateRegonItem")
            ->with($regon)
            ->willReturn(false);

        $client->validateRegon($regon);
    }

    /**
     * @see RegonClient::validateRegonItem()
     */
    public function testValidateRegonItem()
    {
        $data = [
            "919566776",
            "291839056",
            "834951698"
        ];

        $client = new RegonClient("TEST");

        foreach ($data as $item) {
            $this->assertTrue($client->validateRegonItem($item), "Bad regon: {$item}");
        }
    }

    /**
     * @see RegonClient::validateRegonItem()
     */
    public function testValidateRegonItemWrongNumbers()
    {
        $data = [
            "919566775",
            "291839055",
            "834951697"
        ];

        $client = new RegonClient("TEST");

        foreach ($data as $item) {
            $this->assertFalse($client->validateRegonItem($item), "Bad regon: {$item}");
        }
    }

    /**
     * @see RegonClient::validateRegonItem()
     */
    public function testValidateRegonItem14Digits()
    {
        $data = [
            "12345678512347",
        ];

        $client = new RegonClient("TEST");

        foreach ($data as $item) {
            $this->assertTrue($client->validateRegonItem($item), "Bad regon: {$item}");
        }
    }

    /**
     * @see RegonClient::validateRegonItem()
     */
    public function testValidateNipItemWrongLength()
    {
        $data = "1234";

        $client = new RegonClient("TEST");

        $this->assertFalse($client->validateRegonItem($data));
    }
}
