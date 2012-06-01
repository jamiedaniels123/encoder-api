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
			// execute $commandJob as a background task and get the process id for future reference
      $command = $commandJob.' > /dev/null 2>&1 & echo $!';
      exec($command ,$op);
      $pid = (int)$op[0];
      if($pid!="") {
      	return $pid;
			} else {
      	return false;
      }
	  }

    function PsExists($pid) {
			// check to see if given process is still running
			exec("ps ax | grep ".$pid." 2>&1", $output);
			
			//error_log("PSExists ".$pid." Output=".$output);
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
    	// kill given process
			exec("kill -9 ".$pid, $output);
    }

	public function startCheckProcess($apCommand) {

	  error_log("polling-class: startCheckProcess | cli=".$apCommand);

		// Check poll process and launch if not running. The Poll process polls both Media and Encoder APIs for completed tasks.
		$result0 = $this->m_mysqli->query("
			SELECT ap_process_id, ap_script, ap_status 
			FROM api_process 
			WHERE ap_status = 'Y' 
			ORDER BY ap_timestamp DESC");

		// BH 20120326
		if ($this->m_mysqli->errno) {
		  error_log("polling-class: startCheckProcess: query failed: ".$this->m_mysqli->error);
		  // go no further as the MySQL server is no doubt saturated
		  exit();
		}

		//error_log("startCheckProcess ".$apCommand);

		$j=0;
		if ($result0->num_rows >=1) {
			while(	$row0 = $result0->fetch_object()) {
				if ($this->PsExists($row0->ap_process_id)) {
					if ($j==0) {
						$this->m_mysqli->query("
							UPDATE `api_process` 
							SET `ap_status`='Y', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
							WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
							if ($this->m_mysqli->errno) {
							  // go no further as the MySQL server is no doubt saturated
							  exit();
							}
						$j=1;
					} else {
						$this->PsKill($row0->ap_process_id);
						$this->m_mysqli->query("
							UPDATE `api_process` 
							SET `ap_status`='N', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
							WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
							if ($this->m_mysqli->errno) {
							  // go no further as the MySQL server is no doubt saturated
							  exit();
							}
					}
				} else  {
					$this->m_mysqli->query("
						UPDATE `api_process` 
						SET `ap_status`='N', `ap_last_checked`='".date("Y-m-d H:i:s", time())."' 
						WHERE `ap_process_id`=  '".$row0->ap_process_id."' ");
					if ($this->m_mysqli->errno) {
					  // go no further as the MySQL server is no doubt saturated
					  exit();
					}
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
	
	public function check_folder_directory($files) {
		
		global $destination;
		
		$j=0;
		foreach ($files as $k0 => $v0) {
			foreach ($v0 as $k1 => $v1) {
				if ($v1['file']!=".DS_Store" && strlen($k0) > 2) {
					$nameArr = pathinfo($v1['file']);
					$nameArr['reversename'] = strrev( $nameArr['filename'] );
					$nameArr['shortname'] = strrev( substr( strstr( $nameArr['reversename'], '-' ), 1 ) );
					
					// The next 3 lines extract the flavour from the filename that has been generated.
					$start = strrpos( $v1['file'], '-' ) + 1;
					$finish = strrpos( $v1['file'], '.' );
					$nameArr['flavour'] = substr( $v1['file'], $start, ( $finish - $start ) );
					
					//error_log("polling.class > check_folder_directory | v1['file']=".$v1['file']);					
					//error_log("SELECT wf_index, wf_count, wf_status, wf_filename FROM watch_file WHERE wf_fileoutname = '".$v1['file']."' ");
					$result0 = $this->m_mysqli->query("
						SELECT wf_index, wf_count, wf_status, wf_filename 
						FROM watch_file
						WHERE wf_fileoutname = '".$v1['file']."' ");
					if ($result0->num_rows ==0) {
						// File not listed in DB, add new entry
						$result = $this->m_mysqli->query("
								INSERT INTO `watch_file` (`wf_filename`, `wf_fileoutname`, `wf_folder`, `wf_extension`, `wf_filesize0`, `wf_filedate0`, `wf_count`, `wf_status`, `wf_flavour`) 
								VALUES ( '".$nameArr['shortname']."',  '".$v1['file']."', '".$k0."', '.".$nameArr['extension']."', '".$v1['size']."', '".$v1['date']."', '0', 'W','-".$nameArr['flavour']."' )");
						$wf_row = $this->m_mysqli->insert_id;
					} else {
						// file does exist in DB, update entry
						$row0 = $result0->fetch_object();
						if ($row0->wf_count==2) $j=0; else $j=$row0->wf_count+1;
						$this->m_mysqli->query("
							UPDATE `watch_file` 
							SET `wf_filesize".$j."`='".$v1['size']."', `wf_filedate".$j."`='".$v1['date']."', `wf_count`= '".$j."' 
							WHERE `wf_index`=  '".$row0->wf_index."' ");
						$wf_row = $row0->wf_index;
					}

					// Check if file is no longer on the command_queue, if not and older than 24 hours then delete (unlink) and unwatch (remove from DB)

					$result5 = $this->m_mysqli->query("
						SELECT * 
						FROM queue_commands 
						WHERE cq_filename LIKE '".$nameArr['shortname']."%'");
					if ($result5->num_rows == 0 && (time() > $v1['date'] + 86399)) {
						// file no longer listed in command_queue and is older than 24 hours, delete					
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
	
	public function spawn_or_update_commands() {

		// New approach
		global $source, $destination, $workflow_outputs; 
	
		$result = $this->m_mysqli->query("
			SELECT wf.*
			FROM watch_file as wf
			WHERE wf.wf_filesize0=wf.wf_filesize1 AND wf.wf_filesize1=wf.wf_filesize2 AND wf.wf_count!=0 AND wf.wf_status='W'");
		
		if ($result->num_rows > 0) {
			error_log("polling.class > spawn_or_update_commands | watching ".$result->num_rows." files");
		}
		// loop through all files that we are watching and whose file sizes are stable
		while ($row = $result->fetch_object()) {
			// we have a new file that is pending
			$wf_filename = $row->wf_filename;
			$wf_fileoutname = $row->wf_fileoutname;
			$wf_flavour = $row->wf_flavour;
			$wf_index = $row->wf_index;

			error_log("polling.class > spawn_or_update_commands | processing watch_file ".$wf_fileoutname." (".$wf_index.") flavour =".$wf_flavour);
			// locate matching 'encoder-check-output' for cq_filename = wf_filename and not complete cq_status='N'
			// Note: there is an assumption that the original 'encoder-check-output' command will remain set on 'N' until all
			//       files encoded, however there is some risk that there maybe multiple matches against the command queue
			//       that could cause problems.  Code could be improved to limit this risk.
			$result2 = $this->m_mysqli->query("
				SELECT cq.*, wm.*
				FROM queue_commands as cq, workflow_map1 as wm
				WHERE cq.cq_filename ='".$wf_filename."' AND wm.wm_outputfile = '".$wf_flavour."' AND cq.cq_status ='N'");
			$num_flavours=$result2->num_rows;

			if ($num_flavours > 0) {
				// update the watch_file table to indicate that we are now transfering the file (status='T') and also
				// add a transfers_pending count based on number of records.
				// Note: this is done before adding new records to avoid possible issue of the newly added records executing
				//       before we have finished adding them!
				$watch_file_update_result = $this->m_mysqli->query("
					UPDATE watch_file 
					SET `wf_status` = 'T', `wf_transfers_pending` = ".$num_flavours." 
					WHERE wf_index=".$wf_index." ");
				//error_log("polling.class > spawn_or_update_commands | UPDATE watch_file SET `wf_status` = 'T', `wf_transfers_pending` = ".$num_flavours." WHERE wf_index=".$wf_index);
				if ($this->m_mysqli->affected_rows > 0) {
										
					// watch_file updated to transfer state (T), spawn transfer commands in queue_commands
					while ($row2 = $result2->fetch_object()) {
						// Need to spawn a new command for each 'flavour' (media_item) that is found, the command will get pulled back to
						// podcast-admin-api and will then cause a transfer to media command to be issues back to the encoding cluster.						
						$wm_type = $row2->wm_type;
						$cqData = unserialize($row2->cq_data);
						$mData = $cqData;  // $mData will get modified, whereas $cqData represents what is from the database
						$original_filename = $cqData['source_filename'];
						
						if (array_key_exists($wf_flavour, $mData['workflow_outputs'])) {
							error_log("polling.class > spawn_or_update_commands | existing workflow_outputs = ".serialize($mData['workflow_outputs']));
							error_log("polling.class > spawn_or_update_commands | updating workflow_outputs for output = ".$wf_flavour);
							// remove from workflow_outputs array the relevant flavour
							// Note that should there be more than one record for a given output (going to multiple destinations) then
							// this will only execute on the first occurance
							unset($mData['workflow_outputs'][$wf_flavour]);
							// re-index array to clean it up
							//$mData['workflow_outputs'] = array_values($mData['workflow_outputs']);
							// update original 'encoder-check-output' record's mData having updated it's workflow_outputs array in mData
							error_log("polling.class > spawn_or_update_commands | NEW workflow_outputs = ".serialize($mData['workflow_outputs']));
							if (count($mData['workflow_outputs']) == 0) {
								// no more workflow_outputs expected
								error_log("polling.class > spawn_or_update_commands | NO MORE WORKFLOW OUTPUTS EXPECTED");
								$cq_status="R";
								$retData = $mData;
								$retData['workflow_complete'] = 'Y';
								$retData['original_filename'] = $original_filename;
								$retData['number'] = 1;  // BH 20120430 not quite sure as to purpose, just following lead of doQueueAction() in action-encoder.clas.php
								$retData['result'] = 'Y';
								$workflow_result = $this->m_mysqli->query("
									UPDATE `queue_commands` 
									SET `cq_update` = '".date("Y-m-d H:i:s", time())."',`cq_status`= 'Y', cq_result='".serialize($retData)."', cq_data='".serialize($mData)."' 
									WHERE cq_index='".$row2->cq_index."' ");
							} else {
								// still expecting output, update the reduced workflow_outputs for relevant command queue record
								error_log("polling.class > spawn_or_update_commands | ".count($mData['workflow_outputs'])." MORE WORKFLOW OUTPUTS EXPECTED");
								$workflow_result = $this->m_mysqli->query("
									UPDATE `queue_commands` 
									SET `cq_update` = '".date("Y-m-d H:i:s", time())."', cq_data='".serialize($mData)."' 
									WHERE cq_index='".$row2->cq_index."' ");
							}
							if ($this->m_mysqli->affected_rows == -1) {
								error_log("polling.class > spawn_or_update_commands | updating workflow_outputs | MySQL Update command failed -- ".$this->m_mysqli->error);
							}
						}
						
						$mData['source_filename'] = $wf_fileoutname;
						// add the 'flavour' suffix and extension to filename
						$mData['destination_filename'] = $wf_filename.$row2->wm_target_filename;
						// error_log("spawn_or_update_commands = ".$mData['source_filename']);
			
						// BH 20110927 - modified to use correct field now workflow_map1 has been re-populated using correct columns
						if ( $row2->wm_target_folder != '' && $row2->wm_target_folder != '/' ) {
							// this is to avoid adding a double '/' when transfering the files
							$mData['destination_path'] .= $row2->wm_target_folder;
						}
						
						$cq_filenameArr = pathinfo($wf_fileoutname);
						
						$filepath = $source['encoder-outbox'].$wf_fileoutname;

						$mData['original_filename'] = $original_filename;
						
						// BH 20110927 - returned to using correct flavour unmodified now that the workflow_map1 table is correctly populated AND it's wm_flavour and WF_
						// Note: just to make things confusing wm_flavour does not map to wf_flavour, rather it maps to wm_outputfile!
						$mData['flavour'] = $row2->wm_flavour;
												
						$duration_seconds = 0;
						if ($wm_type == "video" || $wm_type == "audio" || $wm_type == "audioBook") {						
							// BH 20120421 - determine duration of track using MediaInfo CLI (http://mediainfo.sourceforge.net/), the duration
							//							 is in the format HH:MM:SS.mmm (mmm = milliseconds)
							$mediaInfoCmd="/usr/local/bin/MediaInfo --inform='General;%Duration/String3%' ".$filepath." 2>&1";
					    error_log("polling > spawn_or_update_commands | mediaInfoCmd = ".$mediaInfoCmd);
					    
					    // BH 20120421 - make sure that the webuser account (_www on OS X) has permissions to run MediaInfo in /usr/local/bin/
							exec($mediaInfoCmd, $out, $code);
							  
						  if ($code == 0) {
						    $duration_string = (isset($out[0])) ? $out[0] : 0 ; //line 1 of output
						    $duration_array = explode(":", $duration_string);
								//$duration_seconds = strtotime($duration_string, 0);
						    $duration_seconds = $duration_array[0]*3600 + $duration_array[1]*60 + round($duration_array[2],2);
					      error_log("polling > spawn_or_update_commands | duration is ".$duration_string." -> ".$duration_seconds." seconds");
						  } else {
						    error_log("polling > spawn_or_update_commands | WARNING: unable to determine video duration, set to 0");
						  }
						  // make sure duration stored to only 2 decimal points by formating string, as serializing data appears to not respect this otherwise
							$mData['duration'] = number_format($duration_seconds,2,".","");
							 
						} else if ($wm_type == "image") {
							// need to convert image from PNG to JPG prior to transfer.
							$jpgfilepath = $source['encoder-outbox'].$cq_filenameArr['filename'].".jpg";
							$image = imagecreatefrompng($filepath);
							imagejpeg($image, $jpgfilepath , 80);
							imagedestroy($image);
							// delete the .png copy as no longer needed
							unlink($filepath);
							// change source_filename so that the jpg version is copied instead of png
							$mData['source_filename'] = $cq_filenameArr['filename'].".jpg";
							// Need to update the watch_file database entry as it is watching the .png version and we have now converted it to .jpg.
							// If we don't the watch_file process will create new .jpg entries.  Also revise the file sizes
							$new_filesize = filesize($jpgfilepath);
							$watch_file_update_result = $this->m_mysqli->query("
								UPDATE watch_file 
								SET `wf_fileoutname` = '".$cq_filenameArr['filename'].".jpg"."', `wf_extension` = '.jpg', `wf_filesize0` = '".$new_filesize."', `wf_filesize1` = '".$new_filesize."', `wf_filesize2` = '".$new_filesize."' 
								WHERE wf_index=".$wf_index." ");
							$mData['duration'] = 0;
						}					
						$this->m_mysqli->query("
							INSERT INTO queue_commands (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_result`, `cq_time`, `cq_update`, `cq_status`) 
							VALUES ('".$row2->cq_command."', '".$cq_filenameArr['filename']."', '".$row2->cq_cq_index."', '".$row2->cq_mq_index."', '".$row2->cq_step."', '".$row2->cq_data."',  '".serialize($mData)."', '".$row2->cq_time."', '".date("Y-m-d H:i:s", time())."', 'Y')");
							
						//error_log("INSERT INTO queue_commands (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_result`, `cq_time`, `cq_update`, `cq_status`) VALUES ('".$row2->cq_command."', '".$row2->wf_fileoutname."', '".$row2->cq_cq_index."', '".$row2->cq_mq_index."', '".$row2->cq_step."', '".$row2->cq_data."',  '".serialize($mData)."', '".$row2->cq_time."', '".date("Y-m-d H:i:s", time())."', 'Y')");
						
						// TODO: Need to update original 'encoder-check-output' commands 'workflow_outputs' array (in data) 
			
					}
				} else {
					// error - couldn't update the watch_file table, this shouldn't happen 
					error_log("polling.class > spawn_or_update_commands | couldn't update the watch_file for wf_index=".$wf_index." to 'transfer (T) state");
				}
			} else {
				// Error, no flavours found.  This could be because the file is no longer in the queue_commands
				// table or the flavour generated is not recognized.
				//
				// TODO: report error for this watch_file
				error_log("polling.class > spawn_or_update_commands | no flavours found matching (".$wf_flavour.") for watch_file ".$wf_fileoutname);
				$watch_file_update_result = $this->m_mysqli->query("
					UPDATE watch_file 
					SET `wf_status` = 'X'
					WHERE wf_index='".$wf_index."' ");
			}
			// Finished processing - need to update check-encoder-output workflow_output list updating which 'outputs' are now transfering
			
		}
	}
	
}
?>