<?php
declare(strict_types=1);

namespace MWojtowicz\GusClient;

/**
 * Class Client
 *
 * @method object DaneSzukaj(array $params)
 * @method object DanePobierzPelnyRaport(array $params)
 * @method object Zaloguj(array $params)
 * @method object Wyloguj(array $params)
 * @method object GetValue(array $params)
 */
abstract class Client extends \SoapClient
{
    /**
     * @var array API methods addresses
     */
    protected static $methodUrls = [
        'Zaloguj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Zaloguj',
        'Wyloguj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/Wyloguj',
        'DaneSzukaj' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneSzukaj',
        'DanePobierzPelnyRaport' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DanePobierzPelnyRaport',
        'DaneKomunikat' => 'http://CIS/BIR/PUBL/2014/07/IUslugaBIRzewnPubl/DaneKomunikat',
        'GetValue' => 'http://CIS/BIR/2014/07/IUslugaBIR/GetValue',
        'SetValue' => 'http://CIS/BIR/2014/07/IUslugaBIR/SetValue',
        'PobierzCaptcha' => 'http://CIS/BIR/2014/07/IUslugaBIR/PobierzCaptcha',
        'SprawdzCaptcha' => 'http://CIS/BIR/2014/07/IUslugaBIR/SprawdzCaptcha'
    ];

    /**
     * @var string User Key from GUS
     */
    private $userKey;

    /**
     * @var string Current session id
     */
    private $sessionId;

    /**
     * @var resource File handler for session store
     */
    private $sessionFile;

    /**
     * @var string Mode of this tool, possible values: TEST, PRODUCTION
     */
    private $mode;

    /**
     * @var resource Stream context handler for \SoapClient
     */
    protected $streamContext;

    /**
     * @var string Class which result item has to be casted
     */
    protected $resultClassName;

    /**
     * GUS Client constructor.
     *
     * @param string $userKey - GUS API key
     * @param string $mode - possible values: TEST, PRODUCTION
     * @param array $soapOptions http://php.net/manual/en/soapclient.soapclient.php
     */
    public function __construct(string $userKey = "", string $mode = "TEST", array $soapOptions = [])
    {
        if (empty($userKey)) {
            $userKey = getenv("GUSAPI_KEY");
        }

        $this->userKey = $userKey;
        $this->mode = $mode;

        if (empty($soapOptions['stream_context'])) {
            $soapOptions['stream_context'] = stream_context_create();
        }

        $this->streamContext = $soapOptions['stream_context'];
        $soapOptions['soap_version'] = SOAP_1_2;
        $soapOptions['cache_wsdl'] = WSDL_CACHE_NONE;
        $soapOptions['encoding'] = 'UTF-8';
        $soapOptions['exceptions'] = 1;
        $soapOptions['location'] = $this->getServiceUrl();

        parent::__construct($this->getWsdlUrl(), $soapOptions);
    }

    /**
     * Sets class which will be used to cast results
     *
     * @param string $className Class name to set
     * @throws \LogicException Thrown when class does not exists
     *
     * @return Client
     */
    public function setResultClassName(string $className) : Client
    {
        if (class_exists($className)) {
            $this->resultClassName = $className;
        } else {
            throw new \LogicException("Class {$className} does not exists.");
        }

        return $this;
    }

