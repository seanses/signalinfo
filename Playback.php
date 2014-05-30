<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
body, html, #allmap {
	width: 100%;
	margin: 0;
	overflow: visible
}

#l-map {
	height: 80%;
	width: 100%;
	float: left;
	border-right: 2px solid #bcbcbc;
}


</style>

<title>回放路线</title>
<link rel="stylesheet" href="css/bootstrap.css" />
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=YCwNZAoPRPGry3Gypi80S1ZL"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/DistanceTool/1.2/src/DistanceTool_min.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/bootstrap.js"></script>


</head>
<body>
	<div id="l-map"></div>
	<div style="overflow: auto; height:20%; width:100%">
		<table class="table table-striped" >
		<tr><td>用户名</td><td>最后位置</td><td>操作</td></tr>
		<?php
		require_once("db_helper.php");
		$result = get_collectors();
		for($i = 0; $i < count($result); $i++) {
			echo "<tr><td>" . $result[$i]['UserName'] . "</td>";
			echo "<td>" . $result[$i]['LastLatitude'] . "N," . $result[$i]['LastLongitude'] . "E</td>";
			$ID = $result[$i]['id'];
			echo "<td><button type=\"button\" onclick=\"i=0;showPath($ID);\">回放路线</button><td></tr>";
		}
		?>
		</table>
	</div>

	
	
</body>
<script type="text/javascript">
var map = new BMap.Map("l-map");
var point = new BMap.Point(116.633604,40.312968);  // ?????
var pointData;
var s=[];
var i=-1;
map.centerAndZoom(point, 40);                 // ?????��???��????????
map.enableScrollWheelZoom();

	$(document).ready(function (){
		$.getJSON("data_output.php?type=userPath",function(data){
			//alert(data.userPath.length);
			pointData = data.userPath;
		});
		});

function draw(){
	if(i<s.length-1){
		i++;
		var circle =s[i];
		circle.show();
		setTimeout("draw()",800);
	}	
}

function showPath(id){
	s=[];
	i=-1;
	map.clearOverlays();
	for(var i=0;i<pointData.length;i++){
		if(id == pointData[i].id){
			var pointArr = pointData[i].points;
			for(var j=0;j<pointArr.length;j++){
				circle_color = 	getColor(pointArr[j].PCC_RANK1_SINR);
				var centerpoint = new BMap.Point(pointArr[j].Longitude,pointArr[j].Latitude);
				var circle = new BMap.Circle(centerpoint,1.5,{strokeColor:circle_color, strokeWeight:3, strokeOpacity: 1, fillColor:circle_color, fillOpacity:1});
				s.push(circle);
				map.addOverlay(circle);
				circle.hide();	
				//setTimeout(function(){map.addOverlay(circle);},10);
				}				
		}	
	}
	draw();
}

function getColor(number){
	if(number <= -5 )
		return 'blue';
	else if(number >-5&& number<=0 )
		return '#4169E1';
	else if(number >0&& number<=5 )
        return '#00FFFF';	
	else if(number >5&& number<=10 )
        return '#00FF00';	
	else if(number >10&& number<=15 )
        return '#FFFF00';
	else if(number >15&& number<=20 )
        return '#FA8072';	
	else if(number >20&& number<=25 )
        return '#FF4500';	
	else if(number >25&& number<=30 )
        return 'red';
	else
		return '#DC143C';
}
</script>

</html>
