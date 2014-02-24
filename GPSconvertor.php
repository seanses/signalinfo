<?php
require_once 'params.php';
require_once 'Tools.php';

function GPS2Grid($lng,$lat,$accuracy=30)
{
	$accuracy_lng = LastEffect(abs($accuracy*36/GREAT_CIRCLE_PERIMETER()/cos(deg2rad($lat))/100));
	$accuracy_lat = LastEffect(abs($accuracy*36/GREAT_CIRCLE_PERIMETER()/100));	
	
	return array("lng"=>floor($lng/$accuracy_lng)*$accuracy_lng,"lat"=>floor($lat/$accuracy_lat)*$accuracy_lat);
}
?>