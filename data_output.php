<?php
require_once 'db_helper.php';
require_once 'config.php';
require_once 'file_helper.php';
require_once 'Tools.php';
$data = array (
		'error' => 0 
);
if ($_GET ['type']) {
	$output_type = $_GET ['type'];
	// 加载原始信息
	if ($output_type == 'original') {
		$data ['originaldata'] = originalinfo_get ();
	} 	// 加载某坐标详细历史信息
	else if ($output_type == 'detail') {
		if ($_GET ["longi"] && $_GET ["lati"]) {
			$data ['detaildata'] = get_detial_data ( $_GET ['longi'], $_GET ['lati'] );
			// $data['detaildata'] = get_detial_data(116.643098, 40.315050);
		} else
			$data ['error'] = 1;
	} 	// 加载csv文件路径信息
	else if ($output_type == "path") {
		if ($_GET ["file"]) {
			$file = $_GET ["file"];
			$data_array = array ();
			$handle = fopen ( "uploads/path/$file", "r" );
			if ($head = fgetcsv ( $handle )) {
				$col = array();
				for($c=0;$c<count($head);$c++){
					$col[$head[$c]] = $c;
				}
				for($i = 0; $data = fgetcsv ( $handle); $i ++) {
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
					$throughput = Empty2Zero($data[$col['PDCP Throughput UL(kbit/s)']]);
					$data=array($datetime,$longitude,$latitude,$gpshight,$gpsspeed,$gpssatellites,$gpsheading,
								$pccaveragesinr,$pccrank1sinr,$pccrank2sinr1,$servingcellpci,$servingcellrsrp,$servingcellrsrq,$servingcellrssi,$throughput);
					$data_array [$i] = $data;
				}
			}
			// for($i = 0; $data = fgetcsv ( $handle, 1000, "," ); $i ++) {
				// foreach ( $data as &$column ) {
					// if (empty ( $column ))
						// $column = 'NULL';
					// $column = trim ( $column );
				// }
				// $data_array [$i] = $data;
			// }
			$data ['pathdata'] = $data_array;
			fclose ( $handle );
		}
	}
	else if($output_type == "region"){
		$pci = $_GET["pci"];
		$pci=rtrim($pci,'*');
		$pci_list = explode('*', $pci);
		$data['regiondata'] = get_region_data($pci_list);
	}
	else if($output_type == "pciList"){
		$data['pciList'] = get_PCI_list();
	}
} else
	$data ['error'] = 1;
$json_string = json_encode ( $data );
echo $json_string;
?>