<?php

    include "database.php";
    include "kalman.php";
    include "functions.php";
    include "constants.php";    

    $kalman_filter =new KalmanFilter();

    $pavers_array = [];

    while($row = $result->fetch_array(MYSQLI_ASSOC)) {

        extract($row);

        $item = [
            "id" => $id,
            "acc" => $acc,
            "longitude" => $long,
            "latitude" => $lat,
            // "tlnid" => $tlnid,
            // "live" => $live,
            "timestamp" => strtotime($time)
        ];

        array_push($pavers_array, $item);
    }    

    $final_result = [];
    $totaldistance = 0;
    for($i = 0 ; $i + 1 < sizeof($pavers_array); $i++) {
        $distance = calculate_distance(
            $pavers_array[$i]["latitude"],
            $pavers_array[$i]["longitude"],
            $pavers_array[$i + 1]["latitude"],
            $pavers_array[$i + 1]["longitude"] 
        );
 

        $totaldistance += $distance;
        
        $speed = calculate_speed(
            $pavers_array[$i]["timestamp"],
            $pavers_array[$i + 1]["timestamp"],
            $distance
        );

        if ($speed > 20)
        continue;

        // // DecimalToDMS($pavers_array[0]["latitude"], $deg, $min, $sec, $dir, true); // degree
        // $lat = ($pavers_array[$i]["latitude"] + $pavers_array[$i + 1]["latitude"]) / 2;

        // // DecimalToDMS($pavers_array[0]["longitude"], $deg, $min, $sec, $dir, false); // degree
        // $long = ($pavers_array[$i]["longitude"] + $pavers_array[$i + 1]["longitude"]) / 2;

        // $time = ($pavers_array[$i]["timestamp"] + $pavers_array[$i + 1]["timestamp"]) / 2;
        // $acc = ($pavers_array[$i]["acc"] + $pavers_array[$i + 1]["acc"]) / 2;
        
        // $result = $kalman_filter->process($speed, $lat, $long, $time, $acc);
        
        // $result = [$distance, $speed];

        array_push($final_result, ['x'=>abs($pavers_array[$i]["timestamp"]),'y'=>abs($speed)]);
    }

    print "<pre>";
    // print_r ($final_result);
    print "</pre>";
?>

<!DOCTYPE HTML>
<html>
<head>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", 
	title:{
		text: "Study of Speed vs Distance"
	},
	axisX:{
		title: "distance",
		suffix: " m"
	},
	axisY:{
		title: "speed",
		suffix: " mpm",
		includeZero: false
	},
	data: [{
		type: "scatter",
		markerType: "circle",
		markerSize: 5,
		toolTipContent: "Speed: {y} mpm<br>Distance: {x} m",
		dataPoints: <?php echo json_encode($final_result, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<br>
<div style="text-align:center">
    <button class="tlnid" type="button" onclick="submit('C2IAX0P')" class="btn btn-large btn-block btn-default">C2IAX0P</button>
    &nbsp;
    <button class="tlnid" type="button" onclick="submit('C3TAY02')" class="btn btn-large btn-block btn-default">C3TAY02</button>
    &nbsp;
    <button class="tlnid" type="button" onclick="submit('C4WAY04')" class="btn btn-large btn-block btn-default">C4WAY04</button>
    &nbsp;
    <button class="tlnid" type="button" onclick="submit('C9HAX0Q')" class="btn btn-large btn-block btn-default">C9HAX0Q</button>
    &nbsp;
    <button class="tlnid" type="button" onclick="submit('CACAY04')" class="btn btn-large btn-block btn-default">CACAY04</button>
    &nbsp;
    <button class="tlnid" type="button" onclick="submit('CBPAY0L')" class="btn btn-large btn-block btn-default">CBPAY0L</button>
</div>


<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
    function submit(data){
        window.location.href = "?tlnid=" + data;
    }
</script>
</body>
</html> 

