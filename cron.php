<?PHP
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  24th May,2011
	#	Test version  
	#	Encoder-api input controller to accept post requests from the admin server
\*=========================================================================================*/

// Initialise objects

	$mysqli = new mysqli($dbLogin['dbhost'], $dbLogin['dbusername'], $dbLogin['dbuserpass'], $dbLogin['dbname']);
	$dataObj = new Default_Model_Action_Class($mysqli);	

// Get the outstanding actions from the queue table

	$sqlQuery0 = "	SELECT * 
						FROM queue_commands AS cq, command_routes AS cr 
						WHERE cr.cr_action=cq.cq_command 
						AND cq.cq_status = 'N' 
						ORDER BY cq.cq_time";
	$result0 = $mysqli->query($sqlQuery0);

// Process any commands to be done

	if (isset($result0->num_rows)) {
		while(	$row0 = $result0->fetch_object()) { 
			$m_data= $dataObj->doQueueAction($row0->cr_function, unserialize($row0->cq_data), $row0->cq_index);	
		}
	}

// Clean up old completed commands

//	$sqlQuery6="DELETE FROM `queue_commands` WHERE DATE(cq_time) < date_sub(curdate(), interval 24 hour)  AND `cq_status`='R' ";
//	$mysqli->query($sqlQuery6);

// Clean up old processes and api-log

	$sqlQuery6="	DELETE FROM `api_process` 
						WHERE ap_timestamp < (now() - interval 5 minute) 
						AND `ap_status`='N' ";
	$mysqli->query($sqlQuery6);

// Report the status and api messages

//	if (!isset($m_data['status']) || $m_data['status']!='ACK') {
//		$sqlLogging = "INSERT INTO `api_log` (`al_message`, `al_reply`, `al_timestamp`) VALUES ( '".urldecode($dataStream)."', '".serialize($m_data)."', '".date("Y-m-d H:i:s", time())."' )";
//		$result = $mysqli->query($sqlLogging);
//	}

?>