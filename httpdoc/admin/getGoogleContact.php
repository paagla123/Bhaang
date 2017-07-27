<?php

include 'global.php';

$callback = @$_GET['callback'];
$access_token = $_GET['access_token'];

echo $access_token;
echo "<br>";

$url = "https://www.google.com/m8/feeds/contacts/default/full?access_token=$access_token&alt=json";

$fileContent = file_get_contents($url);

$arr = (array) json_decode($fileContent, true);

$contactArray = array();
$name = "";
$email = "";
$contact = "";

foreach ($arr as $k=>$v){
    //echo "$k = $v <br>\n";
	foreach ($v as $k1=>$v1){
	    //echo "$k1 = $v1 <br>\n";
		if ($k1 == 'entry') {
			$name = "";
			$email = "";	
			
			foreach ($v1 as $k2=>$v2){
			    //echo "$k2 = $v2 <br>\n";				
				foreach ($v2 as $k3=>$v3){
				    //echo "$k3 = $v3 <br>\n";					
					if ($k3 == 'title') {
						foreach ($v3 as $k4=>$v4){
							//echo "$k4 = $v4 <br>\n";
							$firstname == "";
							$lastname == "";
							if ($k4 == '$t') {
								$name = $v4;
								echo "name = $name <br>\n";
								$pieces = explode(" ", $name);
								$size = sizeof($pieces);
								echo "size = $size <br>\n";
								var_dump($pieces);
								echo "<br>\n";									
								if ($size == 2) {
									$firstname = $pieces[0];
									$lastname = $pieces[1];
								} else if ($size == 1) {
									$firstname = $pieces[0];
									$lastname == "";
								} else {
									$firstname == "";
									$lastname == "";
								}
							}
						}
					} else if ($k3 == 'gd$email') {
						foreach ($v3 as $k4=>$v4){
							//echo "$k4 = $v4 <br>\n";
							foreach ($v4 as $k5=>$v5){
								//echo "$k5 = $v5 <br>\n";
								if ($k5 == 'address') {
									$email = $v5;
								} if ($k5 == 'primary') {
									$primary = $v5;
								}
							}
						}
					}
					
				}
				//echo "======================<br>\n";
				if ($primary == '') {
					$contactArray[] = array($firstname, $lastname, $email);
					$contact .= '"'.$firstname.'","'. $lastname.'","'. $email.'"\n';
				}
				$firstname = "";
				$lastname = "";
				$email = "";
				$primary = "";
			}			
		}
	}
}

$recordCount = sizeof($contactArray);

$importTime = date("YmdHis");

if ($contact != '') {
	$contact = '"firstname","lastname","email"\n'.$contact;
	
	$file = IMPORTPATH."google/".$importTime.".csv";
	echo "$file<br>\n";
	file_put_contents($file, $contact);
}

echo "======================<br>\n";
var_dump($contact);
echo "======================<br>\n";
echo $callback, '(', json_encode( array(
        'success'       => true,
        'message'       => '',
		'recordCount'	=> $recordCount
    )), ')';

