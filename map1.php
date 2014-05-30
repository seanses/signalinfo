<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
body, html, #allmap {
	width: 100%;
	margin: 0;
	overflow: hidden
}

#l-map {
	height: 100%;
	width: 100%;
	border-right: 2px solid #bcbcbc;
}

#r-result {
	height: 600px;
	width: 21%;
	float: left;
}
#color{
 position:float;
 width:30%;
}
</style>
<link rel="stylesheet" href="css/jquery.fancybox.css" type="text/css"
	media="screen" />

<title>index</title>
</head>
<body>
    <span style="position:absolute;z-index:1"><img id="color" src="img/color.jpg"/></span>
	<div id="l-map"></div>
	<!-- <div id="r-result" style="overflow:scroll" ></div> -->
	<div id="data">
		<input id="chartdata" type="hidden" />
	</div>
	<div id="information"></div>
</body>
<!-- 加载js文件 -->
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=YCwNZAoPRPGry3Gypi80S1ZL"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/DistanceTool/1.2/src/DistanceTool_min.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/latlon.js"></script>
<script src="js/jquery.fancybox.pack.js"></script>
<script src="js/jsMapTools.js"></script>

</html>
<script type="text/javascript">

var jq = $.noConflict();
//百度地图API功能
var map = new BMap.Map("l-map");          // 创建地图实例
var point = new BMap.Point(116.633604,40.312968);  // 创建点坐标
var circle_color;
var myPoints = [];
var regionoverlay = [];
var polygonList = []
var pciNumList = [];
var backpoint;
map.centerAndZoom(point, 40);                 // 初始化地图，设置中心点坐标和地图级别
map.enableScrollWheelZoom();



