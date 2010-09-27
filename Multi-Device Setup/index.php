<?php
include 'credentials/credentials.php';
$device = $_GET["device"];
$params = array('accountType' => 'GOOGLE', 'Email' => $user
'Passwd' => $pass, 'source'=>'PHP-cUrl-GoogleLogin', 'service'=>'reader');
if ($device == 1)
{
	$feed_url = "http://www.google.com/reader/api/0/unread-count"; //URL for Google Reader unread counts
	//Parameters required to login to Google
	
	//Login to Google
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$googleResponse = curl_exec($ch);
	curl_close ($ch);
	//Retrieve the SID and Auth parameters for further Google transactions
	$sid = substr($googleResponse,4,203); //I don't believe we need this, but left in
	$pos = strpos($googleResponse,"Auth=");
	$pos = $pos+5;
	$auth = substr($googleResponse,$pos); //This auth token must be passed with request. A new one is generated each time.
										  // it may raise eyebrows or trigger a shutdown on Google servers. Consider
										  // saving this token for 24 hours?
	
	//Connect to Google Reader and retrieve the unread counts
	$headers = array(
		"Authorization: GoogleLogin auth=" . $auth,
		"GData-Version: 3.0",
	);
	$agent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.7) Gecko/20100713 Firefox/3.6.7";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $feed_url);
	curl_setopt($curl, CURLOPT_USERAGENT, $agent);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
	$output = curl_exec($curl); //put the entire XML response into $output
	curl_close ($curl);
	
	//------- Parse the XML ----------

		//to do: put user specific strings into DB
	$xml = new SimpleXMLElement($output); //create a SimpleXML object out of this XML text
	$myID = "user/" . $readerId . "/state/com.google/reading-list";
	$path = "//object[string[@name='id'] = '$myID']/number[@name='count']"; //path to count
	
	$unreads = $xml->xpath($path);
	
	// to do: use mapping formula for changing ranges on meter.  Save ranges in DB.
	if ($unreads[0] <= 510)
		$val = $unreads[0] / 2;
	else
		$val = 255;
	echo chr($val);
		
}


if ($device == 2)
{
$authhost="{imap.gmail.com:993/imap/ssl/novalidate-cert}";
if ($mbox=imap_open( $authhost, $user, $pass ))
        {
			$check = imap_mailboxmsginfo($mbox);
			echo chr(25 * $check->Unread); 
        } else
        {
         	echo chr(0);
        }
}
?>
