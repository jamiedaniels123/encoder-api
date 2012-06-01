<?php
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  20th Feb,2011
	#	Test version  
	#  Encoder Class File to handle file service actions and provide responses.
\*=========================================================================================*/

class Default_Model_Action_Class
 {
    protected $m_mysqli;
	
	/**  * Constructor  */
    function Default_Model_Action_Class($mysqli){
		$this->m_mysqli = $mysqli;
	}  

// ------ User stuff

	function transfer($src, $dest) {
		
		// Note: SCP is executed as user '_www' and it's home directory is /Library/WebServer/
		// thus the secure keys are located in ~/.shh/ and the relevant key needs to be copied
		// onto the relevant user account of each destination server.  Note: exact user/server setup
		// in global array $source and $destination.
		
		$cmdline = "/usr/bin/scp -p -q ".escapeshellcmd($src)." ".escapeshellcmd($dest)." 2>&1";
		
		//	echo "<p>Transfer cmd line =".$cmdline."</p>\n";  // debug
		error_log("Transfer cmd line =".$cmdline);  // debug
		
		exec($cmdline, $out, $code);
		
		return array($code, $out);
	}
	
	function PsExec($commandJob) {
	
	    $command = $commandJob.' > /dev/null 2>&1 & echo $!';
	    exec($command ,$op);
	    $pid = (int)$op[0];
	//		print_r ($op);
	    if($pid!="") return $pid;
	
	    return false;
	}
	
	function PsExists($pid) {
	
	    exec("ps ax | grep ".$pid." 2>&1", $output);
	
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
	    exec("kill -9 ".$pid, $output);
	}

	public function startCheckProcess($apCommand) {

		// Check poll process and launch if not running. The Poll process polls both Media and Encoder APIs for completed tasks.
		$result0 = $this->m_mysqli->query("
			SELECT ap_process_id, ap_script, ap_status 
			FROM api_process 
			WHERE ap_status = 'Y' 
			ORDER BY ap_timestamp DESC");
		 
		// BH 20120326
		if ($this->m_mysqli->errno) {
		  error_log("action: startCheckProcess: query failed: ".$this->m_mysqli->error);
		  // go no further as the MySQL server is no doubt saturated
		  exit();
		}
		
		//error_log("action: startCheckProcess ".$apCommand);

		$j=0;
		if ($result0->num_rows >=1) {
			//error_log("action: startCheckProcess number of results ".$result0->num_rows);
			while(	$row0 = $result0->fetch_object()) {
				if ($this->PsExists($row0->ap_process_id)) {
					if ($j==0) {
						//error_log("action: startCheckProcess PsExists ".$row0->ap_process_id);
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
						//error_log("action: startCheckProcess PsKill ".$row0->ap_process_id);
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
					//error_log("action: startCheckProcess process no longer exists update database ".$row0->ap_process_id);
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
				if ($processID==false) {
					$status='N'; 
				} else {
					$status='Y';
				}  
				//error_log("action: startCheckProcess Start new process ".$processID);
				$result = $this->m_mysqli->query("
					INSERT INTO `api_process` (`ap_process_id`, `ap_script`, `ap_timestamp`, `ap_status`) 
					VALUES ( '".$processID."',  '".$apCommand."', '".date("Y-m-d H:i:s", time())."', '".$status."' )");
		}
		

	}
	   
	public function getStatus($mArr,$mNum,$mCommand)
	{
		$retData= array( 'command'=>'statusReply', 'number'=>'',  'data'=>'') ;
		$dataArr='';		$i=0;		
		while (isset($mArr[$i])){
			
			$i++;
		}
		if ($retData!='') $retData['number']=$i; else $retData['number']=0;
		return $retData;
	}

	public function queueAction($mArr,$action,$cqIndex,$mqIndex,$step,$timestamp)
	{	

		$retData= array( 	'command'=>$action, 'number'=>'', 'data'=>$mArr, 'status'=>'NACK', 'error'=>'' ) ;
		error_log("action-encoder > queueAction | mArr = ".serialize($mArr));
		$nameArr = pathinfo($mArr['source_filename']);
		error_log("action-encoder > queueAction | nameArr =".$nameArr['filename']);
		$this->m_mysqli->query("
			INSERT INTO `queue_commands` (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_time`, `cq_update`, `cq_status`) 
			VALUES ('".$action."','".$nameArr['filename']."','".$cqIndex."','".$mqIndex."','".$step."','".serialize($mArr)."','".date("Y-m-d H:i:s", $timestamp)."', '', 'N')");
		$error = $this->m_mysqli->error;
		if ($error=='') { 
			$retData['status']='Y';
			$retData['number']=1;
			$retData['error']=''; 
		} else { 
			$retData['status']='N';
			$retData['number']=0;
			$retData['error']=$error;
		}
		return $retData;
	}

	public function doQueueAction($function, $mArr, $cqIndex, $cqCqIndex, $step)
	{
		// BH 20120326 This function only updates queue_commands if the QueueAction ($function) returns a 'Y' or 'F'
		//             which may in the long term prove to be limiting.  Alternative results are possible but effectly
		//             remain unchanged (ie 'N')
			
		$retData = $this->$function($mArr,1,$cqCqIndex, $step);
		if ($retData['result']=='Y' || $retData['result']=='F') {
//				error_log($function." -- ".$retData['result']);
			$result = $this->m_mysqli->query("
				UPDATE `queue_commands` 
				SET `cq_update` = '".date("Y-m-d H:i:s", time())."' ,`cq_status`= '".$retData['result']."', cq_result='".serialize($retData)."' 
				WHERE cq_index='".$cqIndex."' ");
			if ($this->m_mysqli->affected_rows == -1) {
				error_log("doQueueAction: function:".$function." MySQL Update command failed -- ".$this->m_mysqli->error);
			}
		}
	}

	public function doDirectAction($function, $mArr)
	{

		$retData = $this->$function($mArr,1);
		return $retData;
	}

	function doEncoderPushToMedia($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination; 


		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';
		
		// add the source_folder in front of fiename
		// Note: exact reason for doing this uncertain, though related to behaviour in media-podcast-api.
		$outFile = urlencode($mArr['source_path'].$mArr['source_filename']);
		$sourceFile = urlencode($mArr['source_filename']);
		
		// BH 20120222 - removed 'workflow' from path as all Output now goes to a common directory - the 'Outbox'
		//               and switched to using the whole path to the file to save appending output path.
		$sourceFilePath = $source['encoder-outbox'].$mArr['source_filename'];
		
		error_log("action-encoder.class > doEncoderPushToMedia | ".$mArr." | ".$mNum." | ".$cqIndex." | ".$step." | ".$sourceFilePath." | ".$outFile);

    // BH 20120324 - added check that file exists as appears possible for system to look for a file that has through
    // some other method (human or system) been removed from the output
    if (file_exists($sourceFilePath)) {
      // BH 2010921 - change permissions to be 0664 so that remote file system has RW access via group
			chmod($sourceFilePath, 0664);
			// BH 20120410 Note: output filename is prefixed with the 'cqIndex' value.  Reason at this time somewhat uncertain
	 		$retData['scp'] = $this->transfer($sourceFilePath , $destination['media-scp'].$cqIndex."_".$outFile);
	
			if ($retData['scp'][0]==0) {
				$retData['result']='Y'; 
			} else {
				$retData['result']='F';
				$retData['debug']="Encoder workflow/file - ".$sourceFile." failed to transfer to media server.";
				error_log("action-encoder.class > doEncoderPushToMedia | failed to transfer to media server, file ".$sourceFile);
			}						
    } else {
      // BH 20120324 - file doesn't exist for some reason, log an error
			$retData['result']='F';
			$retData['debug']="Encoder workflow/file - ".$sourceFilePath." not found in encoder-outbox (".$source['encoder-outbox'].")";
			error_log("action-encoder.class > doEncoderPushToMedia | not found in encoder-outbox (".$source['encoder-outbox']."), file ".$sourceFile);
    }

		// determine the matching watch_file record and decrement the wf_transfers_pending field
		// Note: we do this even if there is an error to ensure that each message whether successful or failed
		//       is considered 'transfered' to allow the watch_file record and assciated file to be removed from
		//       the system going forward.
		//       TODO: Improve file transfer errors to allow 're-tries' in case destination server offline, this
		//       would require some extra tracking of re-tries and only afterwards would the 'wf_transfers_pending'
		//       be decremented.
		// if wf_transfers_pending now zero (or less) then
		//   delete the file
		//   update watch_file wf_status to 'R'  (D or 'C' maybe better, for 'Done' or 'Complete', DB schema would need updating)
		if ($retData['result']=='Y') {		
			$result = $this->m_mysqli->query("
				UPDATE `watch_file` 
				SET `wf_transfers_pending`= `wf_transfers_pending` - 1
				WHERE wf_fileoutname='".$mArr['source_filename']."' AND `wf_transfers_pending` > 0");
		} else {
			// error of some sort occured in file transfer, increment 'wf_transfer_error_count' in
			// addition to decrement 'wf_transfers_pending'.  At present this is for information only
			// and servers no function purpose.
			$result = $this->m_mysqli->query("
				UPDATE `watch_file` 
				SET `wf_transfers_pending`= `wf_transfers_pending` - 1, `wf_transfer_error_count` = `wf_transfer_error_count` + 1
				WHERE `wf_fileoutname`='".$mArr['source_filename']."' AND `wf_transfers_pending` > 0");
		}
		// check whether any more file transfers are pending
		$result = $this->m_mysqli->query("
			SELECT `wf_transfers_pending`
			FROM `watch_file` 
			WHERE `wf_fileoutname`='".$mArr['source_filename']."' AND `wf_transfers_pending` = 0");
		if ($result->num_rows == 1) {
			// no more transfers pending, update record in watch_file.  This will eventually be cleaned up as part of maintenance cycle
			$result = $this->m_mysqli->query("
				UPDATE `watch_file` 
				SET `wf_status`= 'R'
				WHERE wf_fileoutname='".$mArr['source_filename']."' AND `wf_transfers_pending` = 0");
			// delete file as no longer needed
			error_log("action-encoder.class > doEncoderPushToMedia | All transfers completed, attempting to delete file: ".$sourceFilePath);
			if (file_exists($sourceFilePath)) {
				if (unlink($sourceFilePath)) {
					error_log("action-encoder.class > doEncoderPushToMedia | Successfully deleted file ".$sourceFilePath);
				} else {
					error_log("action-encoder.class > doEncoderPushToMedia | Failed to delete file ".$sourceFilePath);
				}
			} else {
				error_log("action-encoder.class > doEncoderPushToMedia | file does not exist, can not delete file: ".$sourceFilePath);
			}
			// TODO: If there have been any transfer errors (detect via wf_transfer_error_count) then
			// instead of deleting the file move to a failed directory and send a notification
			//
			// rename($sourceFilePath, $destination['encoder-failed-transfer'].$sourceFile);			
		}
		
		return $retData;
	}


/*	public function doEncoderPullToInput($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination; 

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';
		$inFilePath = $source['admin-scp'].$mArr['source_path'].$mArr['source_filename'];
		$outFilePath = $destination['encoder-input'].$mArr['workflow']."/".$mArr['source_filename'];

		if (is_dir($destination['encoder-input'].$mArr['workflow'])) {
		

			$retData['scp'] = $this->transfer($inFilePath, $outFilePath);
			if ($retData['scp'][0]==0)
				$retData['result']='Y';
			else
				$retData['result']='F';
		} else {
				$retData['result']='F';
				$retData['debug']="Encoder workflow - ".$destination['encoder-input'].$mArr['workflow']." or source - ".$inFilePath." not found! ";
		}
		return $retData;
	}
*/
	public function doEncoderPullToInput($mArr,$mNum,$cqIndex,$step)
//	public function doEncoderPullToInbox($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination, $workflow_outputs; 
		
		// BH 20120215 New action tht pulls files (that need encoding) to generic 'Inbox' where
    //             the Workflow is then used to push the file on further.  In the current implementation
    //             (depending on workflow) the file is pushed to Eon (EE5 installation) for 'norminalizing'
    //             and having a watermark added (if needed) afterwhich it is thensubmitted to the Podcast
    //             Producer Cluster (Eden) to create all flavours except the default.
    //             Meanwhile the file is submitted to the EE6 cluster (Era) to create the 'default' flavour.
    //
    //  Assumptions:
    //    a) Workflows are of the following naming convention
    //        audio*
    //        screencast*
    //        screencast-wide*
    //        video*
    //        video-wide*
    //

	  // BH 20111026 - Thoughts on 'splitting' the 'default' encoding off from the rest to give better feedback
	  // 
	  // Issues:
	  //  1) how to determine if a 'default' flavour is required, for example some workflows
	  //     are just 'for transfer'.  Most likely we'll need a lookup array but this means
	  //     it needs to be kept in sync with new workflows as they are added.
	  //
	  //  2) Setting the priority of the 'default' workflow(s) higher than other workflows
	  //     won't promise quick processing as existing jobs will block access until a 'thread'
	  //     becomes available.  Possible solution is to use an independant encoder for the
	  //     'default' workflows that returns the files back to the relevant output folders
	  //     on the main encoder.  This complicates the setup, particularly if EE6 used since
	  //     it has no 'event' scripting, but it could possibly use FTP delivery - if reliable.
	  //
	  //  3) Archiving of the source file needs to avoid duplication, suggest the 'default'
	  //     flavour isn't be archived.
	  //
	  //  4) Duplication could be done by transfering the file twice is probably best solved
	  //     by just duplicating after transfer though a 'cp' command and then a 'mv'.
	  //

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';
		$inFilePath = $source['admin-scp'].$mArr['source_path'].$mArr['source_filename'];
		$outFilePath = $destination['encoder-inbox'].$mArr['source_filename'];

		// simplify workflow by removing the extra watermark and trailers, this will eventually be removed on podcast-admin(-api)
		if (stripos($mArr['workflow'], "video") !== false ) {
			// workflow is video, remove the extra watermark and trailers
			$workflow=str_replace(array("-watermark","-trailers"), "", $mArr['workflow']);
		} else {
			$workflow=$mArr['workflow'];
			$workflowKey="'".$workflow."'";
		}
		error_log("Workflow ".$workflow);
		// check workflow is recognised
		if (array_key_exists($workflow, $workflow_outputs)) {
			error_log("action-encoder > doEncoderPullToInput | workflow_outputs recognised (".$workflow.")");
		} else {
			error_log("action-encoder > doEncoderPullToInput | workflow_outputs not recognised (".$workflow.")");
			$retData['result']='F';
			$retData['debug']="Encoder workflow (".$workflow.") not recognised by encoder";
			return $retData;
		}

		// BH 20120421 TODO: Add support for 'forcing' the aspect to either 16:9 (wide) or 4:3 (sd) which is when the user selects
		// 									 an aspect ratio otherwise we simply 'letterbox' any video to the nearest appropriate aspect ratio.
		//									 For now we simply define a variable for future use.
		$videoAspectRatio="letterbox";  // supported values; 'force_wide', 'force_sd', 'letterbox'

		// check the destination directory exists (BH: not we don't have directly mapped 'workflow' directories)
		if (is_dir($destination['encoder-input'])) {
		  //directory exists - pull file to 'inbox' directory
			$retData['scp'] = $this->transfer($inFilePath, $outFilePath);
			//		print_r($retData['scp']);
			if ($retData['scp'][0]==0) {
			  // successfully pulled file to encoder
			  // (Feature addition: delete file after pulling via SSH?)
			  //
			  // To be implemented
			  //  a) at present the various 'flavours' are not supported as it is automatically
			  //     assumed that YouTube and iTunes U Public will have bumpers and trailers.
			  //     The primary variation is iTunes U Private that needs Watermarks but no bumpers
			  //     and trailers which is why support was originally provided
			  
				$retData['result']='Y';
				// add workflow flavours to data for tracking of encoder output
				$retData['workflow_outputs']=$workflow_outputs[$workflow];
				
				// Add workflow_flavours array to $retData so we can track progress of each flavour. The source array is
				// set in the config.php file.
				
				$sourceFilePath=$outFilePath;
				
				// Determine if video is interlaced
				$videointerlaced=false;
		
				if (stripos($mArr['workflow'], "audio") !== false ) {
				  // file is an audio file
				} else {
				  // Use MediaInfo CLI (http://mediainfo.sourceforge.net/) to check if video is
				  // interlaced or not
					// BH 20120421 - make sure that the webuser account (_www on OS X) has permissions to run MediaInfo in /usr/local/bin/
				  $mediaInfoCmd="/usr/local/bin/MediaInfo --inform='Video;%ScanType%' ".$sourceFilePath." 2>&1";
				  error_log("action-encoder > doEncoderPullToInput | mediaInfoCmd = ".$mediaInfoCmd);
				  
				  exec($mediaInfoCmd, $out, $code);
				  
				  if ($code == 0) {
				    $scantype=$out[0]; //line 1 of output
				    $videointerlaced = ($scantype=="Progressive") ? false : true;
				    if ($videointerlaced) {
				      error_log("action-encoder > doEncoderPullToInput | determined video scan type is INTERLACED (".$scantype.")");
				    } else {
				      error_log("action-encoder > doEncoderPullToInput | determined video scan type is PROGRESSIVE (".$scantype.")");
				    }
				  } else {
				    error_log("action-encoder > doEncoderPullToInput | WARNING: unable to determine video scan type, assuming progressive");
				    $videointerlaced=false;
				  }
				}
				$deinterlace = ($videointerlaced) ? "-deinterlace/" : "/" ;

				if (strpos($mArr['workflow'],"audio-") === 0) {
				  // workflow starts with audio-
				  $destinationFilePath=$destination['eon-input'].$mArr['source_filename'];
				  $defaultEncodingFilePath=$destination['encoder-input']."default-audio/".$mArr['source_filename'];
				} elseif (strpos($mArr['workflow'],"screencast-") === 0) {
				  // workflow starts with screencast-			
					if (strpos($mArr['workflow'],"screencast-wide") === 0) {
					  // workflow is a screencast-wide-* flavour
					  $destinationFilePath=$destination['eon-input'].$mArr['source_filename'];				
				    $defaultEncodingFilePath=$destination['encoder-input']."default-screencast-wide/".$mArr['source_filename'];
				    // $defaultEncodingFilePath=$destination['encoder-input']."default-screencast-wide".$deinterlace.$mArr['source_filename'];
					} else {
					  // workflow is a screencast-* flavour
					  $destinationFilePath=$destination['eon-input'].$mArr['source_filename'];
  				  $defaultEncodingFilePath=$destination['encoder-input']."default-screencast/".$mArr['source_filename'];
  				  //$defaultEncodingFilePath=$destination['encoder-input']."default-screencast".$deinterlace.$mArr['source_filename'];
					}

				} elseif (strpos($mArr['workflow'],"video-") === 0) {
				  // workflow starts with video-
					if (strpos($mArr['workflow'],"video-wide") === 0) {
					  // workflow is a video-wide-* flavour
					  $destinationFilePath=$destination['eon-input'].$mArr['source_filename'];
				    $defaultEncodingFilePath=$destination['encoder-input']."default-video-wide".$deinterlace.$mArr['source_filename'];
					} else {
					  // workflow is a video-* flavour
					  $destinationFilePath=$destination['eon-input'].$mArr['source_filename'];
				    $defaultEncodingFilePath=$destination['encoder-input']."default-video".$deinterlace.$mArr['source_filename'];
					}
				}

 		    chmod($sourceFilePath, 0664);  // changed permissions to be 664
 		    // TODO: should check success/fail

				// transfer file to Eon prior to starting encoding here
				// Eon will then pass the file onto PcP cluster for creating all flavours other than the 'default'
				// flavour which is created on EE6 on this encoder, improving the throughput of the default
				// encoding and avoiding bottlenecking due to big job creating many flavours.
				$retData['scp-eon'] = $this->transfer($sourceFilePath, $destinationFilePath);
 		  	// TODO: should check success/fail
 		  	
	  		// call remote web page to start processing of the transfered file.
	  		// Note: We don't have the other server 'pull' this file as we won't then
	  		// know when to start processing the default flavour on this server.  So
	  		// instead once file transfered into an 'Inbox' we submit a request and
	  		// this gives us a chance to also send additional information - ie workflow
	  		// and of course the filename.
	  		
	  		// TODO: Change  Hard coding workflow submission to Eon to the config.php page
	  		//       to avoid having to track down server specific properties in code
				$filename=$mArr['source_filename'];
				$url = "http://eon.open.ac.uk/submit/";
				$ch = curl_init();
				
				$data=array('workflow' => $workflow, 'filename' => $filename, 'videointerlaced' => $videointerlaced, 'videoaspectratio' => $videoAspectRatio);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_exec($ch);										
				curl_close($ch);
 		  	// TODO: should check success/fail of CURL
 		  	
				// start 'default' encoding in EE6 (on this encoder)
				$moved=rename($sourceFilePath, $defaultEncodingFilePath);
				// TODO: should check success/fail of move
			} else {
				$retData['result']='F';
			}
		} else {
				$retData['result']='F';
				$retData['debug']="Encoder workflow - ".$destination['encoder-input'].$mArr['workflow']." or source - ".$inFilePath." not found! ";
		}
		return $retData;
	}

	public function doEncoderPullToOutput($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination; 

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';
		$nameArr = pathinfo($mArr['source_filename']);
		
		// BH 20111101 - added a switch statement to add a suffix dependant on the extension so that the workflow
		// output monitors can pick up the file and deliver to the media server.  New supported types need to be added
		// to the switch statement.  Note, the advantage of using the workflows to monitor the suffix is that it handles
		// delivering the files to multiple end points of the media server, for example .mp3 files go to default, ipod-all etc
		
		$fileExt=isset($nameArr['extension']) ? $nameArr['extension'] : "";  // test extension exists as if not creates a error
		
		switch (strtolower($fileExt)) {
		  case "mp3":
		    $suffix="-mp3";  // mp3 files are not re-transcoded
		    break;
		  case "pdf":
		    $suffix="-pdf";  // note, this will not be transcripts, but files uploaded as track media
		    break;
		  case "m4a":
		    $suffix="-m4a";  // audio book format, not to be transcoded
		    break;
		  case "m4b":
		    $suffix="-m4b";  // another audio book format, not to be transcoded
		    break;
		  case "epub":
		    $suffix="-epub";  // epub file
		    break;
		  default:
	    	$suffix="";  // this should create some error my the monitoring code
		}
		
		// BH 20111101 - added suffix test and error reporting
		if ($suffix=="") {
			// file type not recognized, abort transfer
			$retData['result']='F';
			$retData['debug']="File extension (".$fileExt.") not supported for transfer via encoder (with transcoding) to media server";		
		
		} else {	
			// transfer file with suffix appended to filename for detection by output file monitoring, suffix workflows define subsequent handling
			$inFilePath = $source['admin-scp'].$mArr['source_path'].$mArr['source_filename'];
			// $outFilePath = $destination['encoder-output'].$mArr['workflow']."/".$nameArr['filename'].$suffix.".".$fileExt;  // BH 20111101 altered construction to included computed suffix
			// revised to support single outbox, rather than workflow folders which are no longer supported
			$outFilePath = $destination['encoder-outbox'].$nameArr['filename'].$suffix.".".$fileExt;  // BH 20111101 altered construction to included computed suffix
			
			// this test not really needed anymore, and is left over from an early version that used individual workflow folders
			if (is_dir($destination['encoder-outbox'])) {
				$retData['scp'] = $this->transfer($inFilePath, $outFilePath);
	      //		print_r($retData['scp']);
				if ($retData['scp'][0]==0)
					$retData['result']='Y';
				else
					$retData['result']='F';
					$retData['debug']="Encoder unable to transfer file from admin - ".$inFilePath." to ".$outFilePath;
			} else {
					$retData['result']='F';
					$retData['debug']="Encoder outbox not found - ".$destination['encoder-outbox']." not found!";
			}
		}
		
		return $retData;
	}

	public function doEncoderCheckOutput($mArr, $mNum, $cqIndex, $step)
	{
		global $destination;
		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';

		// BH 20120415 - Disabled call to 'startCheckProcess' as introducing a separate shell script process
		//               that will call 'poll-output.php' at regular intervals but as a single process rather than
		//               via a cron job.  Using a single process means we don't run the risk of multiple processes
		//               possibly running together and executing the same task from the database resulting in duplication
		//               and worst case overloading the MySQL database with queries.
		//
		//               TODO: Changed the 'result' to 'W' so that this funciton doesn't get repeated called while it waits
		
		// Check and/or start 2s polling process
		
		//$apCommand="curl -d \"number=10&time=2\" ".$destination['encoder-api']."/poll-output.php";	
		//$this->startCheckProcess($apCommand); 
		//error_log("doEncoderCheckOutput - ". $cqIndex);
		return $retData;
	}

	public function doStatusEncoder($mArr,$mNum,$cqIndex,$step)
	{
		$retData= array( 'number'=> $mNum,  'result'=> 'Y') ;

		return $retData;
	}

	public function doPollEncoder($mArr,$mNum)
	{
		
		$retData = array( 'command'=>'poll-encoder', 'status'=>'OK', 'number'=>0, 'timestamp'=>time());

		$result0 = $this->m_mysqli->query("
			SELECT * 
			FROM queue_commands AS cq 
			WHERE  cq.cq_status IN ('Y','F') 
			ORDER BY cq.cq_time");
		if ($result0->num_rows) {
			$i=0;
			while(	$row0 = $result0->fetch_object()) { 
				$cqIndexData[$i] = unserialize($row0->cq_result);
				if(isset($cqIndexData[$i]['debug'])) $cqIndexData[$i]['debug'] = strtr(base64_encode(addslashes(gzcompress(serialize($cqIndexData[$i]['debug']),9))), '+/=', '-_,');
				$cqIndexData[$i]['status']= $row0->cq_status;
				$cqIndexData[$i]['cqIndex']= $row0->cq_cq_index;
				$cqIndexData[$i]['mqIndex']= $row0->cq_mq_index;
				$cqIndexData[$i]['step']= $row0->cq_step;
				$this->m_mysqli->query("
					UPDATE `queue_commands` 
					SET `cq_status`= 'R' where cq_index='".$row0->cq_index."' ");
				$i++;
			}
			if (isset($cqIndexData)) {
				$retData['data']=$cqIndexData; 
				$retData['status']= 'Y';
				$retData['number']= $i+1;
			} else {
				$retData['data']='Encoder api - Nothing to do!';
			}
		}
		//error_log("action-encoder > doPollEncoder | retData = ".json_encode($retData));  // debug

		return $retData;
	}



}
?>