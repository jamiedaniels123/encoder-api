<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  24th May,2011
	#	Test version  
	#	controller to process actions queued in the media_actions table and report status to the admin server
\*=========================================================================================*/

	$start_time=microtime(true);

	require_once("lib/config.php");
	require_once("lib/classes/polling.class.php");
	//require_once("lib/getid3/getid3.php");
	// Initialise objects
	$mysqli = new mysqli($dbLogin['dbhost'], $dbLogin['dbusername'], $dbLogin['dbuserpass'], $dbLogin['dbname']);
	if (mysqli_connect_errno($mysqli)) {
    error_log("poll-output | Failed to connect to MySQL: " . mysqli_connect_error());
    // go no further as the MySQL server is no doubt saturated
    exit();
  }
	$pollObj = new Default_Model_Polling_Class($mysqli);	
	//$GetId3 = new getID3(); // Used to extract the duration of the media
	//$GetId3->setOption( array( 'encoding' => 'UTF-8' ) );

	// BH 20120415 - removed support for number (repeations) and time (sleep delay) from code as part
	//               of move to using a shell script running as a process rather than a cron job.  This
	//               allows better control as the process will run this script for however long it takes
	//               limited by PHP execution time and then wait a set time before calling again.
	//
	//               This has the advantage that the script is not called in parallel ever which limits loading
	//               and removes the chance that the database will accessed by two or more running processes
	//               at the same time which can cause message or even file duplication issues.
	
	//if (isset($_REQUEST['time']) && $_REQUEST['time']>=1) $time = $_REQUEST['time']; else $time=1;
	//if (isset($_REQUEST['number']) && $_REQUEST['number']>=1) $number = $_REQUEST['number']; else $number=1;

	// Poll for any completed or failed commands on Media and Encoder queues

	//for ( $i = 1; $i <= $number; $i++) {

	
	// BH 20120415 - changed to out encoder-outbox rather than encoder-output as the latter will
	//               scan all the output folders and we are now just using the one folder for all
	//               files that are created.

  //error_log("poll-output | scanning directory =".$destination['encoder-outbox']);
 	
	//$files = $pollObj->read_folder_directory($destination['encoder-outbox']);
	$files = $pollObj->read_folder_directory($destination['encoder-output']);

	if (is_array($files)) {
		$pollObj->check_folder_directory($files);
	} else {
		$i = $number;	
	}
	
  //error_log("poll-output | spawn_or_update_commands'");
	$pollObj->spawn_or_update_commands();

		// sleep for n seconds
		//flush();  /// only needed for debug purposes if calling this directly
		//echo $i." - ";
	//	sleep($_REQUEST['time']);
	//}
	//error_log("poll-output | elapse time = ".number_format((microtime(true)-$start_time),4)." seconds");

?>