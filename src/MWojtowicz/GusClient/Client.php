<?php

namespace MWojtowicz\GusClient;

use MWojtowicz\GusClient\Exception;
use DeathByCaptcha;

class Client extends \SoapClient implements Constants {

    private static $methodUrls = array(
        'Zaloguj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Zaloguj',
        'Wyloguj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Wyloguj',
        'DaneSzukaj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj',
        'DanePobierzPelnyRaport' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DanePobierzPelnyRaport',
        'DaneKomunikat' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneKomunikat',
        'GetValue' => 'http://CIS/BIR/2014/07/IUslugaBIR/GetValue',
        'SetValue' => 'http://CIS/BIR/2014/07/IUslugaBIR/SetValue',
        'PobierzCaptcha' => 'http://CIS/BIR/2014/07/IUslugaBIR/PobierzCaptcha',
        'SprawdzCaptcha' => 'http://CIS/BIR/2014/07/IUslugaBIR/SprawdzCaptcha'
    );

    /**
     * GUS Client constructor.
     * @param mixed $userKey - GUS API key
     * @param array $deathByCaptchaUser - DeathByCaptcha login
     * @param $deathByCaptchaPassword - DeathByCaptcha password
     * @param string $mode - possible values: TEST, PRODUCTION
     * @param array $soapOptions http://php.net/manual/en/soapclient.soapclient.php
     */
    public function __construct($userKey, $deathByCaptchaUser, $deathByCaptchaPassword, $mode='TEST', $soapOptions=array()){
        $this->_userKey = $userKey;
        $this->_mode = $mode;

        if(empty($soapOptions['stream_context'])){
            $soapOptions['stream_context'] = stream_context_create();
        }

        $this->_streamContext = $soapOptions['stream_context'];

        if(!is_array($soapOptions)){
            $soapOptions = array();
        }
        $soapOptions['soap_version'] = SOAP_1_2;
        $soapOptions['cache_wsdl'] = WSDL_CACHE_NONE;

        parent::__construct($this->_getWsdlUrl(), $soapOptions);

        $this->dbcUser = $deathByCaptchaUser;
        $this->dbcPass = $deathByCaptchaPassword;

        $this->_prepareSession();
    }

    /**
     * Solves problems when GUS doesn't return expected values
     * @throws Exception\NotFound
     * @throws Exception\TooMuchInputData
     */
    private function _solveError(){
        $errorCode = (int) $this->getValue('KomunikatKod');
        $errorMessage = $this->getValue('KomunikatTresc');
        echo 'Error: '.$errorCode.' - '.$errorMessage."\n";

        switch($errorCode){
            case 1: $this->solveCaptcha(); break;
            case 2: throw new Exception\TooMuchInputData($errorMessage); break;
            case 4: throw new Exception\NotFound($errorMessage); break;
            case 7: $this->_prepareSession(); break;
        }
    }

    /**
     * Full proccessing of captcha decoding
     * @return bool
     * @throws Exception\NotFound
     * @throws Exception\TooMuchInputData
     */
    public function solveCaptcha(){
        $headers = array(
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('PobierzCaptcha'), 1),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
        );
        $this->__setSoapHeaders($headers);

        $checkCaptcha = false;

