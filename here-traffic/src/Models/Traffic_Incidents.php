<?php

namespace Sheel\here_traffic\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Traffic_Incidents
 * @package Sheel\here_traffic\Models
 */
class Traffic_Incidents extends Model
{
    protected $table = 'traffic_incidents';
    public $timestamps = true;


    /**
     * @var array
     */
    protected $fillable = [
        'screen_id',
        'traffic_id',
        'location',
        'traffic_status',
        'traffic_desc',
        'criticality',
        'comment',
        'rds_tmc_desc',
        'road_type'

    ];
}
