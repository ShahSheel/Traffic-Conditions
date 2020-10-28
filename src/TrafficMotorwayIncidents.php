<?php


namespace Sheel\here_traffic;

use Carbon\Carbon;
use Exception;
use DB;
use Sheel\CarTraffic\Model\ScreenTrafficRoute;
use Sheel\here_traffic\Models\Traffic_Incidents;
use Sheel\HereTraffic\Models\Traffic_Delays;


class TrafficMotorwayIncidents extends TrafficAPIClient
{

   CONST SCREEN_ID = 0;
   CONST TRAFFIC_ID = 1;
   CONST LOCATION = 2;
   CONST TRAFFIC_STATUS = 3;
   CONST TRAFFIC_DESC = 4;
   CONST TRAFFIC_CRITIALITY = 5;
   CONST TRAFFIC_COMMENT = 6;
   CONST RDS_TMS_DESC = 7;
   CONST FEED_TYPE = 8;

    protected $host =  'https://traffic.api.here.com/traffic/6.3/incidents.json?app_id=';

    /** @var string|array $feed */
    protected $feed;


    public function populateScreenIncidents( $route, $feed, $feed_type )
    {


        if (isset($feed['TRAFFIC_ITEMS'])) {

            $this->getData( $route, $feed, $feed_type ) ;

        }

         // Skip
        
        return $this;
    }


    private function getData( $route, $feed, $feed_type ){

        /** @var array $data */
        $data = array(); // Create re-usuable primitve Array

         $this
             -> getScreenID( $route, $data, $feed )       // Screen ID
             -> getScreenTrafficID(  $route, $data, $feed )   // Screen Traffic Routes ID
             -> getTrafficLocation(  $route, $data, $feed )  // Location
             -> getTrafficStatus( $route, $data, $feed )    // Taffic Status
             -> getTrafficCriticality(  $route, $data, $feed ) // Criticality
             -> getTrafficDescription( $route, $data, $feed ) // Traffic Item Description
             -> getTrafficComment( $route, $data, $feed ) // Comment
             -> getTrafficRDS_Description( $route, $data, $feed ) // RDS Description
             -> addFeedType( $data, $feed_type )
             -> insertIntoDB( $data )
         ;



                // Time date Stamps

     return $this;
    }

    /**
     * @param $route
     * @param $data
     * @param $feed
     * @return mixed
     */
    private function getScreenID( $route, &$data, $feed ){

        $data["SCREEN_ID"] = $route->screen_id;

        return $this;
         
    }

    /**
     * Gets Traffic ID
     *
     * @param $route
     * @param $data
     * @param $feed
     * @return $this
     */
    private function getScreenTrafficID( $route, &$data, $feed){

        $data['TRAFFIC_ID'] =  $route->id;

        return $this;
    }

    /**
     *
     * @param $route
     * @param $data
     * @param $feed
     * @return $this
     */
    private function getTrafficLocation( $route, &$data, $feed ){

        // Search through the location descriptions

        $feed_desc = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['LOCATION']['DEFINED']['ORIGIN']['ROADWAY']['DESCRIPTION'];

        foreach ($feed_desc as $key => $item) {

           // If the type is LOCAL
           if( $item['TYPE'] === 'LOCAL'){
               $data['LOCATION'] = $item['value'];  // Get the value

               break;
           }

        }

        return $this;
    }

    private function getTrafficStatus( $route, &$data, $feed ){

        $data['TRAFFIC_STATUS'] = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['TRAFFIC_ITEM_STATUS_SHORT_DESC'];

        return $this;
    }

    /**
     * Gets Traffic Criticality
     *
     * @param $route
     * @param $data
     * @param $feed
     * @return $this
     */
    private function getTrafficCriticality( $route, &$data, $feed ){


        $data['TRAFFIC_CRITIALITY'] = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['CRITICALITY']['DESCRIPTION'];


        return $this;
    }

    /**
     * Gets Traffic Description
     *
     * @param $route
     * @param $data
     * @param $feed
     * @return $this
     */
    private function getTrafficDescription( $route, &$data, $feed ){

        $data['TRAFFIC_DESC'] = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['TRAFFIC_ITEM_TYPE_DESC'];

        return $this;
    }

    /**
     * Gets Traffic Comment
     * 
     * @param $route
     * @param $data
     * @param $feed
     * @return $this
     */
    private function getTrafficComment( $route, &$data, $feed ){

        $comment = null;
        
        if(isset( $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['COMMENTS'] )){
            $comment = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['COMMENTS'];
        }

        $data['TRAFFIC_COMMENT'] = $comment;

        return $this;
    }

    private function getTrafficRDS_Description( $route, &$data, $feed ){

        $data['RDS_TMS_DESC'] = $feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['RDS-TMC_LOCATIONS']['RDS-TMC'][0]['ALERTC']['DESCRIPTION'];

        return $this;
    }

    private function addFeedType ( &$data, $feed_type ) {


        $data['FEED_TYPE'] = $feed_type;

        return $this;

    }

    private function insertIntoDB( &$data ){

        print_r($data);

        Traffic_Incidents::query()->updateOrCreate([

                'screen_id' => $data['SCREEN_ID']  ,
                'traffic_id'  =>  $data['TRAFFIC_ID'],
                'location'  =>  $data['LOCATION'],
                'traffic_status'  =>  $data['TRAFFIC_STATUS'],
                'traffic_desc'  => $data['TRAFFIC_DESC'],
                'criticality'  => $data['TRAFFIC_CRITIALITY'],
                'comment'  =>  $data['TRAFFIC_COMMENT'],
                'rds_tmc_desc'  =>  $data['RDS_TMS_DESC'],
                'road_type' =>  $data['FEED_TYPE']

        ], $data );
    }


}
