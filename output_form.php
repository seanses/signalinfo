<?php
require_once 'file_helper.php';
function show_uploaded_path() {
	echo '<h1>������ϴ�·���ļ��ļ�</h1>';
	$cureent_dir = 'uploads/path';
	$dir = opendir ( $cureent_dir );
	echo "<p>Upload directory is $cureent_dir</p>";
	echo '<p>Directory Listening:</p><ul>';
	while ( false !== ($file = readdir ( $dir )) ) {
		if ($file != "." && $file != "..") {
			echo "<li><a href=\"show_signal_path.php?file=$file\" >$file</a></li>";
		}
	}
	echo '</ul>';
	closedir ( $dir );
}
?>