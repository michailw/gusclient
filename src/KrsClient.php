<?php

namespace MWojtowicz\GusClient;

/**
 * Class KrsClient
 */
class KrsClient extends Client
{
    /**
     * Find company by KRS number
     *
     * @param array|string $input Up to 100 numbers
     *
     * @return array|mixed
     */
    public function find($input)
    {
        $this->clearInput($input);
        $paramName = is_array($input) ? 'Krsy' : 'Krs';
        $paramValue = is_array($input) ? implode(',', $input) : $input;
        return $this->findBy($paramName, $paramValue);
    }
}
