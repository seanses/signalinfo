<?php 
		$cureent_dir = 'uploads/path';
		$dir = opendir($cureent_dir);
		echo "<p>Upload directory is $cureent_dir</p>";
		echo '<p>Directory Listening:</p><ul>';
		while (false !== ($file = readdir($dir))){
			if($file != "." && $file != ".."){
				echo "<li>$file</li>";
		}
		}
		echo '</ul>';
		closedir($dir);
?>