/**************************
*    start  zhangyichi    *
**************************/
////draw the imformation in the database
jq(document).ready(function(){
	//draw cell coverage area on the map
	jq.getJSON("data_output.php?type=region", function(data) {
		for(var i = 0; i < data.regiondata.length; i++) {
			addPCIRegion(data.regiondata[i].pointList,data.regiondata[i].color,data.regiondata[i].pci);	
		}
	});
	
    
	
	//draw original data on the map
	function refreshpoints(){
		for(p in myPoints)	map.removeOverlay(p);
		myPoints = [];
		jq.getJSON("data_output.php?type=original",function(data){
			for(var i=0;i<data.originaldata.length;i++){
				circle_color = 	getColor(data.originaldata[i].PCC_RANK1_SINR_AVERAGE);
				var centerpoint = new BMap.Point(data.originaldata[i].Longitude,data.originaldata[i].Latitude);
				var circle = new BMap.Circle(centerpoint,1.5,{strokeColor:circle_color, strokeWeight:3, strokeOpacity: 1, fillColor:circle_color, fillOpacity:1});
				myPoints.push(circle);
				map.addOverlay(circle);
			}
			for(var i =0; i <data.originaldata.length; i ++) {
				(function(){
					var oridata = data.originaldata[i];
					
					myPoints[i].addEventListener('click', function(){
						var longi = this.getCenter().lng;
						var lati = this.getCenter().lat;
						var p = this;
				
						jq.get("data_output.php?type=detail&longi="+ longi +"&lati="+lati,function(jsonback,status){
							var sContent;

							var back = JSON.parse(jsonback);
							sContent ="<div><button onclick='deletePoint("+oridata.Gridid+");' />删除这个点</button></div>"+"<div style='overflow: auto; height: 250px'><h4 style='margin:0 0 5px 0;padding:0.2em 0'>详细信息</h4>" +
										 "<table border='1';text-align='center'><tr><th>时间</th><th>经度</th><th>纬度</th><th>PCI</th><th>SINR(dB)</th><th>RSRP(dB)</th><th>下行吞吐量(Kbps)</th></tr>";
							   
							for(var j=0;j<back.detaildata.length;j++){
								sContent +="<tr><td align='center'>"+back.detaildata[j].DateTime+"</td>"+
								"<td align='center'>"+back.detaildata[j].Longitude+"</td>"+
								"<td align='center'>"+back.detaildata[j].Latitude+"</td>"+
								"<td align='center'>"+back.detaildata[j].Serving_Cell_PCI+"</td>"+
								"<td align='center'>"+back.detaildata[j].PCC_RANK1_SINR+"</td>"+
								"<td align='center'>"+back.detaildata[j].Serving_Cell_RSRP+"</td>"+
								"<td align='center'>"+back.detaildata[j].PDCP_Throughput_DL+"</td></tr>";
							}
							sContent +="</table><div>";
							   
							var opts = {width:1200}    // 信息窗口宽度
							   
							var infoWindow = new BMap.InfoWindow(sContent,opts);
							
							var center = p.getCenter();
							map.openInfoWindow(infoWindow,center); //开启信息窗口	 
						  
						});
					});
											
					var centerpoint = new BMap.Point(oridata.Longitude,oridata.Latitude);
					var hotSpot = new BMap.Hotspot(centerpoint, {text: "PCI: "+oridata.PCI+
													"<br />SINR平均值: "+oridata.PCC_RANK1_SINR_AVERAGE+
													"dB<br />RSRP平均值: "+oridata.SERVING_CELL_RSRP_AVERAGE+
													"dB<br />下行吞吐量平均值: "+oridata.PDCP_Throughput_DL_AVERAGE+"Kbps",
													offsets:[50,50,50,50]});
					map.addHotspot(hotSpot);	
				})();
			}	
		});
		//setTimeout(refreshpoints,10000);
	}
	
	refreshpoints();
	
	//draw the baseStation 
 	jq.getJSON("data_output.php?type=baseStation",function(data){ 	
	    for(var i=0; i<data.baseStation.length; i++){
		    var baseStationData = {
				    "Longitude" : data.baseStation[i].Longitude,
				    "Latitude" : data.baseStation[i].Latitude,
				    "Radius" : data.baseStation[i].Radius,
					"Name" : data.baseStation[i].Name,
					"PCI1" : data.baseStation[i].PCI1,
				    "Degree1" : parseInt(data.baseStation[i].Degree1),
				    "Degree2" : parseInt(data.baseStation[i].Degree2),
					"PCI2" : data.baseStation[i].PCI2,
				    "Degree3" : parseInt(data.baseStation[i].Degree3),
				    "Degree4" : parseInt(data.baseStation[i].Degree4),
					"PCI3" : data.baseStation[i].PCI3,
				    "Degree5" : parseInt(data.baseStation[i].Degree5),
				    "Degree6" : parseInt(data.baseStation[i].Degree6)
		    };
		    drawBaseStation(baseStationData);
	    }
	}); 
	
    
	//draw collectors on the map
	function resetMK(){
		jq.getJSON("data_output.php?type=collector&last=-1",function(data){
			for(co in data.collector){
				var myIcon = new BMap.Icon("img/collector.png", new BMap.Size(43, 33), {    
					imageOffset: new BMap.Size(0, 0)    //图片的偏移量。为了是图片底部中心对准坐标点。
				});
				//window.open(myIcon.imageUrl);
				var carstart = new BMap.Point(data.collector[co].Llongitude,data.collector[co].Llatitude);
				var carMk = new BMap.Marker(carstart,{icon:myIcon});
				carMk.setLabel(new BMap.Label(data.collector[co].username,{offset:new BMap.Size(20,-18)}));
				map.addOverlay(carMk);
			}
		});
		// setTimeout(function(){
			// resetMK();
		// },10000);
	}
	
	resetMK();
});

/*
 * 定时刷新collector位置 
 */
// function resetMkPoint(i,len,pts,carMk){
	// carMk.setPosition(pts[i]);
	// if(i < len){
		// setTimeout(function(){
			// i++;
			// resetMkPoint(i,len,pts,carMk);
		// },100);
	// }
// }

/**
 * 绘制region
 */
function drawRegion(pci_list){
    	for(var j=0; j<regionoverlay.length; j++){ 					//清除原来画出的PCI区域
			var x = regionoverlay[j];
    		map.removeOverlay(polygonList[x]);
    	}
    	regionoverlay=[];
    	for(var i = 0; i < pci_list.length; i++) {
			for(var m = 0; m < pciNumList.length; m++) {
				if(pci_list[i] == pciNumList[m]) {
					regionoverlay.push(m);	
					map.addOverlay(polygonList[m]);
				}
			}	
        }
}

