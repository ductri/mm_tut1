<?php
include 'sdes.php';
if(isset($_POST['submit'])) {
	$file_name = $_FILES["file2upload"]["name"];
	$file_size = $_FILES["file2upload"]["size"];
	
	print "file_name = $file_name<br>";
	print "file_size = $file_size<br>";
	encrypt_file($_FILES["file2upload"]["tmp_name"], 1023);
	
	print "<br><a href='result_ciphertext.txt'>Download file here</a>";
}
?>