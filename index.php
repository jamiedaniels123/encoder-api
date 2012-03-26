<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  24th May,2011
	#	Test version  
	#	input controller to accept post requests from the admin server
\*=========================================================================================*/

require_once("./lib/config.php");
require_once("./lib/classes/action-encoder.class.php");
require_once("./lib/getid3/getid3.php");

$mysqli = new mysqli($dbLogin['dbhost'], $dbLogin['dbusername'], $dbLogin['dbuserpass'], $dbLogin['dbname']);
if (mysqli_connect_errno($mysqli)) {
  error_log("Failed to connect to MySQL (/index.php): " . mysqli_connect_error());
  // go no further as the MySQL server is no doubt saturated
  exit();
}

$dataStream = file_get_contents("php://input");

$dataMess=explode('=',urldecode($dataStream));


if ($dataMess[1]!='') {

	$data=json_decode($dataMess[1],true);

	$dataObj = new Default_Model_Action_Class($mysqli);	

	$result = $mysqli->query("SELECT * FROM command_routes AS cr WHERE cr.cr_action = '".$data['command']."'");
	
	if ($result->num_rows) {
  	$row = $result->fetch_object();

		if ($row->cr_route_type=='queue'){
			$m_data = $dataObj->queueAction($data['data'],$data['command'],$data['cqIndex'],$data['mqIndex'],$data['step'],$data['timestamp']);
		}else if ($row->cr_route_type=='direct'){
			$m_data = $dataObj->doDirectAction($row->cr_function,$data['data']);
		}

	}else{
		$m_data = array('status'=>'NACK', 'data'=>'Command not known! - '.$apiName.'-'.$version, 'timestamp'=>time());
	}

}else{
	$m_data = array('status'=>'NACK', 'data'=>'No request values set! - '.$apiName.'-'.$version, 'timestamp'=>time());
}

// Log the command and response
	if (!isset($m_data['status']) || $m_data['status']!='OK') {
		if (isset($data) && is_array($data)) 
			$p_data = json_encode($data);
		else
			$p_data = "Cannot decode message or no input data array!";
		$result = $mysqli->query("	INSERT INTO `api_log` (`al_message`, `al_reply`, `al_debug`, `al_timestamp`) 
											VALUES ( '".$p_data."', '".json_encode($m_data)."', '', '".date("Y-m-d H:i:s", time())."' )");
	}

// Get rid of any debug and output the result to the caller
	ob_clean();
	file_put_contents("php://output", json_encode($m_data));
//	ob_end_clean();

?>