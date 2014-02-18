<?php
require_once 'db_helper.php';
require_once 'file_helper.php';
require_once 'output_form.php';
$uploadFile = "uploads/path/" . $_FILES["userfile"]["name"];
upload_file('userfile',$uploadFile);
show_uploaded_path();
?>