<?php
declare(strict_types=1);

/**
 * Class MWojtowicz\GusClient\ClientInterface
 *
 * @author Michal Wojtowicz <michal.wojtowicz@pb.com>
 */

namespace MWojtowicz\GusClient;


interface GusClientInterface
{
    /**
     * Finds value in API
     *
     * @param array|string $input Input to find by
     *
     * @return array|mixed One object or array of objects
     */
    public function find($input);
}
