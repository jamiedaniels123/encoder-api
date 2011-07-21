<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  25th May,2011
	#	Encoder-api config  file 
/*========================================================================================*/

//___Debug_________________________________________________________________________________________//

//	ini_set(display_errors,On);
	$error='';

//___DB CONNECTION_________________________________________________________________________________//

	$dbLogin = array ('dbhost' => "localhost", 'dbname' => "encoder-api", 'dbusername' => "in625", 'dbuserpass' => "ge5HUQes");

//___TIME ZONE_________________________________________________________________________________//

	date_default_timezone_set("Europe/London");

//____SCP SOURCE/DESTINATIONS_________________________________________________________________//

  $source = array(
  	'admin' => 'admin-transfer-dev@podcast-admin-dev.open.ac.uk:/data/web/podcast-admin-dev.open.ac.uk/www/app/webroot/upload/files/',
  	'media' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/source/',
  	'encoder' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/'
  );
  
  
  $destination = array(
  	'admin' => 'admin-transfer-dev@podcast-api-dev.open.ac.uk:/data/web/podcast-api-dev.open.ac.uk/file-transfer/destination/',
  	'media' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/destination/',
  	'encoder' => '/Volumes/Data/Episode/EpisodeEngine/Inputs/'
  );

?>