        while(!$checkCaptcha) {
            $result = parent::PobierzCaptcha()->PobierzCaptchaResult;

            if(empty($result)){
                $checkCaptcha = true;
                continue;
            }

            if (intval($result) == -1) {
                $this->_solveError();
                continue;
            }

            $decodedText = null;

            if($this->_mode == static::MODE_PRODUCTION){
                if ($this->dbcClient == null) {
                    $this->dbcClient = new DeathByCaptcha\SocketClient($this->dbcUser, $this->dbcPass);
                }

                if ($captcha = $this->dbcClient->decode('data:image/jpeg;base64,' . $result, 20)) {
                    $decodedText = $captcha['text'];
                }
            } else {
                $decodedText = 'aaaaa';
            }

            if(!empty($decodedText)){
                $headers = array(
                    new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('SprawdzCaptcha'), 1),
                    new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
                );
                $this->__setSoapHeaders($headers);

                $checkCaptcha = parent::SprawdzCaptcha(array(
                    'pCaptcha' => $decodedText
                ))->SprawdzCaptchaResult;

                if ($this->_mode == static::MODE_PRODUCTION && !$checkCaptcha && !empty($captcha)) {
                    $this->dbcClient->report($captcha['captcha']);
                }
            }
        }
        return true;
    }

    /**
     * Find companies by NIP number
     * @param string|array $input Up to 100 input numbers
     * @return array|mixed
     * @throws
     * @throws Exception\InvalidNip
     */
    public function findByNip($input){
        $this->clearInput($input);
        $this->_validateNip($input);
        $paramName = is_array($input) ? 'Nipy' : 'Nip';
        $paramValue = is_array($input) ? implode(',',$input) : $input;
        return $this->find($paramName, $paramValue);
    }

    /**
     * Validates NIP
     * @param $input
     * @throws Exception\InvalidNip
     */
    private function _validateNip(&$input){
        if(is_array($input)){
            foreach($input as $k=>$v){
                if(!$this->_validateNipItem($v)){
                    unset($input[$k]);
                }
            }
            reset($input);
        }
        if((!is_array($input) && !$this->_validateNipItem($input)) || empty($input)){
            throw new Exception\InvalidNip("All given values are incorrect");
        }
    }

    /**
     * Vaidates only one NIP number
     * @param $input
     * @return bool
     */
    private function _validateNipItem(&$input){
        if (strlen($input) != 10) return false;

        $steps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
        $sum = 0;
        for ($i = 0; $i < count($steps) ; $i++) {
            $sum += $steps[$i] * $input[$i];
        }
        $controlDigit = $sum % 11;
        $controlDigit = $controlDigit == 10 ? 0 : $controlDigit;
        if ($controlDigit == $input[strlen($input)-1]) return true;
        return false;
    }

    /**
     * Find company by REGON number
     * @param array|string $input Up to 100 numbers
     * @return array|mixed
     * @throws
     * @throws Exception\InvalidRegon
     */
    public function findByRegon($input){
        $this->clearInput($input);
        $this->_validateRegon($input);
        if(is_array($input)) reset($input);
        $paramName = is_array($input) ? (strlen(current($input))==9 ? 'Regony9zn' : 'Regony14zn')  : 'Regon';
        $paramValue = is_array($input) ? implode(',',$input) : $input;
        return $this->find($paramName, $paramValue);
    }

    /**
     * Vaidates REGON
     * @param $input
     * @throws Exception\InvalidRegon
     */
    private function _validateRegon(&$input){
        if(is_array($input)){
            foreach($input as $k=>$v){
                if(!$this->_validateRegonItem($v)){
                    unset($input[$k]);
                }
            }
            reset($input);
        }
        if((!is_array($input) && !$this->_validateNipItem($input)) || empty($input)){
            throw new Exception\InvalidRegon("All given values are incorrect");
        }
    }

    /**
     * Validates only one REGON number
     * @param $input
     * @return bool
     */
    private function _validateRegonItem(&$input){
        if (!in_array(strlen($input), array(9,14))) return false;
        if(strlen($input)==9){
            $steps = array(8, 9, 2, 3, 4, 5, 6, 7);
        } else {
            $steps = array(2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8);
        }
        $sum = 0;
        for ($i = 0; $i < count($steps) ; $i++) {
            $sum += $steps[$i] * $input[$i];
        }
        $controlDigit = $sum % 11;
        $controlDigit = $controlDigit == 10 ? 0 : $controlDigit;
        if ($controlDigit == $input[strlen($input)-1]) return true;
        return false;
    }

    /**
     * Find company by KRS number
     * @param array|string $input Up to 100 numbers
     * @return array|mixed
     * @throws
     */
    public function findByKrs($input){
        $this->clearInput($input);
        $paramName = is_array($input) ? 'Krsy' : 'Krs';
        $paramValue = is_array($input) ? implode(',',$input) : $input;
        return $this->find($paramName, $paramValue);
    }

    /**
     * Clears variable from any non-digit characters
     * @param $input
     */
    private function clearInput(&$input){
        if(is_array($input)){
            foreach($input as $k=>$v){
                $input[$k] = preg_replace('/[^0-9]/','',$v);
            }
            reset($input);
        } else {
            $input = preg_replace('/[^0-9]/','',$input);
        }
    }

    /**
     * Main find method - performs search on API
     * @param $paramName
     * @param $paramValue
     * @return array|mixed One object or array of objects
     * @throws Exception\NotFound
     */
    private function find($paramName, $paramValue){
        $headers = array(
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('DaneSzukaj'), 0),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
        );
        $this->__setSoapHeaders($headers);
        $params = array('pParametryWyszukiwania' => array($paramName => $paramValue));

        $this->solveCaptcha();

        $result = parent::DaneSzukaj($params);
        $xml = new \DOMDocument();
        $xml->loadXML($result->DaneSzukajResult);
        unset($result);

        if($xml->documentElement->hasChildNodes()){
            $data = array();
            for($i=0; $i<$xml->documentElement->childNodes->length; $i++){
                $node = $xml->documentElement->childNodes->item($i);
                if(($node instanceof \DOMElement) && $node->tagName=='dane'){
                    $data[] = $this->parseResult($node);
                    $node->parentNode->removeChild($node);
                }
            }
            if(count($data)==1){
                reset($data);
                $data = current($data);
            }
        } else {
            throw Exception\NotFound();
        }

        return $data;
    }

    /**
     * Parses XML node, and returns simple \stdClass with data of only one company
     * @param \DOMElement $node
     * @return \stdClass
     */
    private function parseResult(\DOMElement $node){
        $result = new \stdClass();
        if($node->hasChildNodes()){
            foreach($node->childNodes as $child){
                if($child->nodeName=='Regon') $result->regon = $child->textContent;
                if($child->nodeName=='RegonLink') $result->regonLink = $child->textContent;
                if($child->nodeName=='Nazwa') $result->name = $child->textContent;
                if($child->nodeName=='Ulica') $result->street = $child->textContent;
                if($child->nodeName=='Miejscowosc') $result->city = $child->textContent;
                if($child->nodeName=='KodPocztowy') $result->postalCode = $child->textContent;
                if($child->nodeName=='Gmina') $result->commune = $child->textContent;
                if($child->nodeName=='Powiat') $result->county = $child->textContent;
                if($child->nodeName=='Wojewodztwo') $result->voivodeship = $child->textContent;
                if($child->nodeName=='Typ') $result->type = $child->textContent;
                if($child->nodeName=='SilosID') $result->silosID = (int) $child->textContent;
            }

            switch($result->type){
                case 'P': $result->typeDescription = 'jednostka prawna'; break;
                case 'F': $result->typeDescription = 'jednostka fizyczna (os. fizyczna prowadząca działalność gospodarczą)'; break;
                case 'LP': $result->typeDescription = 'jednostka lokalna jednostki prawnej'; break;
                case 'LF': $result->typeDescription = 'jednostka lokalna jednostki fizycznej'; break;
            }

            switch($result->silosID){
                case 1: $result->silosDescription = 'Miejsce prowadzenia działalności CEIDG'; break;
                case 2: $result->silosDescription = 'Miejsce prowadzenia działalności Rolniczej'; break;
                case 3: $result->silosDescription = 'Miejsce prowadzenia działalności pozostałej'; break;
                case 4: $result->silosDescription = 'Miejsce prowadzenia działalności zlikwidowanej w starym systemie KRUPGN'; break;
                case 6: $result->silosDescription = 'Miejsce prowadzenia działalności jednostki prawnej'; break;
            }
        }
        return $result;
    }

    /**
     * Destroys session with API
     * @return bool
     */
    public function logout(){
        if($this->getSessionStatus()){
            $this->_setStreamContextSession(null);
            $headers = array(
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('Wyloguj'), 0),
                new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
            );
            $this->__setSoapHeaders($headers);

            $result = parent::Wyloguj(array(
                'pIdentyfikatorSesji' => $this->_sessionId
            ))->WylogujResult;

            if($result){
                $this->storeSession(null);
                $this->_setStreamContextSession(null);
            }

            return $result ? true : false;
        }
        return true;
    }

    /**
     * Returns API status
     * @return string
     */
    public function getServiceStatus(){
        $status = $this->getValue('StatusUslugi');
        switch($status){
            case 2: return static::STATUS_TECHNICALBREAK;
            case 1: return static::STATUS_AVAILABLE;
            case 0:
            default:
                return static::STATUS_UNAVAILABLE;
        }
    }

    /**
     * Returns session status
     * @return string
     */
    public function getSessionStatus(){
        $status = (int) $this->getValue('StatusSesji');
        switch($status){
            case 1: return static::SESSION_ALIVE;
            case 0:
            default:
                return static::SESSION_DEAD;
        }
    }

    /**
     * Simple getter from API sessions's variables
     * @param $paramName
     * @return string
     */
    public function getValue($paramName){
        $headers = array(
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('GetValue'), 0),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
        );
        $this->__setSoapHeaders($headers);

        $result = parent::GetValue(array('pNazwaParametru'=>$paramName));
        return trim($result->GetValueResult);
    }

    /**
     * Returns url for \SoapClient, based on mode.
     * @return string
     */
    private function _getServiceUrl(){
        switch($this->_mode){
            case static::MODE_PRODUCTION: return static::URL_PRODUCTION;
            default: return static::URL_TEST;
        }
    }


    /**
     * Returns WSDL file url for \SoapClient, based on mode.
     * @return string
     */
    private function _getWsdlUrl(){
        switch($this->_mode){
            case static::MODE_PRODUCTION: return static::URL_WSDL_PRODUCTION;
            default: return static::URL_WSDL_TEST;
        }
    }

    /**
     * Returns action URL for API methods
     * @param $methodName
     * @return string
     */
    private function _getMethodUrl($methodName){
        return isset(self::$methodUrls[$methodName]) ? self::$methodUrls[$methodName] : '';
    }

    /**
     * Writes session ID to \SoapClient stream context
     * @param null $sid
     */
    private function _setStreamContextSession($sid=null){
        if(empty($sid)){
            $options = array();
        } else {
            $options = array(
                'http' => array('header' => 'sid: ' . $sid)
            );
        }
        stream_context_set_params($this->_streamContext, array('options'=>$options));
    }

    /**
     * Have to establish session between client and API server
     * @throws Exception\Login
     */
    private function _prepareSession(){
        if(empty($this->_sessionId)) {
            $session = $this->readSessionFile();

            if(!empty($session)){
                $this->_setStreamContextSession($session);
                $sessionStatus = $this->getSessionStatus();
                if($sessionStatus==static::SESSION_ALIVE){
                    $this->_sessionId = $session;
                    $this->_setStreamContextSession(null);
                }
            }

            if(empty($this->_sessionId)){
                $this->_login();
            }
            $this->_setStreamContextSession($this->_sessionId);
        }
    }

    /**
     * Looks for file with session ID, and read it if possible, otherwise creates this file.
     * @return string
     */
    private function readSessionFile(){
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . static::SESSIONFILE_NAME;
        $sessionFileExists = file_exists($filePath);

        if($this->_sessionFile==null){
            $this->_sessionFile = fopen($filePath, $sessionFileExists ? 'r+' : 'w');
        }

        if($sessionFileExists) {
            $session = fread($this->_sessionFile, 100);
            return trim($session);
        }
    }

    /**
     * Login into API
     * @throws Exception\Login
     */
    private function _login(){
        $headers = array(
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->_getMethodUrl('Zaloguj'), 0),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->_getServiceUrl(), 0)
        );
        $this->__setSoapHeaders($headers);

        $result = parent::Zaloguj(array('pKluczUzytkownika'=>$this->_userKey));
        if(empty($result)){
            throw new Exception\Login();
        }

        $this->storeSession($result->ZalogujResult);
        $this->solveCaptcha();
    }

    /**
     * Save created session to object property, and temp file.
     * @param $sessionId
     */
    private function storeSession($sessionId){
        $this->_sessionId = $sessionId;

        if($this->_sessionFile==null){
            $this->readSessionFile();
        }
        ftruncate($this->_sessionFile, 0);
        fwrite($this->_sessionFile, $this->_sessionId.PHP_EOL);
    }

    /**
     * @overwrite
     */
    public function __doRequest($req, $location, $action, $version = SOAP_1_2) {
        $req = preg_replace('@<([a-z0-9]+):Action>[^<]+</([a-z0-9]+):Action>@','<$1:Action>'.$action.'</$2:Action>', $req);
        //echo chr(27) . "[0;33m" .$req. chr(27) . "[0m"."\n";
        $response = parent::__doRequest($req, $location, $action, $version);
        $matches = array();
        preg_match("@<s:Envelope.*</s:Envelope>@s", $response, $matches);
        if(!empty($matches)){
            return current($matches);
        }
        return null;
    }

    /**
     * Desctructor, used only to release temp file handler
     */
    public function __destruct(){
        if($this->_sessionFile){
            fclose($this->_sessionFile);
        }
    }
}