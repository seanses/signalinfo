<?php
require_once 'MysqlConnection.php';

$data =  json_decode($_POST["data"],true);
$minid = 0;
foreach($data as $d){
	$datetime = $d['datetime'];
	$username = $d['username'];
	$longitude = $d['lng'];
	$latitude = $d['lat'];
	$pci = $d['pci'];
	$sinr = $d['sinr'];
	$rsrp = $d['rsrp'];
	$rsrq = $d['rsrq'];
	$rssi = $d['rssi'];
	$throughputdl = $d['dlthroughput'];
	if (! ($longitude && $latitude && $pci))
		continue;
	
	mysql_query("INSERT into originalinfo values (
			NULL,
			'$datetime',
			'$longitude',
			'$latitude',
			'0',
			'0',
			'0',
			'0',
			'0',
			'$sinr',
			'0',
			'$pci',
			'$rsrp',
			'$rsrq',
			'$rssi',
			'0',
			'$throughputdl')");
	
	$oid = mysql_insert_id();
	if($minid==0)
		$minid=$oid;
	$peoples = mysql_query("SELECT * FROM collector WHERE  
			UserName = '$username' ") or die("Invalid query of selecting from collector: " . mysql_error());
	if(mysql_num_rows($peoples)==0){ //insert
		mysql_query("INSERT INTO collector VALUES (
		NULL,
		'$username',
		'$longitude',
		'$latitude',
		'$oid')");
	}else{	//exit, will update
		$people = mysql_fetch_array($peoples);
		$id = $people['id'];
		$originalid = $people['originalid'] . "#" . $oid;
		mysql_query("UPDATE collector SET
		LastLongitude = '$longitude',
		LastLatitude = '$latitude',
		originalid = '$originalid'
		WHERE id='$id'");
	}
	
	echo $oid . " ";
}

file_get_contents("http://localhost/signalinfo/CalcAverVari.php?minid=$minid");
?>