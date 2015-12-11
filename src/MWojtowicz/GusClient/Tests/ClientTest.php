<?php

namespace MWojtowicz\GusClient\Tests;

use MWojtowicz\GusClient;

class ClientTest extends \PHPUnit_Framework_TestCase {

    public function __construct($name = null, array $data = [], $dataName = ''){
        parent::__construct($name, $data, $dataName);

        self::$data = (object) array(
            'Onet' => (object) array(
                'regon' => '001337730',
                'nip' => '7340009469',
                'krs' => '0000007763',
                'name' => 'GRUPA ONET.PL SPÓŁKA AKCYJNA'
            ),
            'Interia' => (object) array(
                'regon' => '141718797',
                'nip' => '6783096366',
                'krs' => '0000324955',
                'name' => 'GRUPA INTERIA.PL SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ'
            )
        );
    }

    public static $data;

    public function testClient(){
        $clientId = readline("Please type GUS client ID: ");
        $dbcUser = readline("Please type DeathByCaptcha username: ");
        $dbcPassword = readline("Please type DeathByCaptcha password: ");

        $client = new GusClient\Client($clientId, $dbcUser, $dbcPassword);

        $onet = $client->findByNip(self::$data->Onet->nip);
        $this->assertEquals(self::$data->Onet->name, $onet->name);

        $interia = $client->findByNip(self::$data->Interia->nip);
        $this->assertEquals(self::$data->Interia->name, $interia->name);

        $both = $client->findByNip(array(self::$data->Interia->nip,self::$data->Onet->nip));
        $this->assertEquals(2, count($both));
    }
}