<?php

namespace Tests\MWojtowicz\GusClient;

use MWojtowicz\GusClient\Exception\NotFound;
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
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
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
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
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
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
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
        $type = "P";

        $expectation = new \stdClass();
        $expectation->house = 1;
        $expectation->flat = 2;
        $expectation->regon = $regon;
        $expectation->nip = "12345678901";

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
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

        $expectation = new \stdClass();

        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) [
                'DanePobierzPelnyRaportResult' => file_get_contents(__DIR__ . "/data/testGetDetails_emptySet.xml")
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
            ->setMethods(["__setSoapHeaders", "DanePobierzPelnyRaport"])
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
            ->method("DanePobierzPelnyRaport")
            ->with([
                'pRegon' => $regon,
                'pNazwaRaportu' => "PublDaneRaportPrawna"
            ])
            ->willReturn((object) []);

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
            ->setmethods(["setStreamContext", "__setSoapHeaders", "Wyloguj"])
            ->getMockForAbstractClass();

        $client
            ->at(0)
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
            ->setmethods(["setStreamContext", "__setSoapHeaders", "Wyloguj"])
            ->getMockForAbstractClass();

        $client
            ->at(0)
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
