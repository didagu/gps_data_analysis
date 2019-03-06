<?php

    $host = "localhost";
    $db_name = "gpsdata";
    $user_name = "root";
    $password = "";
    $table_name = "gps";

    if($_GET) {
        $tlnid = $_GET['tlnid'];    
    } else {
        $tlnid = "C2IAX0P";
    }

    $mysqli = new mysqli($host, $user_name, $password, $db_name);

    $result = $mysqli->query("SELECT * FROM $table_name WHERE tlnid ='$tlnid' order by time ");