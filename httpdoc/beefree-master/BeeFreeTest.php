<?php
	include ('BeeFree.php');

	$beefree = new BeeFree('ba3179ff-d3dc-43cf-9389-55527a24c50c', 'TQeZUREjYmhQcrAqOHGQIi2gGOj5cMHvVVWEgPGt7E01lCrodaR'); 
    $result = $beefree -> getCredentials();

	var_dump ($result);