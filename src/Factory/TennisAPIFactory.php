<?php


namespace Sheel\HereTraffic\Factory;
use Sheel\HereTraffic\TrafficAPIClient;

class TrafficAPIFactory
{
    /**
     * @param string $apiKey
     * @param int $timeout
     * @return TrafficAPIClient
     */
    public static function create( $apiKey, $timeout = 60 ) {

        return new TrafficAPIClient($apiKey);

    }
}