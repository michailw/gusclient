<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Constants;
use MWojtowicz\GusClient\Exception\Login;
use MWojtowicz\GusClient\Exception\NotFound;
use MWojtowicz\GusClient\Result;
use PHPUnit\Framework\TestCase;
use MWojtowicz\GusClient\Client;
use Tests\MWojtowicz\GusClient\Utils\ClientProxy;

class ClientTest extends TestCase
{
    /**
     * @var Client $client
     */
    private $client;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods([
                "DaneSzukaj", "DanePobierzPelnyRaport", "Zaloguj", "Wyloguj", "GetValue",
                "__setSoapHeaders"
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
     * @see Client::getDetails()
     */
    public function testGetDetailsTypeF()
    {
        $regon = "12345678901234";
        $type = "F";

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callAPi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportDzialalnoscFizycznejCeidg"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/testGetDetails_{$type}.xml")
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsTypeLF()
    {
        $regon = "12345678901234";
        $type = "LF";

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportDzialalnoscFizycznejCeidg"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/testGetDetails_{$type}.xml")
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsTypeP()
    {
        $regon = "12345678901234";
        $type = "P";

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/testGetDetails_{$type}.xml")
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsTypeLP()
    {
        $regon = "12345678901234";
        $type = "LP";

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/testGetDetails_{$type}.xml")
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsEmptySet()
    {
        $regon = "12345678901234";
        $type = "P";

        $this->expectException(NotFound::class);

        $expectation = new \stdClass();

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/emptySet.xml")
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsEmptyResult()
    {
        $regon = "12345678901234";
        $type = "P";

        $this->expectException(NotFound::class);

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) []);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::getDetails()
     */
    public function testGetDetailsEmptyXml()
    {
        $regon = "12345678901234";
        $type = "P";

        $this->expectException(NotFound::class);

        $expectation = new \stdClass();;

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $reportXML = file_get_contents(__DIR__ . "/data/emptySet.xml");

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DanePobierzPelnyRaport", [
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) [
                "DanePobierzPelnyRaportResult" => $reportXML
            ]);

        $result = $client->getDetails($regon, $type);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::logout()
     */
    public function testLogoutBadSession()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["setStreamContext", "__setSoapHeaders", "Wyloguj"])
            ->getMockForAbstractClass();

        $client
            ->expects($this->once())
            ->method("setStreamContext")
            ->with([]);

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Wyloguj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("Wyloguj")
            ->with([
                'pIdentyfikatorSesji' => null
            ])
            ->willReturn((object) [
                'WylogujResult' => false
            ]);

        $this->assertFalse($client->logout());
    }

    /**
     * @see Client::logout()
     */
    public function testLogoutGoodSession()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["setStreamContext", "__setSoapHeaders", "Wyloguj"])
            ->getMockForAbstractClass();

        $client
            ->expects($this->exactly(2))
            ->method("setStreamContext")
            ->with([]);

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Wyloguj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("Wyloguj")
            ->with([
                'pIdentyfikatorSesji' => null
            ])
            ->willReturn((object) [
                'WylogujResult' => true
            ]);

