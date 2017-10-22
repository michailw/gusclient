<?php

namespace MWojtowicz\GusClient;

/**
 * Class NIPClient
 *
 */
class NIPClient extends Client implements GusClientInterface
{
    /**
     * Find companies by NIP number
     *
     * @param string|array $input Up to 100 input numbers
     *
     * @throws Exception\InvalidNip Thrown when $input parameter is invalid
     *
     * @return array|mixed
     */
    public function find($input)
    {
        $this->clearInput($input);
        $this->validateNip($input);

        $paramName = is_array($input) ? 'Nipy' : 'Nip';
        $paramValue = is_array($input) ? implode(',', $input) : $input;

        return $this->findBy($paramName, $paramValue);
    }

    /**
     * Validates NIP array
     *
     * @param array|string $input Array of NIP numbers to validate
     *
     * @throws Exception\InvalidNip
     */
    public function validateNip(&$input)
    {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                if (!$this->validateNipItem($v)) {
                    unset($input[$k]);
                }
            }
            reset($input);
        }

        if ((!is_array($input) && !$this->validateNipItem($input)) || empty($input)) {
            throw new Exception\InvalidNip("All given values are incorrect");
        }
    }

    /**
     * Validates only one NIP number
     *
     * @param string $input NIP number to validate
     *
     * @return bool
     */
    public function validateNipItem($input)
    {
        if (strlen($input) != 10) return false;

        $steps = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum = 0;
        for ($i = 0; $i < count($steps); $i++) {
            $sum += $steps[$i] * $input[$i];
        }
        $controlDigit = $sum % 11;
        $controlDigit = $controlDigit == 10 ? 0 : $controlDigit;
        if ($controlDigit == $input[strlen($input) - 1]) return true;
        return false;
    }
}
