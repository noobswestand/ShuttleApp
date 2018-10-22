<?php
function array2csv(array &$array){
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}
function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function export(){
	dbConnect();
	$pid=$_SESSION['pid'];
	$sql="SELECT request.LocationFrom, request.LocationTo, request.TimePickUpNew AS Time,
	car.License, COUNT(requeststudent.RequestID) AS NumberOfStudents FROM request
	INNER JOIN car ON request.CarID=car.CarID
	LEFT JOIN requeststudent ON request.RequestID=requeststudent.RequestID
	WHERE request.DriverID=$pid
	GROUP BY request.RequestID";
	$result=query($sql);
	$result2=[];
	//Calculate the distance for each location
	function vincentyGreatCircleDistance(
		$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) +
		pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);
		return round(($angle * $earthRadius * 0.621371)/1000,2);
	}

	
	foreach($result as $r){
		$dist=0;
		$to=$r["LocationTo"];
		$from=$r["LocationFrom"];
		$sql="SELECT * FROM place WHERE Name='$from'";
		$sql2="SELECT * FROM place WHERE Name='$to'";
		$result3=query($sql);
		$result4=query($sql2);
		if (!empty($result3) && !empty($result4)){
			$dist=vincentyGreatCircleDistance($result3[0]["Lat"],$result3[0]["Lon"],
			$result4[0]["Lat"],$result4[0]["Lon"]);
		}
		$r["Distance"]=$dist;
		array_push($result2,$r);
	}
	dbClose();
	
	download_send_headers("data_export_" . date("Y-m-d") . ".csv");
	echo array2csv($result2);
	
	die();
}

?>
