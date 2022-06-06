<?php
	$lon = isset($_GET["lon"]) ? $_GET["lon"] : die("Err");
	$lat = isset($_GET["lat"]) ? $_GET["lat"] : die("err");
	file_put_contents("log.txt", date("Y-m-d h:i:s") . "\t" . round($lat,7) . "," . round($lon, 7) . "\r\n", FILE_APPEND | LOCK_EX);
?>