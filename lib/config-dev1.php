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

	$dbLogin = array ('dbhost' => "localhost", 'dbname' => "encoder-api-dev", 'dbusername' => "encoder-api-dev", 'dbuserpass' => "eRDpNpLs4tzz8GR6");

//___TIME ZONE_________________________________________________________________________________//

	date_default_timezone_set("Europe/London");

//___API_NAME_________________________________________________________________________________//

	$apiName = "encoder-api";
	$version = "dev";

//____SCP and FILE - SOURCE/DESTINATIONS_________________________________________________________________//

  $source = array(
  	'admin-scp' => 'admin-transfer-dev@podcast-admin-dev.open.ac.uk:/data/web/podcast-admin-dev.open.ac.uk/www/app/webroot/upload/files/',
  	'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/source/'
  );
  
  
  $destination = array(
  	'admin-scp' => 'admin-transfer-dev@podcast-api-dev.open.ac.uk:/data/web/podcast-api-dev.open.ac.uk/file-transfer/destination/',
  	'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/destination/',
	'encoder-api' => 'http://kmi-encoder-api-dev.open.ac.uk', 
   	'encoder-input' => '/Volumes/Data/Episode/EpisodeEngine/Inputs/',
 	'encoder-output' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/'
  );

//____Default workflow mapping _________________________________________________________________//

	$default = "-270p";

?>