/**
 * add pci region overlay
 */
function addPCIRegion(pointList,color,pci){
	var myPoints = [];
    for(var j=0; j<pointList.length; j++) {
        point = new BMap.Point(pointList[j].x, pointList[j].y);
        myPoints.push(point);
    }
    var polygon= new BMap.Polygon(myPoints, {fillColor:color, strokeColor:color, StrokeWeight:1, fillOpacity:0.3, strokeOpacity:0.00001});
    polygonList.push(polygon);
    pciNumList.push(pci);

}

/**
 * 清除已绘制的PCIregion
 */
function clearRegion(){
	for(var j=0; j<regionoverlay.length; j++){ 					//清除原来画出的PCI区域
		map.removeOverlay(polygonList[j]);
	}
	regionoverlay=[];
}


//删除点
function deletePoint(grid){
	jq.get("deletePoint.php?grid="+grid,function(data,status){
		alert("删除成功");
		location.reload();
	});
}

/**************************
*    end  zhangyichi      *
**************************/

/**************************
*    start  xiaodi        *
**************************/

//封装一条路径中的一个点
//lng-经度 lat-纬度 dit-与起点的距离
function OneStep(lng,lat,dit){
	this.lng = lng;
	this.lat = lat;
	this.dit = dit;
}

function show(str){
	document.getElementById("r-result").innerHTML = str;
}

function openchartcallback(){
	jq.fancybox({
		type: 'iframe',
		href: 'chart.html',
		closeBtn: true,	
		'autoDimensions'	: false,
		'width'         		: 1200,
		'height'        		: 1000,
		'autoScale' : false
	});
}

function exportcsvfile(){
	window.open(JSON.parse(jq("#chartdata").val())[10]);
}

function findMids(p1,p2,d0){
	var sp = [];
	if(p1.distanceTo(p2)>=<?php include_once 'params.php'; echo $_config['params']['ACCURACY_DEFAULT']; ?>){
		var mid = p1.midpointTo(p2);
		sp=sp.concat(findMids(p1,mid,d0));
		sp.push(new OneStep(mid._lon,mid._lat,d0+p1.distanceTo(mid)));
	}
	return sp;
}

var contextMenu = new BMap.ContextMenu();
var startmarker, endmarker, startpoint, endpoint;
var txtMenuItem = [
  {
   text:'此处为起点',
   callback:function(p){
	if(startpoint!=undefined){
		startmarker.hide();
	}
	startpoint = p;
    startmarker = new BMap.Marker(p), px = map.pointToPixel(p);
    map.addOverlay(startmarker);
	if(endpoint!=undefined){
		startmarker.hide();
		endmarker.hide();
		driving.search(startpoint, endpoint);
		//startpoint = endpoint = undefined;
	}
   }
  },
  {
   text:'此处为终点',
   callback:function(p){
	if(endpoint!=undefined){
		endmarker.hide();
	}
	endpoint =  p;
    endmarker = new BMap.Marker(p), px = map.pointToPixel(p);
    map.addOverlay(endmarker);
	if(startpoint!=undefined){
		startmarker.hide();
		endmarker.hide();
		driving.search(startpoint, endpoint);
		//startpoint = endpoint = undefined;
	}
   }
  }
 ];
 
 for(var i=0; i < txtMenuItem.length; i++){
  contextMenu.addItem(new BMap.MenuItem(txtMenuItem[i].text,txtMenuItem[i].callback,100));
 }
 map.addContextMenu(contextMenu);

