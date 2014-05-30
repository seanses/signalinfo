<?php
// upload original data to database
function upload_file($file_tool_name, $file_save_path) {
	if ($_FILES [$file_tool_name] ["error"] > 0) {
		echo "Error: " . $_FILES [$file_tool_name] ["error"] . "<br />";
	} else {
		echo "Upload: " . $_FILES [$file_tool_name] ["name"] . "<br />";
		echo "Type: " . $_FILES [$file_tool_name] ["type"] . "<br />";
		echo "Size: " . ($_FILES [$file_tool_name] ["size"] / 1024) . " Kb<br />";
		echo "Stored in: " . $_FILES [$file_tool_name] ["tmp_name"];
		
		if (file_exists ( $file_save_path )) {
			echo $_FILES [$file_tool_name] ["name"] . " already exists. ";
		} else {
			move_uploaded_file ( $_FILES [$file_tool_name] ["tmp_name"], $file_save_path );
			echo "Stored in: " . $file_save_path;
		}
	}
}

//由sinr平均值-距离曲线生成sinr平均值-时间曲线
function convert_sinrave_distance_to_sinrave_time_csvfile($points_sinrave_dit,$longest_dit){
	$file = fopen("generate/curve.csv","w"); //generate a csv file, rewrite if exist
	fputcsv($file,array("TIME","SINR_AVERAGE"));
	
	$dit = 0;
	$dit_interval = $longest_dit/150;
	$time = 1000;
	
	$points_sinrave_time = array();
	
	while(count($points_sinrave_time)<150 && $dit<$longest_dit){
		for($i=0; $i<count($points_sinrave_dit); $i++){
			if($points_sinrave_dit[$i]->x <= $dit && $points_sinrave_dit[$i+1]->x > $dit){
				$k = ($points_sinrave_dit[$i+1]->y - $points_sinrave_dit[$i]->y)/($points_sinrave_dit[$i+1]->x - $points_sinrave_dit[$i]->x);
				fputcsv($file,array($time,$points_sinrave_dit[$i]->y+$k*($dit-$points_sinrave_dit[$i]->x)));
				$dit += $dit_interval;
				$time += $time_interval;
				break;
			}
		}
	}
	return "generate/curve.csv";
}
?>