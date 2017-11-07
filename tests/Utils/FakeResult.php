<?php
/**
 * Class Tests\MWojtowicz\GusClient\Utils\FakeResult
 *
 * @author Michal Wojtowicz <michal.wojtowicz@pb.com>
 */

namespace Tests\MWojtowicz\GusClient\Utils;

use MWojtowicz\GusClient\Result;

/**
 * Class FakeResult
 *
 * @author Michal Wojtowicz <michal.wojtowicz@pb.com>
 */
class FakeResult
{
    public $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }
}
