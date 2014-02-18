<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.getJSON("data_output.php?type=detail&longi=<?php echo $_GET["longi"]?>&lati=<?php echo $_GET["lati"]?>",function(data){
				document.write("<table><tr><td>time</td><td>longitude</td><td>latitude</td><td>SINR</td><td>RSRP</td><td>PCI</td></tr>");
				for(var i=0;i<data.detaildata.length;i++){
						document.write("<tr><td>"+data.detaildata[i].DateTime+"</td>");
						document.write("<td>"+data.detaildata[i].Longitude+"</td>");
						document.write("<td>"+data.detaildata[i].Latitude+"</td>");
						document.write("<td>"+data.detaildata[i].PCC_RANK1_SINR+"</td>");
						document.write("<td>"+data.detaildata[i].Serving_Cell_RSRP+"</td>");
						document.write("<td>"+data.detaildata[i].Serving_Cell_PCI+"</td></tr>");
					}
					document.write("</table>");
			});
		});
	</script>