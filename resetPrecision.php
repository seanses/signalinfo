<?php
require_once 'MysqlConnection.php';
include_once 'params.php';

if($_POST["precision"]==0){
	echo ���Ȳ���Ϊ0;
}
else{
	mysql_query("truncate processedinfo");
	mysql_query("truncate baidubasedinfo");
	$str = file_get_contents("params.php");
	$str = str_replace('$_config[\'params\'][\'ACCURACY_DEFAULT\'] = ' . $_config['params']['ACCURACY_DEFAULT'] ,'$_config[\'params\'][\'ACCURACY_DEFAULT\'] = ' . $_POST["precision"],$str);
	file_put_contents("params.php",$str);
	file_get_contents("http://localhost/signalinfo/CalcAverVari.php");
	echo �����������ݿ�ɹ� . "   <a href='http://localhost/signalinfo/index.html'>�ص���ҳ</a>";
}
?>