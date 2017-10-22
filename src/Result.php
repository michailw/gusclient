<?php

namespace MWojtowicz\GusClient;

class Result {
    public $regon;

    public $type;

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
            if ($child->nodeName == 'Typ') $this->type = $child->textContent;
            if ($child->nodeName == 'SilosID') $this->silosID = (int)$child->textContent;
        }
    }

    public function parseDetails($details)
    {
        if (!empty($details->regon)) $this->regon = $details->regon;
        if (!empty($details->nip)) $this->nip = $details->nip;
        if (!empty($details->house)) $this->street .= ' ' . $details->house;
        if (!empty($details->flat)) $this->street .= '/' . $details->flat;

        if (!empty($this->type)) {
            switch ($this->type) {
                case 'P':
                    $this->typeDescription = 'jednostka prawna';
                    break;
                case 'F':
                    $this->typeDescription = 'jednostka fizyczna (os. fizyczna prowadząca działalność gospodarczą)';
                    break;
                case 'LP':
                    $this->typeDescription = 'jednostka lokalna jednostki prawnej';
                    break;
                case 'LF':
                    $this->typeDescription = 'jednostka lokalna jednostki fizycznej';
                    break;
            }
        } else {
            $this->type = '';
            $this->typeDescription = '';
        }

        if (!empty($this->silosID)) {
            switch ($this->silosID) {
                case 1:
                    $this->silosDescription = 'Miejsce prowadzenia działalności CEIDG';
                    break;
                case 2:
                    $this->silosDescription = 'Miejsce prowadzenia działalności Rolniczej';
                    break;
                case 3:
                    $this->silosDescription = 'Miejsce prowadzenia działalności pozostałej';
                    break;
                case 4:
                    $this->silosDescription = 'Miejsce prowadzenia działalności zlikwidowanej w starym systemie KRUPGN';
                    break;
                case 6:
                    $this->silosDescription = 'Miejsce prowadzenia działalności jednostki prawnej';
                    break;
            }
        } else {
            $this->silosID = '';
            $this->silosDescription = '';
        }
    }
}