<?php


namespace Sheel\here_traffic;


class TrafficDictionary
{

    private $_comment;
    
    public function __construct ( $Comment ){

        $this->_comment = strtolower($Comment);

    }

    public function returnRDSDes(){

        switch ($this->_comment) {
            case strpos($this->_comment, 'construction') :
               return 'Construction Work';
            case strpos($this->_comment, 'lane_restriction') :
               return 'Lane closed.';
            case strpos($this->_comment,'congestion'):
                return 'Traffic congestion';
            case strpos($this->_comment,'road_closure'):
                return 'Lane closed.';
        }
    }


}