        $this->assertTrue($client->logout());
    }

    /**
     * @see Client::getServiceStatus()
     */
    public function testGetServiceStatus()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["getApiValue"])
            ->getMockForAbstractClass();

        $client
            ->expects($this->exactly(4))
            ->method("getApiValue")
            ->with("StatusUslugi")
            ->willReturnOnConsecutiveCalls(null, 0, 1, 2);

        $this->assertEquals('UNAVAILABLE', $client->getServiceStatus());
        $this->assertEquals('UNAVAILABLE', $client->getServiceStatus());
        $this->assertEquals('AVAILABLE', $client->getServiceStatus());
        $this->assertEquals('TECHNICALBREAK', $client->getServiceStatus());
    }

    /**
     * @see Client::getSessionStatus()
     */
    public function testGetSessionStatus()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["getApiValue"])
            ->getMockForAbstractClass();

        $client
            ->expects($this->exactly(3))
            ->method("getApiValue")
            ->with("StatusSesji")
            ->willReturnOnConsecutiveCalls(1, 0, null);

        $this->assertEquals('ALIVE', $client->getSessionStatus());
        $this->assertEquals('DEAD', $client->getSessionStatus());
        $this->assertEquals('DEAD', $client->getSessionStatus());
    }

    /**
     * @see Client::getApiValue()
     */
    public function testGetApiValue()
    {
        $testData = "Parameter";
        $returnData = " data ";
        $expectation = "data";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMockForAbstractClass();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/2014/07/IUslugaBIR/GetValue", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("GetValue", [
                "pNazwaParametru" => $testData
            ])
            ->willReturn((object) [
                "GetValueResult" => $returnData
            ]);

        $result = $client->getApiValue($testData);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @see Client::clearInput()
     */
    public function testClearInputArray()
    {
        $input = ["efwececwec1234!@#\$ASDFG", "efwececwec1234!@#\$ASDFG"];
        $expectations = ["1234", "1234"];

        $client = new ClientProxy();
        $client->clearInput($input);

        $this->assertEquals($expectations, $input);
    }

    /**
     * @see Client::clearInput()
     */
    public function testClearInputString()
    {
        $input = "efwececwec1234!@#\$ASDFG";
        $expectations = "1234";

        $client = new ClientProxy();
        $client->clearInput($input);

        $this->assertEquals($expectations, $input);
    }

    /**
     * @see Client::clearItem()
     */
    public function testClearItem()
    {
        $input = "efwececwec1234!@#\$ASDFG";
        $expectation = "1234";

        $client = new ClientProxy();
        $this->assertEquals($expectation, $client->clearItem($input));
    }

    /**
     * @see Client::findBy()
     */
    public function testFindByWrongResponse()
    {
        $this->expectException(NotFound::class);

        $paramName = "PARAM";
        $paramValue = "VALUE";

        $expectation = null;

        $client = $this
            ->getMockBuilder(ClientProxy::class)
            ->setMethods(["callApi", "prepareSession"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("prepareSession");

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DaneSzukaj", ["pParametryWyszukiwania" => [$paramName => $paramValue]])
            ->willReturn("");

        $this->assertEquals($expectation, $client->findBy($paramName, $paramValue));
    }

    /**
     * @see Client::findBy()
     */
    public function testFindByEmptyXML()
    {
        $this->expectException(NotFound::class);

        $paramName = "PARAM";
        $paramValue = "VALUE";

        $expectation = [];

        $client = $this
            ->getMockBuilder(ClientProxy::class)
            ->setMethods(["callApi", "prepareSession"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("prepareSession");

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("DaneSzukaj", ["pParametryWyszukiwania" => [$paramName => $paramValue]])
            ->willReturn((object) [
                "DaneSzukajResult" => file_get_contents(__DIR__ . "/data/emptySet.xml")
            ]);

        $this->assertEquals($expectation, $client->findBy($paramName, $paramValue));
    }

    /**
     * @see Client::findBy()
     */
    public function testFindByOneRow()
    {
        $paramName = "PARAM";
        $paramValue = "VALUE";

        $expectation = new Result();
        $expectation->regon = "123456789";
        $expectation->regonLink = "http://123456789";
        $expectation->name = "Name";
        $expectation->street = "Street";
        $expectation->city = "City";
        $expectation->postalCode = "PostalCode";
        $expectation->county = "County";
        $expectation->commune = "Commune";
        $expectation->voivodeship = "Voivodeship";
        $expectation->silosID = 5;
        $expectation->type = "Type";

        $client = $this
            ->getMockBuilder(ClientProxy::class)
            ->setMethods(["callApi", "prepareSession"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("prepareSession");

        $client
            ->expects($this->exactly(2))
            ->method("callApi")
            ->withConsecutive(
                ["DaneSzukaj", ["pParametryWyszukiwania" => [$paramName => $paramValue]]],
                ["DanePobierzPelnyRaport", ["pRegon" => "123456789"]]
            )
            ->willReturnOnConsecutiveCalls(
                (object) [
                    "DaneSzukajResult" => file_get_contents(__DIR__ . "/data/testFindByOneRow.xml")
                ],
                (object) [
                    "DanePobierzPelnyRaportResult" => file_get_contents(__DIR__ . "/data/testFindByOneRow_details.xml")
                ]
            );

        $this->assertEquals($expectation, $client->findBy($paramName, $paramValue));
    }

    /**
     * @see Client::getSessionId()
     */
    public function testGetSessionIdSessionFileDoesNotExist()
    {
        @unlink(sys_get_temp_dir() . DIRECTORY_SEPARATOR . Constants::SESSIONFILE_NAME);

        $client = new ClientProxy();
        $this->assertEquals("", $client->getSessionId());
    }

    /**
     * @see Client::__construct()
     */
    public function testConstructorKeyFromEnvVar()
    {
        $envVal = getenv("GUSAPI_KEY");
        putenv("GUSAPI_KEY=dummyValue");

        $reflectionClass = new \ReflectionClass(Client::class);
        $reflectionProperty = $reflectionClass->getProperty("_userKey");
        $reflectionProperty->setAccessible(true);

        $client = new ClientProxy("");
        $this->assertEquals("dummyValue", $reflectionProperty->getValue($client));

        putenv("GUSAPI_KEY=".$envVal);
    }

    /**
     * @see Client::setStreamContextSession()
     */
    public function testSetStreamContextSession_noSid()
    {
        $client = $this
            ->getMockBuilder(ClientProxy::class)
            ->setMethods(["setStreamContext"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("setStreamContext")
            ->with([]);

        $client->setStreamContextSession();
    }

    /**
     * @see Client::setStreamContextSession()
     */
    public function testSetStreamContextSession()
    {
        $sid = "TEST";

        $client = $this
            ->getMockBuilder(ClientProxy::class)
            ->setMethods(["setStreamContext"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("setStreamContext")
            ->with(['http' => ['header' => 'sid: ' . $sid]]);

        $client->setStreamContextSession($sid);
    }

    /**
     * @see Client::setStreamContext()
     */
    public function testSetStreamContext()
    {
        $params = [
            "http" => [
                "param1" => "value1",
                "param2" => "value2"
            ]
        ];

        $expectation = [
            "options" => [
                "http" => [
                    "param1" => "value1",
                    "param2" => "value2",
                    "protocol_version" => 1.1,
                    "header" => "Connection: close\r\n"
                ]
            ]
        ];

        $reflectionClass = new \ReflectionClass(Client::class);
        $reflectionProperty = $reflectionClass->getProperty("_streamContext");
        $reflectionProperty->setAccessible(true);

        $client = new ClientProxy();
        $client->setStreamContext($params);

        $this->assertEquals($expectation, stream_context_get_params($reflectionProperty->getValue($client)));
    }

    /**
     * @see Client::prepareSession()
     */
    public function testPrepareSession()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->setMethods([
                "getSessionId",
                "setStreamContextSession",
                "getSessionStatus",
                "login"
            ])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("getSessionId")
            ->with()
            ->willReturn("");

        $client
            ->expects($this->once())
            ->method("login")
            ->with();

        $client
            ->expects($this->once())
            ->method("setStreamContextSession")
            ->with(null);

        $client
            ->expects($this->never())
            ->method("getSessionStatus");

        $client->prepareSession();
    }

    /**
     * @see Client::prepareSession()
     */
    public function testPrepareSessionWithExistingSessionDead()
    {
        $sessionID = "TEST";

        $client = $this
            ->getMockBuilder(Client::class)
            ->setMethods([
                "getSessionId",
                "setStreamContextSession",
                "getSessionStatus",
                "login"
            ])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("getSessionId")
            ->with()
            ->willReturn($sessionID);

        $client
            ->expects($this->exactly(3))
            ->method("setStreamContextSession")
            ->withConsecutive(
                [$sessionID],
                [null],
                [null]
            );

        $client
            ->expects($this->once())
            ->method("getSessionStatus")
            ->willReturn(Constants::SESSION_DEAD);

        $client
            ->expects($this->once())
            ->method("login")
            ->with();

        $client->prepareSession();
    }

    /**
     * @see Client::prepareSession()
     */
    public function testPrepareSessionWithExistingSessionAlive()
    {
        $sessionID = "TEST";

        $client = $this
            ->getMockBuilder(Client::class)
            ->setMethods([
                "getSessionId",
                "setStreamContextSession",
                "getSessionStatus",
                "login"
            ])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("getSessionId")
            ->with()
            ->willReturn($sessionID);

        $client
            ->expects($this->exactly(3))
            ->method("setStreamContextSession")
            ->withConsecutive(
                [$sessionID],
                [null],
                [$sessionID]
            );

        $client
            ->expects($this->once())
            ->method("getSessionStatus")
            ->willReturn(Constants::SESSION_ALIVE);

        $client
            ->expects($this->never())
            ->method("login");

        $client->prepareSession();
    }

    /**
     * @see Client::callApi()
     */
    public function testCallApi()
    {
        $client = new ClientProxy();
        $this->assertEquals(["TEST"], $client->callApi("fakeFunction", ["TEST"]));
    }

    /**
     * @see Client::__construct()
     */
    public function testProductionUrls()
    {
        $reflectionClass = new \ReflectionClass(Client::class);
        $reflectionMethod1 = $reflectionClass->getMethod("_getWsdlUrl");
        $reflectionMethod1->setAccessible(true);
        $reflectionMethod2 = $reflectionClass->getMethod("_getServiceUrl");
        $reflectionMethod2->setAccessible(true);

        $client = new ClientProxy("", Constants::MODE_PRODUCTION);

        $this->assertEquals(Constants::URL_WSDL_PRODUCTION, $reflectionMethod1->invoke($client));
        $this->assertEquals(Constants::URL_PRODUCTION, $reflectionMethod2->invoke($client));
    }

    /**
     * @see Client::__construct()
     */
    public function testTestUrls()
    {
        $reflectionClass = new \ReflectionClass(Client::class);
        $reflectionMethod1 = $reflectionClass->getMethod("_getWsdlUrl");
        $reflectionMethod1->setAccessible(true);
        $reflectionMethod2 = $reflectionClass->getMethod("_getServiceUrl");
        $reflectionMethod2->setAccessible(true);

        $client = new ClientProxy("", Constants::MODE_TEST);

        $this->assertEquals(Constants::URL_WSDL_TEST, $reflectionMethod1->invoke($client));
        $this->assertEquals(Constants::URL_TEST, $reflectionMethod2->invoke($client));
    }

    /**
     * @see Client::login()
     */
    public function testLogin()
    {
        $sessionID = "TEST";

        $client = $this
            ->getMockBuilder(Client::class)
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Zaloguj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("Zaloguj")
            ->willReturn((object) [
                "ZalogujResult" => $sessionID
            ]);


        $reflectionClass = new \ReflectionClass(Client::class);
        $reflectionProperty = $reflectionClass->getProperty("_sessionId");
        $reflectionProperty->setAccessible(true);

        $client->login();

        $this->assertEquals($sessionID, $reflectionProperty->getValue($client));
    }

    /**
     * @see Client::login()
     */
    public function testLoginWithException()
    {
        $this->expectException(Login::class);

        $client = $this
            ->getMockBuilder(Client::class)
            ->setMethods(["__setSoapHeaders", "callApi"])
            ->getMock();

        $client
            ->expects($this->once())
            ->method("__setSoapHeaders")
            ->with([
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', "http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Zaloguj", 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc", 0)
            ]);

        $client
            ->expects($this->once())
            ->method("callApi")
            ->with("Zaloguj")
            ->willReturn("");

        $client->login();
    }
}
