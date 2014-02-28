<?php
require_once 'MysqlConnection.php';
/******************
*    �մ�����0    *
******************/
function Empty2Zero($s)
{
	return empty($s)? 0:$s;
}

/*****************************
*   ������#�Ÿ�����һ��id    *
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
*    ���㷽��     *
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
*    ȡС�����һλ��Ч����    *
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
/*******************************
 *    ͹����غ���   *
*******************************/
//�������֮���ƽ�����
function getDistance($p1,$p2){
	return sqrt(($p1['x']-$p2['y'])*($p1['x']-$p2['y'])+($p1['y']-$p2['y'])*($p1['y']-$p2['y']));
}

//����ֵΪ��˵��p0p1p2��ת ������ֵΪ����˵��p0p1p2Ϊ��ת
function Mutiply($p1,$p2,$p0){
	return (($p1['x']-$p0['x'])*($p2['y']-$p0['y'])-($p2['x']-$p0['x'])*($p1['y']-$p0['y']));
}

//���͹������
function GetRegion($pointsArray){
	$start = $pointsArray[0];
	$n = 0;
	for($i=0; $i<count($pointsArray); $i++){
		if($start['y']>$pointsArray[$i]['y']||($start['y']==$pointsArray[$i]['y']&&$start['x']>$pointsArray[$i]['x'])){
			$start = $pointsArray[$i];
			$n = $i;
		}
	}
	//�������µĵ��Ƶ�ǰ��
	$tmp = $pointsArray[0];
	$pointsArray[0] = $start;
	$pointsArray[$n] = $tmp;
	// 	echo "exchang 0 - $n<br/>";
	for($i=1; $i<count($pointsArray); $i++){
		for($j=$i; $j<count($pointsArray); $j++){
			$angel = Mutiply($pointsArray[$i], $pointsArray[$j], $pointsArray[0]);
			if($angel<0 || ($angel == 0 && getDistance($pointsArray[$i], $pointsArray[0]) > getDistance($pointsArray[$j], $pointsArray[0]))){
				$tmp = $pointsArray[$j];
				$pointsArray[$j] = $pointsArray[$i];
				$pointsArray[$i] = $tmp;
				// echo "exchang $i - $j<br/>";
			}
		}
	}
	$vector = array($pointsArray[0],$pointsArray[1]);
	for($i=2; $i<count($pointsArray); $i++){
		// 		echo $top.'<br/>';
		while(Mutiply($pointsArray[$i], $vector[count($vector)-1], $vector[count($vector)-2])>=0){
			if(empty($vector))
				break;
			array_pop($vector);
		}
		//ȥ�������ֵ
		if($pointsArray[$i]!=null){
		array_push($vector, $pointsArray[$i]);}
		// 		echo $pointsArray[$i]['x'].'<br/>' ;
	}
	return $vector;
	// 	return $pointsArray;
}
?>