var options = {
	renderOptions: {map: map, autoViewport: true, enableDragging: true},
	onSearchComplete: function(res){
		var path = res.getPlan(0).getRoute(0).getPath();
		var steps = [];
		var str = [];
		var dit = 0;
		for(var i=0;i<path.length;i++){
			steps.push(new OneStep(path[i].lng,path[i].lat,dit));
			//show to the r-result panel
			str.push(path[i].lng+" "+path[i].lat+" "+dit);
			<!-- map.addOverlay(new BMap.Circle(path[i],1)); -->
			
			//if not last step
			if(i!=path.length-1){
				var p1 = new LatLon(path[i].lat,path[i].lng);
				var p2 = new LatLon(path[i+1].lat,path[i+1].lng);	
				steps = steps.concat(findMids(p1,p2,dit));
				dit += p1.distanceTo(p2);
			}
		}
		//show on the r-result panel
		<!-- document.getElementById("r-result").innerHTML = path.length + "<br />" + res.getPlan(0).getRoute(0).getDistance(false) + "<br />" + str.join("<br/>"); -->
		
		var steps_json = JSON.stringify(steps);
		
		function searchP(){
			jq.post("SearchRoute.php",
			{
				data: steps_json
			},
			function(data,status){
				if(status=="success"){
					jq('#chartdata').val(data);
				
				}else{
					alert("Fail to fetch data.");
				}
			});
			
			//setTimeout(searchP,300000);
		}
		
		searchP();
		
	}
};
var driving = new BMap.DrivingRoute(map, options);



/**************************
*    end    xiaodi        *
**************************/


/**************************
*    start     wushan     *
**************************/

/**
 * the function to draw the sector of pci
 */
function Sector1(point2, radius, sDegree, eDegree, strokeColour, strokeWeight, Strokepacity, fillColour, fillOpacity) {
	var points = [];
    var step = ((eDegree - sDegree) / 10) || 10;
    points.push(point2);
    for ( var i = sDegree; i < eDegree + 0.001; i += step) {
        points.push(EOffsetBearing(point2, radius, i));
    }
    points.push(point2);
    var polygon = new BMap.Polygon(
		points, 
		{strokeColor:strokeColour, strokeWeight:strokeWeight, strokeOpacity:Strokepacity, fillColor: fillColour, fillOpacity:fillOpacity}
	);
    
    return polygon;
}

/**
 * the function to convert the point to baidu map's point
 */
function EOffsetBearing(point3, dist, bearing) {
    var latConv = map.getDistance(point3, new BMap.Point(point3.lng + 0.1, point3.lat)) * 10;
    var lngConv = map.getDistance(point3, new BMap.Point(point3.lng, point3.lat + 0.1)) * 6;
    var lat = dist * Math.cos(bearing * Math.PI / 180) / latConv;
    var lng = dist * Math.sin(bearing * Math.PI / 180) / lngConv;
    return new BMap.Point(point3.lng + lng, point3.lat + lat);
}

function Median(point,radius,start,end)
{
	var middleangle =(start+(end-start)/2);
	var middlepoint = EOffsetBearing(point,radius/2,middleangle);
	return middlepoint;
}

function moveon_out(polygon,station,PCI,middlepoint){

	polygon.addEventListener("mouseover",function(){map.addOverlay(Label);});

	polygon.addEventListener("mouseout",function(){map.removeOverlay(Label);});  
	var Label = new BMap.Label("<b>"+station+"</b></br>PCI:"+PCI+"</br>",{position:middlepoint});  //new BMap.Point(x,y)
	Label.setStyle({"z-index":"999999", "padding": "10px","width": "100px","border": "1px solid #ccff00"}); 
}

/**
 * function to draw the baseStation 
 */