    /**
     * Clears variable from any non-digit characters
     * @param array|string $input
     */
    protected function clearInput(&$input)
    {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                $input[$k] = $this->clearItem($v);
            }
            reset($input);
        } else {
            $input = $this->clearItem($input);
        }
    }

    /**
     * Removes non numeric characters
     * @param string $input
     * @return string
     */
    protected function clearItem(string $input) : string
    {
        return preg_replace('/[^0-9]/', '', $input);
    }

    /**
     * Main find method - performs search on API
     *
     * @param string $paramName
     * @param string $paramValue
     *
     * @throws Exception\NotFound
     *
     * @return Result[]|Result One object or array of objects
     */
    protected function findBy(string $paramName, string $paramValue)
    {
        $this->prepareSession();

        $headers = [
            new \SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'Action',
                $this->getMethodUrl('DaneSzukaj'),
                false
            ),
            new \SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'To',
                $this->getServiceUrl(),
                false
            )
        ];
        $this->__setSoapHeaders($headers);
        $params = ['pParametryWyszukiwania' => [$paramName => $paramValue]];

        $result = $this->callApi("DaneSzukaj", $params);
        if (empty($result->DaneSzukajResult)) {
            throw new Exception\NotFound();
        }
        $xml = new \DOMDocument();
        $xml->loadXML($result->DaneSzukajResult);
        unset($result);

        if ($xml->documentElement->hasChildNodes()) {
            $data = [];
            for ($i = 0; $i < $xml->documentElement->childNodes->length; $i++) {
                $node = $xml->documentElement->childNodes->item($i);
                if (($node instanceof \DOMElement) && $node->nodeName == 'dane') {
                    $data[] = $this->parseResult($node);
                    $node->parentNode->removeChild($node);
                }
            }
            if (count($data) == 1) {
                reset($data);
                $data = current($data);
            }
        } else {
            throw new Exception\NotFound();
        }
        unset($result);

        return $data;
    }

    /**
     * Gets details of particular company by it's REGON number
     * @param string $regon
     * @param string $type
     * @return \stdClass
     */
    public function getDetails(string $regon, string $type = 'F') : \stdClass
    {
        $headers = [
            new \SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'Action',
                $this->getMethodUrl('DaneSzukaj'),
                false
            ),
            new \SoapHeader(
                'http://www.w3.org/2005/08/addressing',
                'To',
                $this->getServiceUrl(),
                false
            )
        ];
        $this->__setSoapHeaders($headers);
        $params = [
            'pRegon' => $regon
        ];

        $prefix = "";

        switch ($type) {
            case 'F':
            case 'LF':
                $params['pNazwaRaportu'] = 'PublDaneRaportDzialalnoscFizycznejCeidg';
                $prefix = 'fiz';
                break;
            case 'P':
            case 'LP':
                $params['pNazwaRaportu'] = 'PublDaneRaportPrawna';
                $prefix = 'praw';
        }

        $result = $this->callApi("DanePobierzPelnyRaport", $params);
        if (empty($result->DanePobierzPelnyRaportResult)) {
            throw new Exception\NotFound();
        }
        $xml = new \DOMDocument();
        $xml->loadXML($result->DanePobierzPelnyRaportResult);
        unset($result);

        if ($xml->documentElement->hasChildNodes()) {
            $data = new \stdClass();

            for ($i = 0; $i < $xml->documentElement->childNodes->length; $i++) {
                $node = $xml->documentElement->childNodes->item($i);
                if ($node->nodeName == 'dane') {
                    for ($j = 0; $j < $node->childNodes->length; $j++) {
                        $child = $node->childNodes->item($j);
                        switch ($child->nodeName) {
                            case $prefix . '_adSiedzNumerNieruchomosci':
                                $data->house = $child->textContent;
                                break;
                            case $prefix . '_adSiedzNumerLokalu':
                                $data->flat = $child->textContent;
                                break;
                            case $prefix . '_regon14':
                                $data->regon = $child->textContent;
                                break;
                            case $prefix . '_nip':
                                $data->nip = $child->textContent;
                                break;
                        }
                    }
                }
            }
        } else {
            throw new Exception\NotFound();
        }

        return $data;
    }

    /**
     * Parses XML node, and returns simple Result object with data of only one company
     *
     * @param \DOMElement $node
     *
     * @return Result
     */
    private function parseResult(\DOMElement $node) : Result
    {
        $result = new Result();
        if ($node->hasChildNodes()) {
            $result->parseBasicData($node->childNodes);

            $details = $this->getDetails($result->regon, $result->type);
            $result->parseDetails($details);
        }
        if (!empty($this->resultClassName)) {
            $result = new $this->resultClassName($result);
        }
        return $result;
    }

    /**
     * Destroys session with API
     *
     * @return bool
     */
    public function logout() : bool
    {
        $this->setStreamContextSession(null);
        $headers = [
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->getMethodUrl('Wyloguj'), false),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->getServiceUrl(), false)
        ];
        $this->__setSoapHeaders($headers);

        $result = $this->Wyloguj([
            'pIdentyfikatorSesji' => $this->sessionId
        ])->WylogujResult;

        if ($result) {
            $this->storeSession(null);
            $this->setStreamContextSession(null);
        }

        return $result ? true : false;
    }

    /**
     * Returns API status
     *
     * @return string
     */
    public function getServiceStatus()
    {
        $status = $this->getApiValue('StatusUslugi');
        switch ($status) {
            case 2:
                return Constants::STATUS_TECHNICALBREAK;
            case 1:
                return Constants::STATUS_AVAILABLE;
            case 0:
            default:
                return Constants::STATUS_UNAVAILABLE;
        }
    }

    /**
     * Returns session status
     *
     * @return string
     */
    public function getSessionStatus() : string
    {
        $status = (int)$this->getApiValue('StatusSesji');
        switch ($status) {
            case 1:
                return Constants::SESSION_ALIVE;
            case 0:
            default:
                return Constants::SESSION_DEAD;
        }
    }

    /**
     * Simple getter from API sessions's variables
     * @param $paramName
     * @return string
     */
    public function getApiValue(string $paramName) : ?string
    {
        $headers = [
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->getMethodUrl('GetValue'), false),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->getServiceUrl(), false)
        ];
        $this->__setSoapHeaders($headers);

        $result = $this->callApi("GetValue", ['pNazwaParametru' => $paramName]);
        return trim($result->GetValueResult);
    }

    /**
     * Returns url for \SoapClient, based on mode.
     * @return string
     */
    private function getServiceUrl() : string
    {
        switch ($this->mode) {
            case Constants::MODE_PRODUCTION:
                return Constants::URL_PRODUCTION;
            default:
                return Constants::URL_TEST;
        }
    }

    /**
     * Returns WSDL file url for \SoapClient, based on mode.
     * @return string
     */
    private function getWsdlUrl() : string
    {
        switch ($this->mode) {
            case Constants::MODE_PRODUCTION:
                return Constants::URL_WSDL_PRODUCTION;
            default:
                return Constants::URL_WSDL_TEST;
        }
    }

    /**
     * Returns action URL for API methods
     *
     * @param $methodName
     *
     * @return string
     */
    private function getMethodUrl($methodName) : string
    {
        return isset(self::$methodUrls[$methodName]) ? self::$methodUrls[$methodName] : '';
    }

    /**
     * Writes session ID to \SoapClient stream context
     *
     * @param string|null $sid Session ID
     */
    public function setStreamContextSession(?string $sid = null)
    {
        $options = [];
        if (!empty($sid)) {
            $options['http'] = ['header' => 'sid: ' . $sid];
        }

        $this->setStreamContext($options);
    }

    /**
     * Wraps PHP's function stream_context_set_params
     *
     * @param array $options Stream context options
     */
    public function setStreamContext(array $options)
    {
        stream_context_set_params($this->streamContext, ['options' => $options]);
    }

    /**
     * Have to establish session between client and API server
     *
     * @throws Exception\Login
     */
    public function prepareSession()
    {
        if (empty($this->sessionId)) {
            $session = $this->getSessionId();

            if (!empty($session)) {
                $this->setStreamContextSession($session);
                $sessionStatus = $this->getSessionStatus();
                $this->setStreamContextSession(null);
                if ($sessionStatus == Constants::SESSION_ALIVE) {
                    $this->sessionId = $session;
                }
            }

            if (empty($this->sessionId)) {
                $this->login();
            }
            $this->setStreamContextSession($this->sessionId);
        }
    }

    /**
     * Gets session ID from file if session file exists
     *
     * @return string
     */
    public function getSessionId() : string
    {
        return trim($this->readSessionFile());
    }

    /**
     * Looks for file with session ID, and read it if possible, otherwise creates this file.
     *
     * @return string
     */
    protected function readSessionFile() : string
    {
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . Constants::SESSIONFILE_NAME;
        $sessionFileExists = file_exists($filePath);

        if ($this->sessionFile === null) {
            $this->sessionFile = fopen($filePath, $sessionFileExists ? 'r+' : 'w');
        }

        if ($sessionFileExists) {
            return fread($this->sessionFile, 100);
        }

        return "";
    }

    /**
     * Login into API
     *
     * @throws Exception\Login
     */
    public function login()
    {
        $headers = [
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', $this->getMethodUrl('Zaloguj'), false),
            new \SoapHeader('http://www.w3.org/2005/08/addressing', 'To', $this->getServiceUrl(), false)
        ];
        $this->__setSoapHeaders($headers);

        $result = $this->callApi("Zaloguj", ['pKluczUzytkownika' => $this->userKey]);
        if (empty($result)) {
            throw new Exception\Login();
        }

        $this->storeSession($result->ZalogujResult);
    }

    /**
     * Save created session to object property, and temp file.
     * @param $sessionId
     */
    private function storeSession(?string $sessionId)
    {
        $this->sessionId = $sessionId;

        if ($this->sessionFile == null) {
            $this->getSessionId();
        }
        ftruncate($this->sessionFile, 0);
        fwrite($this->sessionFile, $this->sessionId . PHP_EOL);
    }

    /**
     * @inheritdoc
     */
    public function __doRequest($req, $location, $action, $version = SOAP_1_2, $one_way = 0)
    {
        $req = preg_replace(
            '@<([a-z0-9]+):Action>[^<]+</([a-z0-9]+):Action>@',
            '<$1:Action>' . $action . '</$2:Action>',
            $req
        );
        $response = parent::__doRequest($req, $location, $action, $version, $one_way);
        $matches = [];
        $match = null;
        preg_match("@<s:Envelope.*</s:Envelope>@s", $response, $matches);
        if (!empty($matches)) {
            $match = current($matches);
        }
        return $match;
    }

    /**
     * Calls static SOAP methods
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    protected function callApi(string $method, array $params)
    {
        return static::$method($params);
    }

    /**
     * Destructor used only to release temp file handler
     */
    public function __destruct()
    {
        if ($this->sessionFile) {
            fclose($this->sessionFile);
        }
    }
}
