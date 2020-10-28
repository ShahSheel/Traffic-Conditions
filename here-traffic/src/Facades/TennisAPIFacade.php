<?php

namespace Sheel\here_traffic\Facades;

use Illuminate\Support\Facades\Facade;

class TrafficAPIFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TrafficAPIFacade';
    }
}
