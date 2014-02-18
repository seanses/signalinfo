<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=YCwNZAoPRPGry3Gypi80S1ZL"></script>
<title>添加多边形</title>
</head>
<body>
<div id="allmap"></div>
</body>
</html>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=YCwNZAoPRPGry3Gypi80S1ZL"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/DistanceTool/1.2/src/DistanceTool_min.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/latlon.js"></script>
<script src="js/jquery.fancybox.pack.js"></script>
<script type="text/javascript">
var map = new BMap.Map("allmap");
$(document).ready(function(){
    $.getJSON("data_output.php?type=region",function(data){
    	// 百度地图API功能
    	var point = new BMap.Point(data.regiondata[1][1].x, data.regiondata[1][1].y);
    	map.centerAndZoom(point, 17);
    	var myPoints = [];
        for(var i=0; i<data.regiondata.length; i++){
            var point_list = data.regiondata[i];
            for(var j=0; j<point_list.length;j++){
           	 	point = new BMap.Point(point_list[j].x, point_list[j].y);
             	myPoints.push(point);
            }
            var polygon = new BMap.Polygon(myPoints, {strokeColor:"blue", strokeWeight:6, strokeOpacity:0.5});
         	map.addOverlay(polygon);
        }
    });
});


</script>

