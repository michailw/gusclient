<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Constants;
use MWojtowicz\GusClient\NIPClient;
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
     * @inheritdoc
     */
    public function testNipClient_oneClient()
    {
        $input = self::$testData->evaluate("//onet/nip/text()")->item(0)->nodeValue;
        $name = self::$testData->evaluate("//onet/name/text()")->item(0)->nodeValue;

        $client = new NIPClient("", Constants::MODE_PRODUCTION);
        /**
         * @var Result $result
         */
        $result = $client->find($input);

        $this->assertEquals($result->nip, $input);
        $this->assertEquals($result->name, $name);
    }
}
