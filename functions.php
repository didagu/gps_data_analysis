<?php

    function DecimalToDMS($decimal, &$degrees, &$minutes, &$seconds, &$direction, $type = true) {
        //set default values for variables passed by reference
        $degrees = 0;
        $minutes = 0;
        $seconds = 0;
        $direction = 'X';
        
        //decimal must be integer or float no larger than 180;
        //type must be Boolean
        if(!is_numeric($decimal) || abs($decimal) > 180 || !is_bool($type)) {
            return false;
        }
            
        //inputs OK, proceed
        //type is latitude when true, longitude when false
            
        //set direction; north assumed
        if($type && $decimal < 0) { 
            $direction = 'S';
        }
        elseif(!$type && $decimal < 0) {
            $direction = 'W';
        }
        elseif(!$type) {
            $direction = 'E';
        }
        else {
            $direction = 'N';
        }
            
        //get absolute value of decimal
        $d = abs($decimal);
            
        //get degrees
        $degrees = floor($d);
            
        //get seconds
        $seconds = ($d - $degrees) * 3600;
            
        //get minutes
        $minutes = floor($seconds / 60);
            
        //reset seconds
        $seconds = floor($seconds - ($minutes * 60));   
    }

    function calculate_distance($lat1, $lon1, $lat2, $lon2){
        //Using the "Haversine" formula
         
        $lat1 = deg2rad($lat1);;
        $lat2 = deg2rad($lat2);

        $diff_lat = deg2rad($lat2 - $lat1);
        $diff_lon = deg2rad($lon2 - $lon1);

        $a = sin($diff_lat/2) * sin($diff_lat/2) +
            cos($lat1) * cos($lat2) * sin($diff_lon/2) * sin($diff_lon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return RADIUS * $c;
    }

    function calculate_speed($timestamp1, $timestamp2, $distance){
        if($distance == 0){
            return 0;
        }
        //to avoid any division by zero
        if($timestamp1 == $timestamp2){
            return 0;
        }

        //convert timestamp to minute
        $time_in_minutes = ($timestamp2 - $timestamp1) / (60);
        //calculate speed in metres per minute
        $speed_mpm = $distance / $time_in_minutes;

        return $speed_mpm;
    }