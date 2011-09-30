<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  24th May,2011
	#	Test version  
	#	controller to process actions queued in the media_actions table and report status to the admin server
\*=========================================================================================*/

require_once("lib/config.php");
require_once("lib/classes/polling.class.php");
//require_once("lib/getid3/getid3.php");

// Initialise objects
	$mysqli = new mysqli($dbLogin['dbhost'], $dbLogin['dbusername'], $dbLogin['dbuserpass'], $dbLogin['dbname']);
	$pollObj = new Default_Model_Polling_Class($mysqli);	
	//$GetId3 = new getID3(); // Used to extract the duration of the media
	//$GetId3->setOption( array( 'encoding' => 'UTF-8' ) );

	if (isset($_REQUEST['time']) && $_REQUEST['time']>=1) $time = $_REQUEST['time']; else $time=1;
	if (isset($_REQUEST['number']) && $_REQUEST['number']>=1) $number = $_REQUEST['number']; else $number=1;

// Poll for any completed or failed commands on Media and Encoder queues

	for ( $i = 1; $i <= $number; $i++) {

		$files = $pollObj->read_folder_directory($destination['encoder-output']);

// echo $destination['encoder-output']." <br />";
		
		if (is_array($files))
		{
			print_r($files);
			$pollObj->check_folder_directory($files);
			
		} else {
			$i = $number;	
		}
		
		$pollObj->spawn_or_update_commands();

// sleep for n seconds
		flush();
//		echo $i." - ";
		sleep($_REQUEST['time']);
	}

?>