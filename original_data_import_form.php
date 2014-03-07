 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<script src="js/jquery-1.10.2.min.js"></script>
<title>数据管理</title>
</head>
<body>
<div id="file">
	<form action="original_data_import.php" method="post" enctype="multipart/form-data">
	<p><b>导入原始数据</b></p>
	<label for="userfile">文件:</label>
	<input type="file" name="userfile" id="userfile" />
	<div id="divofpeople">
		<label for="people">用户数:</label>
		<select id="people" name="people" >
			<option value=1 selected="selected">1</option>
			<option value=2>2</option>
			<option value=3>3</option>
			<option value=4>4</option>
			<option value=5>5</option>
			<option value=0>更多</option>
		</select>
	</div>
	</br>
	<input type="submit" value="上传" />
	</form>
</div>
</br></br>
<div id="resetGrid">
<form action="resetPrecision.php" method="post" enctype="multipart/form-data">
	<p><b>修改数据精度</b></p>
	<div id="divofprecision">
	<label for="people">栅格精度:</label>
	<select id="precision" name="precision" >
		<option value=0 selected="selected"></option>
		<option value=5>5</option>
		<option value=10>10</option>
		<option value=20>20</option>
		<option value=30>30</option>
		<option value=0>其他</option>
	</select>
	</div>
	<input type="submit" value="确定精度，重新生成数据库" />
</form>
</div>
</br></br>
<p><b>清除所有数据</b></p>
	<a href="data_clear.php">清除数据</a>
</body>
</html>
<script type="text/javascript">
$(function(){
	$("#precision option:first").text("现在精度为:"+<?php include_once 'params.php'; echo $_config['params']['ACCURACY_DEFAULT']; ?>);
	$("select").change(function(){
		if($(this).attr("id")=="people"){
			if($("#people").val()==0){
				$("#divofpeople").html("<label for='people'>用户数:</label> <input name='people' type='text' />");
			}
		}
		if($(this).attr("id")=="precision"){
			if($("#precision").val()==0){
				$("#divofprecision").html("<label for='people'>栅格精度:</label> <input name='precision' type='text' />");
			}
		}
	});
});
</script>