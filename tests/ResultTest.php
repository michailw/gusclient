<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @see Result::parseBasicData()
     */
    public function testParseBasicData()
    {
        $nodes = new \DOMDocument();
        $nodes->loadXML(file_get_contents(__DIR__ . "/data/Result_testParseBasicData.xml"));

        $result = new Result();
        $result->parseBasicData($nodes->documentElement->childNodes);

        $this->assertEquals("123456789", $result->regon);
        $this->assertEquals("http://123456789", $result->regonLink);
        $this->assertEquals("Name", $result->name);
        $this->assertEquals("Street", $result->street);
        $this->assertEquals("City", $result->city);
        $this->assertEquals("PostalCode", $result->postalCode);
        $this->assertEquals("Commune", $result->commune);
        $this->assertEquals("County", $result->county);
        $this->assertEquals("Voivodeship", $result->voivodeship);
        $this->assertEquals("Type", $result->type);
        $this->assertEquals("5", $result->silosID);
    }

    /**
     * @see Result::parseDetails()
     */
    public function testParseDetails()
    {
        $data = (object) [
            "regon" => 123,
            "nip" => 456,
            "house" => "789",
            "flat" => "012"
        ];

        $result = new Result();
        $result->parseDetails($data);

        $this->assertSame(123, $result->regon);
        $this->assertSame(456, $result->nip);
        $this->assertSame("789", $result->house);
        $this->assertSame("012", $result->flat);
    }

    /**
     * @see Result::getTypeDescriptionByAbbreviation()
     */
    public function testGetTypeDescriptionByAbbreviation()
    {
        $data = [
            ["P", "jednostka prawna"],
            ["F", "jednostka fizyczna (os. fizyczna prowadząca działalność gospodarczą)"],
            ["LP", "jednostka lokalna jednostki prawnej"],
            ["LF", "jednostka lokalna jednostki fizycznej"],
            ["any", ""]
        ];

        $this->assertEquals($data[0][1], Result::getTypeDescriptionByAbbreviation($data[0][0]));
        $this->assertEquals($data[1][1], Result::getTypeDescriptionByAbbreviation($data[1][0]));
        $this->assertEquals($data[2][1], Result::getTypeDescriptionByAbbreviation($data[2][0]));
        $this->assertEquals($data[3][1], Result::getTypeDescriptionByAbbreviation($data[3][0]));
        $this->assertEquals($data[4][1], Result::getTypeDescriptionByAbbreviation($data[4][0]));
    }

    /**
     * @see Result::getSilosDescriptionById()
     */
    public function testGetSilosDescriptionById()
    {
        $data = [
            [1, 'Miejsce prowadzenia działalności CEIDG'],
            [2, 'Miejsce prowadzenia działalności Rolniczej'],
            [3, 'Miejsce prowadzenia działalności pozostałej'],
            [4, 'Miejsce prowadzenia działalności zlikwidowanej w starym systemie KRUPGN'],
            [5, ''],
            [6, 'Miejsce prowadzenia działalności jednostki prawnej'],
            [999, ""]
        ];

        $this->assertEquals($data[0][1], Result::getSilosDescriptionById($data[0][0]));
        $this->assertEquals($data[1][1], Result::getSilosDescriptionById($data[1][0]));
        $this->assertEquals($data[2][1], Result::getSilosDescriptionById($data[2][0]));
        $this->assertEquals($data[3][1], Result::getSilosDescriptionById($data[3][0]));
        $this->assertEquals($data[4][1], Result::getSilosDescriptionById($data[4][0]));
        $this->assertEquals($data[5][1], Result::getSilosDescriptionById($data[5][0]));
        $this->assertEquals($data[6][1], Result::getSilosDescriptionById($data[6][0]));
    }
}
