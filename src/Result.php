<?php
declare(strict_types=1);

namespace MWojtowicz\GusClient;

class Result {
    /**
     * @var string
     */
    public $regon;

    /**
     * @var string
     */
    public $nip;

    /**
     * @var string
     */
    public $regonLink;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $house;

    /**
     * @var string
     */
    public $flat;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $postalCode;

    /**
     * @var string
     */
    public $commune;

    /**
     * @var string
     */
    public $county;

    /**
     * @var string
     */
    public $voivodeship;

    /**
     * @var int
     */
    public $silosID;

    /**
     * @var int
     */
    public $silosDescription;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $typeDescription;

    public function parseBasicData(\DOMNodeList $domNodes)
    {
        foreach ($domNodes as $child) {
            if ($child->nodeName == 'Regon') $this->regon = $child->textContent;
            if ($child->nodeName == 'RegonLink') $this->regonLink = $child->textContent;
            if ($child->nodeName == 'Nazwa') $this->name = $child->textContent;
            if ($child->nodeName == 'Ulica') $this->street = $child->textContent;
            if ($child->nodeName == 'Miejscowosc') $this->city = $child->textContent;
            if ($child->nodeName == 'KodPocztowy') $this->postalCode = $child->textContent;
            if ($child->nodeName == 'Gmina') $this->commune = $child->textContent;
            if ($child->nodeName == 'Powiat') $this->county = $child->textContent;
            if ($child->nodeName == 'Wojewodztwo') $this->voivodeship = $child->textContent;
            if ($child->nodeName == 'Typ') {
                $this->type = $child->textContent;
                $this->typeDescription = static::getTypeDescriptionByAbbreviation($this->type);
            }
            if ($child->nodeName == 'SilosID') {
                $this->silosID = (int)$child->textContent;
                $this->silosDescription = static::getSilosDescriptionById($this->silosID);
            }
        }
    }

    public function parseDetails($details)
    {
        if (!empty($details->regon)) $this->regon = $details->regon;
        if (!empty($details->nip)) $this->nip = $details->nip;
        if (!empty($details->house)) $this->house = $details->house;
        if (!empty($details->flat)) $this->flat = $details->flat;
    }

    /**
     * Returns user friendly name of silos by it's ID
     *
     * @param int $silosType
     *
     * @return string
     */
    public static function getSilosDescriptionById(int $silosType) : string
    {
        switch ($silosType) {
            case 1:
                return 'Miejsce prowadzenia działalności CEIDG';
            case 2:
                return 'Miejsce prowadzenia działalności Rolniczej';
            case 3:
                return 'Miejsce prowadzenia działalności pozostałej';
            case 4:
                return 'Miejsce prowadzenia działalności zlikwidowanej w starym systemie KRUPGN';
            case 6:
                return 'Miejsce prowadzenia działalności jednostki prawnej';
            default:
                return "";
        }
    }

    /**
     * Returns user friendly type description by it's one/two letter abbreviation
     *
     * @param string $type
     *
     * @return string
     */
    public static function getTypeDescriptionByAbbreviation(string $type) : string
    {
        switch ($type) {
            case 'P':
                return 'jednostka prawna';
            case 'F':
                return 'jednostka fizyczna (os. fizyczna prowadząca działalność gospodarczą)';
            case 'LP':
                return 'jednostka lokalna jednostki prawnej';
            case 'LF':
                return 'jednostka lokalna jednostki fizycznej';
            default:
                return "";
        }
    }
}
