<?php
	date_default_timezone_set('America/Chicago');

	$importTime = date("YmdHis");
	$contact = "12345";
	$file = "/var/www/html/admin/import/google/".$importTime.".csv";
	echo "$file<br>\n";
	file_put_contents($file, $contact);