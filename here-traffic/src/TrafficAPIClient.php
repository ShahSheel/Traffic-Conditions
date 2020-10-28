<?php

namespace Sheel\here_traffic;
use Exception;
use Sheel\CarTraffic\Model\ScreenTrafficRoute;
use Sheel\here_traffic\Models\Traffic_Incidents;

class TrafficAPIClient
{
    /** @var string HOST - The path to the HERE API endpoint */
    protected $host =  'https://traffic.api.here.com/traffic/6.3/incidents.json?app_id=';

    /**
     * @var String Api Key
     */
    protected $_apiKey;

    /** @var string $uri */
    protected $uri;

    /** @var string|array $feed */
    protected $feed;

    protected $_appID;

    protected $feed_type;

    protected $route;
    
    public function __construct($appID, $apiKey){
        $this->_appID = $appID;
        $this->_apiKey = $apiKey;
    }


    /**
     * @return $this
     * @throws Exception
     */
    private function checkAPIKey(){

        if( empty($this->_apiKey )) {
            throw new Exception('ERROR: Could not find API key for Here Traffic in environment');
        }
        if( empty($this->_appID )) {
            throw new Exception('ERROR: Could not find APP ID for Here Traffic in environment');
        }

        return $this;

    }


    /**
     * @return $this
     */
    protected function buildURI() {

         //Construct the URL
         $this->uri = $this->host
             .$this->_appID    // APP ID
             .'&app_code=' .   // Append Path for API key
            $this->_apiKey . '&bbox='  // Add Api key and append box path
        ;

        return $this;
    }

    private function getScreenTrafficRoutes(){



        // For each screen
        $Routes =  ScreenTrafficRoute::all();

        // For each route
        foreach($Routes as $route ){


            $this
                ->constructRequest( $route )
                -> fetchContents()
                -> decodeContents()
//            -> checkStatus()
                -> getFeed();
                
            $this-> checkFeed( $route );

          

        }
        return $this;

    }

    private function constructRequest( $route ){
        $this
            -> buildURI()     // Build the URI
        ;

        // Add the Start and End lat & longs to the URI
        $this->uri .=
            $route->route_start_lat . ',' .
            $route->route_start_lon . ';' .
            $route->route_end_lat . ',' .
            $route->route_end_lon
        ;

        $this
            -> fetchContents()  // Fetch the contents
            -> decodeContents() // Decode the contents

        ;

        return $this;

    }
    /**
     * Get the contents from the remote API
     * @throws Exception
     * @return $this
     */
    protected function fetchContents() {

        // Get the contents of the API call.
        $this->feed = file_get_contents( $this->uri );

        // Make chainable
        return $this;

    }

    /**
     * Decode the contents from the remote API
     * @return $this
     */
    protected function decodeContents() {

        // Decode and push to member.
        $this->feed = json_decode( $this->feed, true );

        // Make chainable
        return $this;

    }



    /**
     * @return array|string
     * @throws Exception
     */
    protected function getFeed() {
        // If feed has never been set
        if( is_null( $this->feed ) ) {

            throw new Exception( 'There is no feed as none of the fetch methods have been called', 400 );

        }


        // If feed hasn't been decoded, return an empty array.
        else return gettype( $this->feed ) !== 'array' ? array() : $this->feed;

    }


    /**
     * @return $this
     * @throws Exception
     */
    protected function checkStatus() {

        // If not throw an exception based on the status
        if( array_key_exists( '401 Authorization Required', $this->feed ) ) throw new Exception(

            "{$this->feed[ 'status' ]}: {$this->feed[ 'error_message' ]}", 401

        );

        return $this;

    }

    /**
     * @return array|string
     * @throws Exception
     */
    public function search() {


        return $this
            -> checkAPIKey()
         //   -> buildUri()
            -> getScreenTrafficRoutes()

            ;
          
        

    }

    public function checkFeed( $route ){
        if (isset($this->feed['TRAFFIC_ITEMS'])) {


            if (isset ($this->feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['LOCATION']['DEFINED']['ORIGIN']['ROADWAY']['DESCRIPTION'][0])) {
                $feed_type = 'MOTORWAY';

                var_dump($this->uri);
                $Motworway = new TrafficMotorwayIncidents($this->_appID, $this->_apiKey );

                $Motworway->populateScreenIncidents( $route, $this->feed, $feed_type );

            } elseif (isset ($this->feed['TRAFFIC_ITEMS']['TRAFFIC_ITEM'][0]['LOCATION']['INTERSECTION']['ORIGIN']['STREET1']['ADDRESS1'])) {
                $feed_type = 'INTERSECTION';
                var_dump($this->uri);
                $intersection = new TrafficINtersectionIncidents($this->_appID, $this->_apiKey );

                $intersection->populateScreenIncidents( $route, $this->feed, $feed_type );

                
            } else {
                $feed_type = 'UNKNOWN';
            }
        }
        else{
            // Delete the screen as the screen has no traffic and is probably outdated in the DB
            Traffic_Incidents::query()->where('screen_id', $route->screen_id)->forceDelete();
            
        }
      return $this;

    }




}
