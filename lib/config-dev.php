<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  25th May,2011
	#	Encoder-api config  file 
/*========================================================================================*/

//___Debug_________________________________________________________________________________________//

//	ini_set(display_errors,On);
	$error='';
	$debug='';

//___DB CONNECTION_________________________________________________________________________________//

	$dbLogin = array ('dbhost' => "localhost", 'dbname' => "encoder-api-dev", 'dbusername' => "in625", 'dbuserpass' => "ge5HUQes");

//___TIME ZONE_________________________________________________________________________________//

	date_default_timezone_set("Europe/London");

//___API_NAME_________________________________________________________________________________//

	$apiName = "encoder-api";
	$version = "dev";

//____SCP and FILE - SOURCE/DESTINATIONS_________________________________________________________________//

  $source = array(
  	'admin-scp' => 'admin-transfer-dev@podcast-admin-dev.open.ac.uk:/data/web/podcast-admin-dev.open.ac.uk/www/app/webroot/upload/files/',
  	'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/source/',
  	'encoder-outbox' => 'outbox/'
  );

  //	'encoder-outbox' => 'outbox-dev/'

  
  $destination = array(
  	'admin-scp' => 'admin-transfer-dev@podcast-api-dev.open.ac.uk:/data/web/podcast-api-dev.open.ac.uk/file-transfer/destination/',
  	'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/destination/',
		'encoder-api' => 'http://kmi-encoder-api-dev.open.ac.uk', 
  	'eon-input' => 'admin@eon.open.ac.uk:/Library/Webserver/Documents/submit/eon-inbox/',
  	'encoder-inbox' => '/Volumes/Data/Encoder-Inbox-Dev/',
  	'encoder-input' => '/Volumes/Data/Episode/EpisodeEngine/Inputs/',
  	'encoder-output' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/'
  );

	// 	'encoder-inbox' => '/Volumes/Data/Encoder-Inbox/',

//____Default workflow mapping _________________________________________________________________//

	$default = "-270p";

?>