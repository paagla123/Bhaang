<?php

$group_id = "6"; // Used as the default 'My Contacts' group.

/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
require_once '../src/Google_Client.php';// MAKE SURE TO CHANGE THIS TO YOUR LOCATION

session_start();

$client = new Google_Client();
$client->setApplicationName("Google Apps PHP Starter Application");
$client->setScopes(array(
    'https://apps-apis.google.com/a/feeds/groups/',
    'https://apps-apis.google.com/a/feeds/alias/',
    'https://apps-apis.google.com/a/feeds/user/',
	'https://www.google.com/m8/feeds/',
	'https://www.google.com/m8/feeds/user/',
));

// Documentation: http://code.google.com/googleapps/domain/provisioning_API_v2_developers_guide.html
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.

 $client->setClientId('YOUR_CLIENT_ID');
 $client->setClientSecret('YOUR_SECRET');
 $client->setRedirectUri('https://PATH_TO_YOUR_URL');
 $client->setDeveloperKey('DEVELOPER_KEY');

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token'])) {
 
	$client->setAccessToken($_SESSION['access_token']);
	$token = json_decode($_SESSION['access_token']);
						 
						 
 	$auth_pass = $token->access_token;
	


	//Get Email of User ------------------------------------
	// You are now logged in
	// We need the users email address for later use. We can get that here.
	
	
	$req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full");
    $req->setRequestHeaders(array('GData-Version'=> '3.0','content-type'=>'application/atom+xml; charset=UTF-8; type=feed'));
	
	$val = $client->getIo()->authenticatedRequest($req);

	  // The contacts api only returns XML responses.
	  $response =$val->getResponseBody();
	  
	$xml = simplexml_load_string($response); // Convert to an ARRAY
	
	$user_email = urlencode($xml->id); // email address
	unset($xml); // clean-up
//-------------------------------------
	// How to save an entry to your My Contacts List
	
	// This is an example contact XML that Google is looking for.
	

	$contact="
	<atom:entry xmlns:atom='http://www.w3.org/2005/Atom'
		xmlns:gd='http://schemas.google.com/g/2005'
		xmlns:gContact='http://schemas.google.com/contact/2008'>
	  <atom:category scheme='http://schemas.google.com/g/2005#kind'
		term='http://schemas.google.com/contact/2008#contact'/>
	  <gd:name>
		 <gd:givenName>HELLO</gd:givenName>
		 <gd:familyName>WORLD</gd:familyName>
		 <gd:fullName>Hello World</gd:fullName>
	  </gd:name>
	  <atom:content type='text'>Notes</atom:content>
	  <gd:email rel='http://schemas.google.com/g/2005#work'
		primary='true'
		address='liz@gmail.com' displayName='E. Bennet'/>
	  <gd:email rel='http://schemas.google.com/g/2005#home'
		address='liz@example.org'/>
	  <gd:phoneNumber rel='http://schemas.google.com/g/2005#work'
		primary='true'>
		(206)555-1212
	  </gd:phoneNumber>
	  <gd:phoneNumber rel='http://schemas.google.com/g/2005#home'>
		(206)555-1213
	  </gd:phoneNumber>
	  <gd:im address='liz@gmail.com'
		protocol='http://schemas.google.com/g/2005#GOOGLE_TALK'
		primary='true'
		rel='http://schemas.google.com/g/2005#home'/>
	  <gd:structuredPostalAddress
		  rel='http://schemas.google.com/g/2005#work'
		  primary='true'>
		<gd:city>Mountain View</gd:city>
		<gd:street>1600 Amphitheatre Pkwy</gd:street>
		<gd:region>CA</gd:region>
		<gd:postcode>94043</gd:postcode>
		<gd:country>United States</gd:country>
		<gd:formattedAddress>
		  1600 Amphitheatre Pkwy Mountain View
		</gd:formattedAddress>
	  </gd:structuredPostalAddress>
	 <gContact:groupMembershipInfo deleted='false'
			href='http://www.google.com/m8/feeds/groups/".$user_email."/base/6'/>
	</atom:entry>
	";


	$len = strlen($contact);
	$add = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/".$user_email."/full/");
	$add->setRequestMethod("POST");
	$add->setPostBody($contact);
	$add->setRequestHeaders(array('content-length' => $len, 'GData-Version'=> '3.0','content-type'=>'application/atom+xml; charset=UTF-8; type=feed'));
	
	$submit = $client->getIo()->authenticatedRequest($add);
	$sub_response = $submit->getResponseBody();
	
	
	$parsed = simplexml_load_string($sub_response);	
	$client_id = explode("base/",$parsed->id);

// Contact Groups -------------------------------------------------
	// This section will collect all the groups for this user for contact sorting.
	// For now, I have set the default group to "My Contacts" of that user.
	
	
	$group="http%3A%2F%2Fwww.google.com%2Fm8%2Ffeeds%2Fgroups%2F".$user_email."%2Fbase%2F6";
	
	
	//Get Contacts by Group -------------------------------------------------------------------
	// Now we request the users contacts based on group. For now, we will retreive 'My Contacts'
	
	
	$req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/".$user_email."/full?group=".$group);
    $req->setRequestHeaders(array('GData-Version'=> '3.0','content-type'=>'application/atom+xml; charset=UTF-8; type=feed'));
	$val = $client->getIo()->authenticatedRequest($req);

	  // The contacts api only returns XML responses.
	  $response =$val->getResponseBody();
	  
	
		$xml = simplexml_load_string($response); // Convert the response to an ARRAY
		
	//print_r($xml);
		
			echo "Group: ".$xml->title."<br>";
			echo "Email: ".$xml->id."<br>";
			echo "<hr><br>";
			
			for($i = 0; $i <= sizeof($xml->entry); $i++)
				
				{
					
				echo $xml->entry[$i]->title."<br>";
					
				}

	
	
 // The access token may have been updated lazily.
  $_SESSION['access_token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
}

if(isset($authUrl)) {
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
} else {
 print "<a class='logout' href='?logout'>Logout</a>";
}
