<?php
require_once 'MysqlConnection.php';
/******************
*    空串返回0    *
******************/
function Empty2Zero($s)
{
	return empty($s)? 0:$s;
}

/*****************************
*   返回以#号隔开的一组id    *
******************************/
function String2Int($str){
	$str_arr = explode("#",$str);
	$num_arr = array();
	foreach($str_arr as $sub){
		array_push($num_arr,intval($sub));
	}
	return $num_arr;
}

/******************
*    计算方差     *
******************/
function CalcVariance($id_arr,$count,$ave,$what){
	$powsum = 0;
	foreach($id_arr as $id){
		$entry = mysql_fetch_row(mysql_query("SELECT 
			PCC_RANK1_SINR, 
			PCC_RANK2_SINR1, 
			Serving_Cell_RSRP, 
			Serving_Cell_RSRQ, 
			Serving_Cell_RSSI,
			PDCP_Throughput_UL,
			PDCP_Throughput_DL
			FROM originalinfo WHERE id='$id'")) or die("Invalid query of select entry from original " . mysql_error());
		$powsum += ($entry[$what]-$ave)*($entry[$what]-$ave);
	}
	return $powsum/$count;
}

/*******************************
*    取小数点后一位有效数组    *
*******************************/
function LastEffect($num){
	$pow = 0;
	while($num<1){
		$pow++;
		$num*=10.0;
	}
	$num = floor($num);
	$num/=pow(10.0,$pow);
	return $num;
}
?>