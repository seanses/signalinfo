<?php
error_reporting(0);
$con = mysql_connect("localhost","root","") or die('Could not connect' . mysql_error());
mysql_select_db("signalinfo",$con);
?>