 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<script src="js/jquery-1.10.2.min.js"></script>
<title>���ݹ���</title>
</head>
<body>
<div id="file">
	<form action="original_data_import.php" method="post" enctype="multipart/form-data">
	<p><b>����ԭʼ����</b></p>
	<label for="userfile">�ļ�:</label>
	<input type="file" name="userfile" id="userfile" />
	<div id="divofpeople">
		<label for="people">�û���:</label>
		<select id="people" name="people" >
			<option value=1 selected="selected">1</option>
			<option value=2>2</option>
			<option value=3>3</option>
			<option value=4>4</option>
			<option value=5>5</option>
			<option value=0>����</option>
		</select>
	</div>
	</br>
	<input type="submit" value="�ϴ�" />
	</form>
</div>
</br></br>
<div id="resetGrid">
<form action="resetPrecision.php" method="post" enctype="multipart/form-data">
	<p><b>�޸����ݾ���</b></p>
	<div id="divofprecision">
	<label for="people">դ�񾫶�:</label>
	<select id="precision" name="precision" >
		<option value=0 selected="selected"></option>
		<option value=5>5</option>
		<option value=10>10</option>
		<option value=20>20</option>
		<option value=30>30</option>
		<option value=0>����</option>
	</select>
	</div>
	<input type="submit" value="ȷ�����ȣ������������ݿ�" />
</form>
</div>
</br></br>
<p><b>�����������</b></p>
	<a href="data_clear.php">�������</a>
</body>
</html>
<script type="text/javascript">
$(function(){
	$("#precision option:first").text("���ھ���Ϊ:"+<?php include_once 'params.php'; echo $_config['params']['ACCURACY_DEFAULT']; ?>);
	$("select").change(function(){
		if($(this).attr("id")=="people"){
			if($("#people").val()==0){
				$("#divofpeople").html("<label for='people'>�û���:</label> <input name='people' type='text' />");
			}
		}
		if($(this).attr("id")=="precision"){
			if($("#precision").val()==0){
				$("#divofprecision").html("<label for='people'>դ�񾫶�:</label> <input name='precision' type='text' />");
			}
		}
	});
});
</script>