
<html>
<head>
  <title>Uploading...</title>
</head>
<body>
<h1>Uploading file...</h1>
<?php
require_once 'db_helper.php';
require_once 'config.php';
require_once 'file_helper.php';
set_time_limit(0);
$uploadFile = "uploads/" . $_FILES["userfile"]["name"];
upload_file('userfile',$uploadFile);

  //�������ݿ��ļ�
  db_connect();
  originalinfo_upload($uploadFile);
  
  // show what was uploaded

  file_get_contents("http://localhost/signalinfo/CalcAverVari.php");
?>
</body>
</html>