<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Constants;
use MWojtowicz\GusClient\KrsClient;
use MWojtowicz\GusClient\NIPClient;
use MWojtowicz\GusClient\RegonClient;
use MWojtowicz\GusClient\Result;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    /**
     * @var \DOMXPath
     */
    public static $testData;

    public static function setUpBeforeClass()
    {
        $document = new \DOMDocument();
        $document->loadXML(file_get_contents(__DIR__ . "/data/integrationData.xml"));
        self::$testData = new \DOMXPath($document);
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        if (getenv("GUSAPI_KEY") == "") {
            $this->markTestIncomplete("Please set environment variable GUSAPI_KEY to perform this test");
        }
    }

    /**
     * @see NIPClient::find()
     */
    public function testNipClient_oneItem()
    {
        $input = self::$testData->evaluate("//onet/nip/text()")->item(0)->nodeValue;
        $name = self::$testData->evaluate("//onet/name/text()")->item(0)->nodeValue;
        $regon = self::$testData->evaluate("//onet/regon/text()")->item(0)->nodeValue;

        $client = new NIPClient("", Constants::MODE_PRODUCTION);
        /**
         * @var Result $result
         */
        $result = $client->find($input);

        $this->assertEquals($result->nip, $input);
        $this->assertEquals($result->name, $name);
        $this->assertEquals($result->regon, $regon);
    }

    /**
     * @see NIPClient::find()
     */
    public function testNipClient_manyItems()
    {
        $input1 = self::$testData->evaluate("//onet/nip/text()")->item(0)->nodeValue;
        $name1 = self::$testData->evaluate("//onet/name/text()")->item(0)->nodeValue;
        $regon1 = self::$testData->evaluate("//onet/regon/text()")->item(0)->nodeValue;

        $input2 = self::$testData->evaluate("//interia/nip/text()")->item(0)->nodeValue;
        $name2 = self::$testData->evaluate("//interia/name/text()")->item(0)->nodeValue;
        $regon2 = self::$testData->evaluate("//interia/regon/text()")->item(0)->nodeValue;

        $client = new NIPClient("", Constants::MODE_PRODUCTION);
        /**
         * @var Result[] $result
         */
        $result = $client->find([$input1, $input2]);

        $this->assertArrayHasKey("0", $result);
        $this->assertInstanceOf(Result::class, $result[0]);
        $this->assertEquals($result[0]->nip, $input1);
        $this->assertEquals($result[0]->name, $name1);
        $this->assertEquals($result[0]->regon, $regon1);

        $this->assertArrayHasKey("1", $result);
        $this->assertInstanceOf(Result::class, $result[1]);
        $this->assertEquals($result[1]->nip, $input2);
        $this->assertEquals($result[1]->name, $name2);
        $this->assertEquals($result[1]->regon, $regon2);
    }

    /**
     * @see RegonClient::find()
     */
    public function testRegonClient_oneItem()
    {
        $input = self::$testData->evaluate("//onet/regon/text()")->item(0)->nodeValue;
        $name = self::$testData->evaluate("//onet/name/text()")->item(0)->nodeValue;
        $nip = self::$testData->evaluate("//onet/nip/text()")->item(0)->nodeValue;

        $client = new RegonClient("", Constants::MODE_PRODUCTION);
        /**
         * @var Result $result
         */
        $result = $client->find($input);

        $this->assertEquals($result->nip, $nip);
        $this->assertEquals($result->name, $name);
        $this->assertEquals($result->regon, $input);
    }
}
