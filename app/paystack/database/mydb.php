<?php
	$con = new mysqli('localhost:3308','root','','laravel_framework');
	if (!$con) {
		echo "Not connected to database".mysqli_error($con);
	}
?>