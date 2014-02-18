<?php
require_once 'Tools.php';
// 连接数据库
function db_connect() {
	$result = new mysqli ( "localhost", "root", "", "signalinfo" );
	if (! $result) {
		throw new Exception ( 'Could not connect to database server' );
	} else {
		return $result;
	}
}

// 上传原始数据
function originalinfo_upload($upfile) {
	$conn = db_connect ();
	$handle = fopen ( $upfile, "r" );
	if ($head = fgetcsv ( $handle )) {
		$col = array();
		for($c=0;$c<count($head);$c++){
			$col[$head[$c]] = $c;
		}
		while ( $data = fgetcsv ( $handle) ) {
			foreach ( $data as &$column ) {
				// if (empty ( $column ))
					// $column = 'NULL';
				$column = trim($column);
			}
			$datetime = Empty2Zero($data[$col['Date & Time']]);
			$longitude = Empty2Zero($data[$col['Longitude']]);
			$latitude = Empty2Zero($data[$col['Latitude']]);
			$gpshight = Empty2Zero($data[$col['GPS Hight']]);
			$gpsspeed = Empty2Zero($data[$col['GPS Speed']]);
			$gpssatellites = Empty2Zero($data[$col['GPS Satellites']]);
			$gpsheading = Empty2Zero($data[$col['GPS Heading']]);
			$pccaveragesinr = Empty2Zero($data[$col['PCC Average SINR(Normal/csi-MeasSubframeSet1)(dB)']]);
			$pccrank1sinr = Empty2Zero($data[$col['PCC RANK1 SINR(Normal/csi-MeasSubframeSet1)(dB)']]);
			$pccrank2sinr1 = Empty2Zero($data[$col['PCC RANK2 SINR1(Normal/csi-MeasSubframeSet1)(dB)']]);
			$servingcellpci = Empty2Zero($data[$col['Serving Cell PCI']]);
			$servingcellrsrp = Empty2Zero($data[$col['Serving Cell RSRP(dBm)']]);
			$servingcellrsrq = Empty2Zero($data[$col['Serving Cell RSRQ(dB)']]);
			$servingcellrssi = Empty2Zero($data[$col['Serving Cell RSSI(dBm)']]);
			$throughputul = Empty2Zero($data[$col['PDCP Throughput UL(kbit/s)']]);
			$throughputdl = Empty2Zero($data[$col['PDCP Throughput DL(kbit/s)']]);
			
			$sqlstr1 = "INSERT into originalinfo values (
			NULL,
			'$datetime',
			'$longitude',
			'$latitude',
			'$gpshight',
			'$gpsspeed',
			'$gpssatellites',
			'$gpsheading',
			'$pccaveragesinr',
			'$pccrank1sinr',
			'$pccrank2sinr1',
			'$servingcellpci',
			'$servingcellrsrp',
			'$servingcellrsrq',
			'$servingcellrssi',
			'$throughputul',
			'$throughputdl')";
		 $conn->query ( $sqlstr1 ) or die (  $conn->error );
		}
		echo '表读取成功';
	} else
		echo "表读取错误";
		// fgetcsv($handle, 1000, ",");
		// $data = fgetcsv($handle, 1000, ",");
		// foreach ($data as &$column){
		// if(empty($column)){
		// $column = 'NULL';
		// }
		// }
		// $sqlstr1 = "insert into originalinfo values (NULL,'$data[0]',$data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7],$data[8],$data[9],$data[10],$data[11],$data[12],$data[13])";
		// echo $sqlstr1;
	fclose ( $handle );
}

//读取全部原始数据
function originalinfo_get() {
	$conn = db_connect ();
	$result = $conn->query ( "select * from baidubasedinfo");
	if ( !$result) {
		throw new Exception ( '原始数据读取失败。' );
	}
	if ($result->num_rows <= 0) {
		throw new Exception ( '原始数据为空。' );
	}
	$infos = array();
	for($i=0; $row = $result->fetch_assoc(); $i++){
		$infos[$i] = $row;
	}
	return $infos;
// 	$json_string = json_encode($infos);
// 	echo " var originaldata = $json_string;";
}

function get_detial_data($longi , $lati){
	$conn = db_connect ();
	$result = $conn->query ( "SELECT originalid FROM processedinfo WHERE id=(select gridid from baidubasedinfo where Longitude=$longi and Latitude=$lati)");
	if ( !$result) {
		throw new Exception ( '原始数据读取失败。' );
	}
	if ($result->num_rows <= 0) {
		throw new Exception ( '原始数据为空。' );
	}
	$data = $result->fetch_assoc();
	$infos= $data['originalid'];
	$originalid_arr = String2Int($infos);
	$query_string = 'select * from originalinf where id in (';
	$id_string = implode(',', $originalid_arr);
	$data_arr = $conn->query("select * from originalinfo where id in ($id_string)");
	$infos = array();
	for($i=0; $row = $data_arr->fetch_assoc(); $i++){
		$infos[$i] = $row;
	}
	return $infos;
}

function get_region_data(){
	$PCI_list = get_PCI_list();
	for($j=0;$j<count($PCI_list);$j++){
		$PCI = $PCI_list[$j];
		$conn = db_connect ();
		$result = $conn->query ( "SELECT Latitude as y,Longitude as x FROM baidubasedinfo where PCI=$PCI");
		if ( !$result) {
			throw new Exception ( '原始数据读取失败。' );
		}
		if ($result->num_rows <= 0) {
			throw new Exception ( '原始数据为空。' );
		}
		
		for($i=0; $row = $result->fetch_assoc(); $i++){
			$infos[$i] = $row;
		}
		$arrayPro[$j] = GetRegion($infos);
	}
	return $arrayPro;
}

//获得PCI列表
function get_PCI_list(){
	$conn = db_connect ();
	$result = $conn->query ( "SELECT distinct PCI FROM baidubasedinfo where PCI!=0");
	if ( !$result) {
		throw new Exception ( '原始数据读取失败。' );
	}
	if ($result->num_rows <= 0) {
		throw new Exception ( '原始数据为空。' );
	}

	for($i=0; $row = $result->fetch_assoc(); $i++){
		$infos[$i] = $row['PCI'];
	}
	return $infos;
}
?>
