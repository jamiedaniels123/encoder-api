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

	$result0 = $mysqli->query("
		SELECT * 
		FROM queue_commands AS cq, command_routes AS cr 
		WHERE cr.cr_action=cq.cq_command AND cq.cq_status = 'N' 
		ORDER BY cq.cq_time");

// Process any commands to be done

	if (isset($result0->num_rows)) {
		while(	$row0 = $result0->fetch_object()) { 
			$m_data= $dataObj->doQueueAction($row0->cr_function, unserialize($row0->cq_data), $row0->cq_index, $row0->cq_cq_index, $row0->cq_step);	
		}
	}
	
// Clean up old completed commands

//	$sqlQuery6="DELETE FROM `queue_commands` WHERE DATE(cq_time) < date_sub(curdate(), interval 24 hour)  AND `cq_status`='R' ";
//	$mysqli->query($sqlQuery6);

// Clean up old processes, completed commands and api-log

	$mysqli->query("	DELETE FROM `api_process` 
						WHERE ap_timestamp < (now() - interval 2 minute) 
						AND `ap_status`='N' ");
	$mysqli->query("	DELETE FROM `queue_commands` 
							WHERE DATE(cq_time) < date_sub(curdate(), interval 6 hour)  
							AND `cq_status` IN('R','D') ");
	$mysqli->query("	DELETE FROM `api_log` 
							WHERE al_timestamp < (now() - interval 24 hour) ");


?>