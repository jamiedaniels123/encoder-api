<?php
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  20th Feb,2011
	#	Test version  
	#  Encoder Class File to handle file service actions and provide responses.
\*=========================================================================================*/




class Default_Model_Polling_Class
 {
    protected $m_mysqli;
	
	/**  * Constructor  */
    function Default_Model_Polling_Class($mysqli){
		$this->m_mysqli = $mysqli;
	}  

// ------ User stuff


    function PsExec($commandJob) {

        $command = $commandJob.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $pid = (int)$op[0];
//		print_r ($op);
        if($pid!="") return $pid;

        return false;
    }

    function PsExists($pid) {

        exec("ps ax | grep $pid 2>&1", $output);

        while( list(,$row) = each($output) ) {

                $row_array = explode(" ", $row);
                $check_pid = $row_array[0];

                if($pid == $check_pid) {
                        return true;
                }
        }

        return false;
    }

    function PsKill($pid) {
        exec("kill -9 $pid", $output);
    }

	public function startCheckProcess($apCommand) {

// Check poll process and launch if not running. The Poll process polls both Media and Encoder APIs for completed tasks.
		$result0 = $this->m_mysqli->query("
			SELECT ap_process_id, ap_script, ap_status 
			FROM api_process 
			WHERE ap_status = 'Y' 
			ORDER BY ap_timestamp DESC");
		$j=0;
		if ($result0->num_rows >=1) {
			while(	$row0 = $result0->fetch_object()) {
				if ($this->PsExists($row0->ap_process_id)) {
					if ($j==0) {
						$this->m_mysqli->query("
							UPDATE `api_process` 
							SET `ap_status`='Y', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
							WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
						$j=1;
					} else {
						$this->PsKill($row0->ap_process_id);
						$this->m_mysqli->query("
							UPDATE `api_process` 
							SET `ap_status`='N', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
							WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
					}
				} else  {
						$this->m_mysqli->query("
							UPDATE `api_process` 
							SET `ap_status`='N', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
							WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
				}
			}
		}
		if ($j==0) {
				$processID=$this->PsExec($apCommand);
				if ($processID==false) $status='N'; else $status='Y';  
				$result = $this->m_mysqli->query("
					INSERT INTO `api_process` (`ap_process_id`, `ap_script`, `ap_timestamp`, `ap_status`) 
					VALUES ( '".$processID."',  '".$apCommand."', '".date("Y-m-d H:i:s", time())."', '".$status."' )");
		}

	}

	public function read_folder_directory($dir = "root_dir/dir"){

		$listDir = array();
		if($handler = opendir($dir)) {
			while (($sub = readdir($handler)) !== FALSE) {
				if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
					if(is_file($dir."/".$sub)) {
						$listDir[] = array("file"=>$sub, "size"=>filesize ($dir."/".$sub), "date"=>fileatime ($dir."/".$sub));
					}elseif(is_dir($dir."/".$sub)){
						$listDir[$sub] = $this->read_folder_directory($dir."/".$sub);
					}
				}
			}
			closedir($handler);
		}
		return $listDir;
	} 
	
	public function check_folder_directory($files){
		
		global $destination;
		
			$j=0;
			foreach($files as $k0 => $v0){
				foreach($v0 as $k1 => $v1){
					if ($v1['file']!=".DS_Store" && strlen($k0) > 2) {
						$nameArr = pathinfo($v1['file']);
						$nameArr['reversename'] = strrev( $nameArr['filename'] );
						$nameArr['shortname'] = strrev( substr( strstr( $nameArr['reversename'], '-' ), 1 ) );
						
						// The next 3 lines extract the flavour from the filename that has been generated.
						$start = strrpos( $v1['file'], '-' ) + 1;
						$finish = strrpos( $v1['file'], '.' );
						$nameArr['flavour'] = substr( $v1['file'], $start, ( $finish - $start ) );
						
						
						$result0 = $this->m_mysqli->query("
							SELECT wf_index, wf_count, wf_status, wf_filename 
							FROM watch_file
							WHERE wf_fileoutname = '".$v1['file']."' ");
						if ($result0->num_rows ==0) {
								$result = $this->m_mysqli->query("	INSERT INTO `watch_file` (`wf_filename`, `wf_fileoutname`, `wf_folder`, `wf_extension`, `wf_filesize0`, `wf_filedate0`, `wf_count`, `wf_status`, `wf_flavour`) 
																	VALUES ( '".$nameArr['shortname']."',  '".$v1['file']."', '".$k0."', '.".$nameArr['extension']."', '".$v1['size']."', '".$v1['date']."', '0', 'W','-".$nameArr['flavour']."' )");
							$wf_row = $this->m_mysqli->insert_id;
						}else{
							$row0 = $result0->fetch_object();
							if ($row0->wf_count==2) $j=0; else $j=$row0->wf_count+1;
							$this->m_mysqli->query("
								UPDATE `watch_file` 
								SET `wf_filesize".$j."`='".$v1['size']."', `wf_filedate".$j."`='".$v1['date']."', `wf_count`= '".$j."' 
								WHERE `wf_index`=  '".$row0->wf_index."' ");
							$wf_row = $row0->wf_index;
						}

// Check if any of this set of files in the read output tree array are still on the command_queue or older than 24 hours. If so unlink and unwatch 

						$result5 = $this->m_mysqli->query("
							SELECT * 
							FROM queue_commands 
							WHERE cq_filename LIKE '".$nameArr['shortname']."%'");
						if ($result5->num_rows == 0 && (time() > $v1['date'] + 86399)) {						
							$unlink_file= $destination['encoder-output'].$k0."/".$v1['file'];
							unlink($unlink_file);
							$this->m_mysqli->query("
								DELETE FROM watch_file 
								WHERE wf_index = '".$wf_row."'");
						}

					}
				}
			}
	}
	
	public function spawn_or_update_commands(){

		//global $GetId3;
		/*$result3 = $this->m_mysqli->query("
			SELECT * 
			FROM watch_file as wf, queue_commands as cq, workflow_map as wm 
			WHERE cq.cq_filename=wf.wf_filename AND wm.wm_workflow=wf.wf_folder AND wm.wm_mimetype=wf.wf_extension AND cq.cq_step = '4' AND wf.wf_filesize0=wf.wf_filesize1 AND wf.wf_filesize1=wf.wf_filesize2 AND cq.cq_status IN ('N','D') AND wf.wf_count!=0 AND wf.wf_status='W' AND wm.wm_media_root='./feeds' "); */


/*	$result3 = $this->m_mysqli->query("
			SELECT * 
			FROM watch_file as wf, queue_commands as cq, workflow_map1 as wm 
			WHERE cq.cq_filename=wf.wf_filename AND wm.wm_mimetype=wf.wf_extension AND cq.cq_step = '4' AND wf.wf_filesize0=wf.wf_filesize1 AND wf.wf_filesize1=wf.wf_filesize2 AND cq.cq_status IN ('N','D') AND wf.wf_count!=0 AND wf.wf_status='W' ");
*/
	// Amended to include the "distinct" clause preventing duplications of rows/callbacks.
	$result3 = $this->m_mysqli->query("
			SELECT
			distinct(wf.wf_fileoutname), wf.* , cq.*, wm.*
			FROM watch_file as wf, queue_commands as cq, workflow_map1 as wm 
			WHERE cq.cq_filename=wf.wf_filename AND wm.wm_outputfile = wf.wf_flavour AND cq.cq_step = '4' AND wf.wf_filesize0=wf.wf_filesize1 AND wf.wf_filesize1=wf.wf_filesize2 AND cq.cq_status IN ('R','N','D') AND wf.wf_count!=0 AND wf.wf_status='W' ");

		while ($row3 = $result3->fetch_object()) {
			/*$result4 = $this->m_mysqli->query("
				UPDATE queue_commands 
				SET `cq_update` = '".date("Y-m-d H:i:s", time())."' ,`cq_status`= 'D', cq_result='".$row3->cq_data."' 
				WHERE cq_index='".$row3->cq_index."' ");*/
			$mData = unserialize($row3->cq_data);
			$mData['source_filename'] = $row3->wf_fileoutname;
			$mData['destination_filename'] = $row3->wf_filename.$row3->wm_target_filename;

			// BH 20110927 - modified to use correct field now workflow_map1 has been re-populated using correct columns
			if ( $row3->wm_target_folder != '' && $row3->wm_target_folder != '/' ) {
				$mData['destination_path'] .= $row3->wm_target_folder;
			}
				
			$cqData = unserialize($row3->cq_data);
			$mData['original_filename'] = $cqData['source_filename'];
			
			// BH 20110927 - returned to using correct flavour unmodified now that the workflow_map1 table is correctly populated AND it's wm_flavour and WF_
			$mData['flavour'] = $row3->wm_flavour;
			// BH20110927 - this needs fixing to populate with actual duration of file via getID3 library

			//$GetId3->Analyze($mData['destination_path'].$mData['destination_filename']);
			$mData['duration']  = '1.55';
			//$mData['duration'] = @$GetId3->info['playtime_seconds'];
			
			$this->m_mysqli->query("
				INSERT INTO queue_commands (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_result`, `cq_time`, `cq_update`, `cq_status`) 
				VALUES ('".$row3->cq_command."', '".$row3->wf_fileoutname."', '".$row3->cq_cq_index."', '".$row3->cq_mq_index."', '".$row3->cq_step."', '".$row3->cq_data."',  '".serialize($mData)."', '".$row3->cq_time."', '".date("Y-m-d H:i:s", time())."', 'Y')");
error_log("INSERT INTO queue_commands (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_result`, `cq_time`, `cq_update`, `cq_status`) VALUES ('".$row3->cq_command."', '".$row3->wf_fileoutname."', '".$row3->cq_cq_index."', '".$row3->cq_mq_index."', '".$row3->cq_step."', '".$row3->cq_data."',  '".serialize($mData)."', '".$row3->cq_time."', '".date("Y-m-d H:i:s", time())."', 'Y')");
			$result4 = $this->m_mysqli->query("
				UPDATE watch_file 
				SET `wf_status` = 'R' 
				WHERE wf_index='".$row3->wf_index."' ");
		}
	 }

}
?>