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

// WORKFLOW OUTPUTS
// BH 20120409 - added to help tracking of what outputs for a given workflow are expected to be created these correspond
//               to the wm_outputfile field in the workflow_map1 table and should not be confused with the final flavours
//               created on the media server.
//
//		$workflow_outputs[workflow:[output:status]]
//		
//		status =	queued (default)
//							encoding
//							transfering
//							done
//							failed
//
// Note: a) only supports the basic workflows as -watermark an -watermark-trailers variants have been dropped as now encoded anyway
//       b) TODO: audio-trailers and audio-no-trailers workflows need converting to a single audio workflow that creates all flavours required
//       c) 'workflow_outputs' lists the output files encoded not the final 'media_type' on podcast server which. Some outputs will map to map
//          to multiple media_types (aka flavours) via workflow_map DB table field 'wm_flavour'


	$workflow_outputs = array(
		'audio-no-trailers' => array('-mp3'=>'queued'),
		'audio-trailers' => array('-mp3'=>'queued',
															'-trailers_mp3'=>'queued'),
		'video-270' 		 => array('-240'=>'queued',
															'-270'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-youtube'=>'queued'),
		'video-360' 		 => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-youtube'=>'queued'),
		'video-480' 		 => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-youtube'=>'queued'),
		'video-576' 		 => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-540'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-large'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-270' => array('-240'=>'queued',
															'-270'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-360' => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-480' => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-576' => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-540'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-large'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-720' => array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-540'=>'queued',
															'-720'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-large'=>'queued',
															'-hd'=>'queued',
															'-youtube'=>'queued'),
		'video-wide-1080'=> array('-240'=>'queued',
															'-270'=>'queued',
															'-360'=>'queued',
															'-480'=>'queued',
															'-540'=>'queued',
															'-720'=>'queued',
															'-1080'=>'queued',
															'-ipod'=>'queued',
															'-iphone'=>'queued',
															'-iphonecellular'=>'queued',
															'-desktop'=>'queued',
															'-large'=>'queued',
															'-hd'=>'queued',
															'-hd_1080'=>'queued',
															'-youtube'=>'queued')
	);


//____SCP and FILE - SOURCE/DESTINATIONS_________________________________________________________________//

  $source = array(
		'admin-scp' => 'admin-transfer-dev@podcast-admin-dev.open.ac.uk:/data/web/podcast-admin-dev.open.ac.uk/www/app/webroot/upload/files/',
		'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/source/',
		'encoder-outbox' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/outbox/'
  );

  //	'encoder-outbox' => 'outbox-dev/'
  
  
  $destination = array(
		'admin-scp' => 'admin-transfer-dev@podcast-api-dev.open.ac.uk:/data/web/podcast-api-dev.open.ac.uk/file-transfer/destination/',
		'media-scp' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/destination/',
		'encoder-api' => 'http://kmi-encoder-api-dev.open.ac.uk', 
		'eon-input' => 'admin@eon.open.ac.uk:/Library/Webserver/Documents/submit/eon-inbox/',
		'encoder-inbox' => '/Volumes/Data/Encoder-Inbox/',
		'encoder-outbox' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/outbox/',
		'encoder-input' => '/Volumes/Data/Episode/EpisodeEngine/Inputs/',
		'encoder-output' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/',
		'encoder-failed-transfer' => '/Volumes/Data/Episode/EpisodeEngine/Failed-transfer/'
  );

//   	'encoder-inbox' => '/Volumes/Data/Encoder-Inbox-dev/',

//____Default workflow mapping _________________________________________________________________//

	$default = "-270p";

?>