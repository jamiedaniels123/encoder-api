<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Transfer Example</title>
	</head>
	<body>

  <?php 
  /*
  $source = array(
  	'admin' => 'admin-transfer-dev@podcast-api-dev.open.ac.uk:/data/web/podcast-api-dev.open.ac.uk/file-transfer/source/',
  	'media' => 'media-transfer-dev@media-podcast-api-dev.open.ac.uk:/data/web/media-podcast-api-dev.open.ac.uk/file-transfer/source/',
  	'encoder' => '/Volumes/Data/Episode/EpisodeEngine/Outputs/'
  );
  */

  // LOGGING EVENTS:
  // Code to allow logging to http://podcast-api.open.ac.uk/EVA/www/dashboard/logs
  // Once you've created an instance you can 
  require_once("logger.php");

  //$endpoint = "http://podcast-api.open.ac.uk/EVA/www/dashboard/logs/Events/";  // logging for live server
  $endpoint = "http://podcast-api.open.ac.uk/EVA/www/dashboard/logs/Events-Dev/";  // logging for Dev server
  
  global $logger;
  $logger = new Logger($endpoint);
  
  $logger->log($uuid, 102, "Transfering " . $_POST['file'], data());

  


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
  
  function transfer($src, $dest) {
  	
  	// Note: SCP is executed as user '_www' and it's home directory is /Library/WebServer/
  	// thus the secure keys are located in ~/.shh/ and the relevant key needs to be copied
  	// onto the relevant user account of each destination server.  Note: exact user/server set
  	// in global array $source and $destination.
  	
  	
    $cmdline = "/usr/bin/scp -vp ".escapeshellcmd($src)." ".escapeshellcmd($dest)." 2>&1";
  	echo "<p>Transfer cmd line =".$cmdline."</p>\n";  // debug
  	
  	//error_log("Transfer cmd line =".$cmdline);  // debug
  
  	exec($cmdline, $out, $code);
  
  	return array($code, $out);
  }
  
  // FILENAMES AND PATHS MUST NOT HAVE SPACES IN THEM
  
  //$worflowFolder="video-wide/";
  // $filename="dd205-mexico-hazardous-crossing-c.mov";
  
  //$worflowFolder="video/";
  //$filename="jamiestest_01.mp4";
  //$filename="dd205-mexico-economic-aspirations-480p.mov";
  /*
  $filepath="/Volumes/Data/Episode/EpisodeEngine/Outputs/".$worflowFolder.$filename;
  
 	if (chmod($filepath, 0664)) {
    echo "<p>Successfully set 0664 permissions on: ".$filepath."<p>\n";
  } else {
    $cmdline = "/usr/bin/chmod 664 ".escapeshellcmd($filepath)." 2>&1";
   	exec($cmdline, $out, $code);
    echo "Out=".$out."</br>\n";
    echo "Code=".$code."</br>\n";
    if ($code==0) {
      echo "<p>Successfully set 664 permissions (via CLI) on: ".$filepath."<p>\n";   
    } else {
      echo "<p>Failed to set 0664 permissions on: ".$filepath."<p>\n";    
    }
  }
  */
  $transfer = transfer($source['admin'].$filename, $destination['media'].$worflowFolder.$filename);
  //$transfer = transfer($source['encoder'].$worflowFolder.$filename, $destination['media'].$filename);
 
  print_r($transfer);
  echo "<br>\n";
  
  if ($transfer[0] == 0) {
    echo "<p>Transfered file successful</p>\n";
  } else {
    echo "<p>Failed to transfered file</p>\n";
  }
  
  ?>

	</body>
</html>