function drawBaseStation(baseStationData){	
	var point = new BMap.Point(baseStationData.Longitude,baseStationData.Latitude);
	
	var opts = {
		position : point,    // 指定文本标注所在的地理位置
		//offset   : new BMap.Size(30, -30)    //设置文本偏移量
      }
    var label = new BMap.Label(baseStationData.Name, opts);  // 创建文本标注对象
	label.setStyle({
		 color : "blue",
		 fontSize : "20px",
		 height : "20px",
		 lineHeight : "20px",
		 fontFamily:"微软雅黑",
		 borderColor : "#ffff00"
	 });
    map.addOverlay(label);   
	
	var polygon1 = Sector1(point,baseStationData.Radius,baseStationData.Degree1,baseStationData.Degree2, "#ffff00", 3, 0.5, "#00ff00", 0.5);
	map.addOverlay(polygon1);
	var middlepoint1 = Median(point,baseStationData.Radius,parseInt(baseStationData.Degree1),parseInt( baseStationData.Degree2));
	moveon_out(polygon1,baseStationData.Name,baseStationData.PCI1,middlepoint1);
	
	
	var polygon2 = Sector1(point,baseStationData.Radius,baseStationData.Degree3,baseStationData.Degree4, "#ffff00", 3, 0.5, "#00ff00", 0.5);//90, 150
	map.addOverlay(polygon2);
	var middlepoint2 = Median(point,baseStationData.Radius,parseInt(baseStationData.Degree3),parseInt( baseStationData.Degree4));
	moveon_out(polygon2,baseStationData.Name,baseStationData.PCI2,middlepoint2);
	
	
	var polygon3 = Sector1(point,baseStationData.Radius,baseStationData.Degree5,baseStationData.Degree6, "#ffff00", 3, 0.5, "#00ff00", 0.5);//30, 90
	map.addOverlay(polygon3);
	var middlepoint3 = Median(point,baseStationData.Radius,parseInt(baseStationData.Degree5),parseInt( baseStationData.Degree6));
	moveon_out(polygon3,baseStationData.Name,baseStationData.PCI3,middlepoint3);
	
}
/* 
//怀柔宏怀热力
var point2 = new BMap.Point(116.63590,40.30915);
var polygon21 = Sector1(point2, 50,275,335, "#ffff00", 3, 0.5, "#00ff00", 0.5);//270, 330
map.addOverlay(polygon21);
moveon_out(polygon21,"怀柔宏怀热力",103,116.63520,40.30985);
var polygon22 = Sector1(point2, 50,65,125, "#ffff00", 3, 0.5, "#00ff00", 0.5);//150, 210
map.addOverlay(polygon22);
moveon_out(polygon22,"怀柔宏怀热力",105,116.63660,40.30915);
var polygon23 = Sector1(point2, 50,125,185, "#ffff00", 3, 0.5, "#00ff00", 0.5);//80, 140
map.addOverlay(polygon23);
moveon_out(polygon23,"怀柔宏怀热力",104,116.63590,40.30865);

//怀柔仁和通办公楼
var point3 = new BMap.Point(116.636772,40.322987);
var polygon31 = Sector1(point3, 50,300,360 , "#ffff00", 3, 0.5, "#00ff00", 0.5);//30, 90
map.addOverlay(polygon31);
moveon_out(polygon31,"怀柔仁和通办公楼",106,116.636672,40.323787);//106还是107
var polygon32 = Sector1(point3, 50,90,150, "#ffff00", 3, 0.5, "#00ff00", 0.5);//270, 330
map.addOverlay(polygon32);
moveon_out(polygon32,"怀柔仁和通办公楼",107,116.637272,40.322887);//106还是107
var polygon33 = Sector1(point3, 50,150,210, "#ffff00", 3, 0.5, "#00ff00", 0.5);//170, 230
map.addOverlay(polygon33);
moveon_out(polygon33,"怀柔仁和通办公楼",108,116.636772,40.322472);

//怀柔文化馆
var point4 = new BMap.Point(116.637725,40.317957);
var polygon41 = Sector1(point4, 50,15,75, "#ffff00", 3, 0.5, "#00ff00", 0.5);//270, 330
map.addOverlay(polygon41);
moveon_out(polygon41,"怀柔文化馆",111,116.638125,40.318357);
var polygon42 = Sector1(point4, 50,315,375, "#ffff00", 3, 0.5, "#00ff00", 0.5);//180, 240
map.addOverlay(polygon42);
moveon_out(polygon42,"怀柔文化馆",109,116.637725,40.318657);
var polygon43 = Sector1(point4, 50,150,210,"#ffff00", 3, 0.5, "#00ff00", 0.5);//30, 90
map.addOverlay(polygon43);
moveon_out(polygon43,"怀柔文化馆",110,116.637725,40.317457); */

/**************************
*    end     wushan       *
**************************/


</script>