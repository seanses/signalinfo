<?php
require_once 'MysqlConnection.php';

class Point{
	var $lat;
	var $lng;
	function Point($lat, $lng){
		$this->lat = $lat;
		$this->lng = $lng;
	}
}

$result = mysql_query("SELECT * FROM baidubasedinfo") or die("Invalid query 18: " . mysql_error());
$points = array();
while($row = mysql_fetch_array($result)){
	$p = new Point($row['Latitude'],$row['Longitude']);
	array_push($points,$p);
}
echo json_encode($points);
?>