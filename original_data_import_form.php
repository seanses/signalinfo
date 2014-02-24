 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<title>Upload File</title>
</head>
<body>
	<form action="original_data_import.php" method="post" enctype="multipart/form-data">
	<p>导入原始数据</p>
	<label for="userfile">Filename:</label>
	<input type="file" name="userfile" id="userfile" />
	<label for="people">Number of users:</label>
	<select name="people">
	<option value=1 selected="selected">1</option>
	<option value=2>2</option>
	<option value=3>3</option>
	<option value=4>4</option>
	<option value=5>5</option>
	</select>
	</br>
	<input type="submit" value="Send File" />
	</form>
</body>
</html>