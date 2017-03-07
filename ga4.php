<?php

 /**
  * Sends a pageview to Google Analytics
  *
  * @param string $tId Your Google Tracking ID 
  *
  * @param string $hostname Specifies the hostname from which content was hosted
  *
  * @param string $path Tracking path
  *
  * @param string $title Tracking title
  *
  * @param string $source (optional) Source of request (web / app etc.) - default is 'web'
  *
  * @param string $connectTimeout (optional) How many seconds the script waits until a connection has been established - default is 2 seconds
  *
  * @return void
  */
function sendGA($tId, $hostname, $path, $title, $source="web", $connectTimeout=2) {
    $baseGAurl = 'http://www.google-analytics.com/collect?';
    $parameters = array();
		
	if(!function_exists( 'curl_init' ) ) {
		die("curl not available");
	}
	
	// Protocol Version
    $parameters['v'] = '1'; 
	// Google Tracking ID
    $parameters['tid'] = $tId; 
	// Data Source 
    $parameters['ds'] = $source;
	// Hit type 
    $parameters['t'] = "pageview";
    // Specifies the hostname from which content was hosted.
    $parameters['dh'] = $hostname; 
    // Tracking Path
    $parameters['dp'] = $path;
    //Tracking Title
    $parameters['dt'] = urlencode($title);
	// IP anonymization 
	$parameters['aip'] = '1'; 
	// Client ID "fingerprint"
	$parameters['cid'] = hash("sha256",$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'],false); 
    $parameters['qt'] = 0;
	// Override IP, else the origin would be the server itself
    $parameters['uip'] = $_SERVER['REMOTE_ADDR'];
	// Override user agent, for the same reason as above
    $parameters['ua'] = urlencode($_SERVER['HTTP_USER_AGENT']); 
    //Document Referrer
    if(isset( $_SERVER['HTTP_REFERER'] )){
		$parameters['dr'] = $_SERVER['HTTP_REFERER'];
	}

	$gaCall = $baseGAurl;
    foreach($parameters as $key => $value) {
        $gaCall.= "$key=$value&";
    }
    $gaCall = substr($gaCall, 0, -1); // remove last &
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gaCall);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Checking the server certificate is important
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout); // Seconds to wait while trying to connect
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);	// We do not really care about the response
	curl_exec($ch);
	curl_close($ch);
}
?>