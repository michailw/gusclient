<?php

namespace MWojtowicz\GusClient;

/**
 * Class RegonClient
 *
 */
class RegonClient extends Client implements GusClientInterface
{
    /**
     * Find company by REGON number
     *
     * @param array|string $input Up to 100 numbers
     * @throws Exception\InvalidRegon
     *
     * @return array|mixed
     */
    public function find($input)
    {
        $this->clearInput($input);
        $this->validateRegon($input);

        if (is_array($input)) {
            reset($input);
        }

        $paramName = is_array($input) ? (strlen(current($input)) == 9 ? 'Regony9zn' : 'Regony14zn') : 'Regon';
        $paramValue = is_array($input) ? implode(',', $input) : $input;

        return $this->findBy($paramName, $paramValue);
    }

    /**
     * Validates REGON
     *
     * @param string|array $input Number of array of numbers to validate
     * @throws Exception\InvalidRegon
     */
    public function validateRegon(&$input)
    {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                if (!$this->validateRegonItem($v)) {
                    unset($input[$k]);
                }
            }
            reset($input);
        }
        if ((!is_array($input) && !$this->validateRegonItem($input)) || empty($input)) {
            throw new Exception\InvalidRegon("All given values are incorrect");
        }
    }

    /**
     * Validates only one REGON number
     * @param $input
     * @return bool
     */
    public function validateRegonItem($input)
    {
        if (!in_array(strlen($input), [9, 14])) return false;
        if (strlen($input) == 9) {
            $steps = [8, 9, 2, 3, 4, 5, 6, 7];
        } else {
            $steps = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];
        }
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