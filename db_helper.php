<?php
require_once 'Tools.php';
include_once 'params.php';
// 连接数据库
function db_connect() {
	global $_config;
	$result = new mysqli ( $_config['db']['dbhost'], $_config['db']['dbuser'], $_config['db']['dbpw'], $_config['db']['dbname'] );
	if (! $result) {
		throw new Exception ( 'Could not connect to database server' );
	} else {
		return $result;
	}
}
function sql_query($sqlString) {
	$conn = db_connect ();
	$conn->query ( $sqlString );
}

// 清空数据库
function database_clear() {
	$conn = db_connect ();
	$conn->query ( "truncate baidubasedinfo" );
	$conn->query ( "truncate originalinfo" );
	$conn->query ( "truncate processedinfo" );
}

//上传原始数据
function originalinfo_upload($upfile, $people) {
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
			if(!($longitude && $latitude)) continue;
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
			$throughputdl = Empty2Zero($data[$col['PDCP Throughput DL(kbit/s)']])*$people;
			
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
	fclose ( $handle );
}

/**
 * 返回全部原始数据
 * @throws Exception
 * @return multitype:unknown
 */
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

/**
 * 获得pci凸包数组，如果传入0返回所有PCI凸包数组，否则返回传入pci凸包数组
 * @param int $num 传入的pci
 * @throws Exception
 * @return multitype:unknown
 */
function get_region_data($PCI_list) {
		$k=0;
		for($j = 0; $j < count ( $PCI_list ); $j ++) {
			$PCI = $PCI_list [$j];
			$conn = db_connect ();
			$result = $conn->query ( "SELECT Latitude as y,Longitude as x FROM baidubasedinfo where PCI=$PCI" );
			if (! $result) {
				throw new Exception ( '原始数据读取失败。' );
			}
			if ($result->num_rows <= 0) {
				continue;
			}
			
			for($i = 0; $row = $result->fetch_assoc (); $i ++) {
				$infos [$i] = $row;
			}
			
			// 根据pci将正确的基站坐标加入到凸包的数组中
			$point = array (
					"x" => 0,
					"y" => 0 
			);
			if ($PCI == 102 || $PCI == 101 || $PCI == 100) {
				$point ['x'] = 116.64480;
				$point ['y'] = 40.31580;
			} else if ($PCI == 103 || $PCI == 104 || $PCI == 105) {
				$point ['x'] = 116.6359;
				$point ['y'] = 40.30915;
			} else if ($PCI == 106 || $PCI == 107 || $PCI == 108) {
				$point ['x'] = 116.636772;
				$point ['y'] = 40.322987;
			} else if ($PCI == 109 || $PCI == 110 || $PCI == 111) {
				$point ['x'] = 116.637725;
				$point ['y'] = 40.317957;
			}
			array_push ( $infos, $point );
			// end
			$arrayPro [$k] = GetRegion ( $infos );
			$k++;
		}
	return $arrayPro;
}

/**
 * 返回pci数据
 * @throws Exception
 * @return unknown
 */
function get_PCI_list() {
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
