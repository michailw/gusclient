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

        $this->assertEquals($input, $result->nip);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($regon, $result->regon);
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
        $this->assertEquals($input1, $result[0]->nip);
        $this->assertEquals($name1, $result[0]->name);
        $this->assertEquals($regon1, $result[0]->regon);

        $this->assertArrayHasKey("1", $result);
        $this->assertInstanceOf(Result::class, $result[1]);
        $this->assertEquals($input2, $result[1]->nip);
        $this->assertEquals($name2, $result[1]->name);
        $this->assertEquals($regon2, $result[1]->regon);
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

        $this->assertEquals($nip, $result->nip);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($input, $result->regon);
    }
}
