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
	// onto the relevant user account of each destination server.  Note: exact user/server set
	// in global array $source and $destination.
	
	$cmdline = "/usr/bin/scp -pv ".escapeshellcmd($src)." ".escapeshellcmd($dest)." 2>&1";

	echo "<p>Transfer cmd line =".$cmdline."</p>\n";  // debug
//error_log("Transfer cmd line =".$cmdline);  // debug

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
		$nameArr = pathinfo($mArr['source_filename']);
		$this->m_mysqli->query("
			INSERT INTO `queue_commands` (`cq_command`, `cq_filename`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_time`, `cq_update`, `cq_status`) 
			VALUES ('".$action."','".$nameArr['filename']."','".$cqIndex."','".$mqIndex."','".$step."','".json_encode($mArr)."','".date("Y-m-d H:i:s", $timestamp)."', '', 'N')");
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

			$retData = $this->$function($mArr,1,$cqCqIndex, $step);
			echo $function." - ";
			if ($retData['result']=='Y' || $retData['result']=='F') {
//	echo $sqlQuery;
				$result = $this->m_mysqli->query("
					UPDATE `queue_commands` 
					SET `cq_update` = '".date("Y-m-d H:i:s", time())."' ,`cq_status`= '".$retData['result']."', cq_result='".json_encode($mArr)."' where cq_index='".$cqIndex."' ");
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
				
		$outFile = urlencode($mArr['source_path'].$mArr['source_filename']);
		$inFile = $mArr['workflow']."/".$mArr['source_filename'];
		chmod($source['encoder-files'].$inFile, 0664);
 		$retData['scp'] = $this->transfer($destination['encoder-output'].$inFile , $destination['media-scp'].$cqIndex."_".$outFile);
//		print_r($retData['scp']);

		if ($retData['scp'][0]==0) {
			$retData['result']='Y'; 

			$result0 = $this->m_mysqli->query("
				SELECT cq_filename 
				FROM queue_commands
				WHERE cq_filename = '".$mArr['source_filename']."' AND cq_status='N' AND cq_step=".$step." ");
			if ($result0->num_rows==1) unlink($destination['encoder-output'].$inFile);
		} else {
			$retData['result']='F';
			$retData['debug']="Encoder workflow - ".$destination['encoder-output'].$mArr['workflow']." not found! ";
		}
		return $retData;
	}

	public function doEncoderPullToInput($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination; 

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';

		if (is_dir($destination['encoder-input'].$mArr['workflow'])) {

			$retData['scp'] = $this->transfer($source['admin-scp'].$mArr['source_path'].$mArr['source_filename'], $destination['encoder-input'].$mArr['workflow']."/".$mArr['source_filename']);
//		print_r($retData['scp']);
			if ($retData['scp'][0]==0)
				$retData['result']='Y';
			else
				$retData['result']='F';
		} else {
				$retData['result']='F';
				$retData['debug']="Encoder workflow - ".$destination['encoder-input'].$mArr['workflow']." not found! ";
		}
		return $retData;
	}

	public function doEncoderPullToOutput($mArr,$mNum,$cqIndex,$step)
	{
		global $source, $destination; 

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';

		if (is_dir($destination['encoder-output'].$mArr['workflow'])) {

			$retData['scp'] = $this->transfer($source['admin-scp'].$mArr['source_path'].$mArr['source_filename'], $destination['encoder-output'].$mArr['workflow']."/".$mArr['source_filename']);
//		print_r($retData['scp']);
			if ($retData['scp'][0]==0)
				$retData['result']='Y';
			else
				$retData['result']='F';
		} else {
				$retData['result']='F';
				$retData['debug']="Encoder workflow - ".$destination['encoder-input'].$mArr['workflow']." not found! ";
		}
		return $retData;
	}

	public function doEncoderCheckOutput($mArr, $mNum, $cqIndex, $step)
	{

		$retData = $mArr;
		$retData['number'] = $mNum;
		$retData['result'] = 'N';

// Check and/or start 2s polling process
		$apCommand="curl -d \"number=40&time=2\" http://kmi-encoder04/poll-output.php";	
		$this->startCheckProcess($apCommand); 

		return $retData;
	}

	public function doStatusEncoder($mArr,$mNum,$cqIndex,$step)
	{
		$retData= array( 'number'=> $mNum,  'result'=> 'Y') ;

		return $retData;
	}

	public function doPollEncoder($mArr,$mNum)
	{
		
		$retData = array( 'command'=>'poll-encoder', 'status'=>'N', 'data'=>'Nothing to do!', 'number'=>0, 'timestamp'=>time());

		$result0 = $this->m_mysqli->query("
			SELECT * 
			FROM queue_commands AS cq 
			WHERE  cq.cq_status IN ('Y','F')  
			ORDER BY cq.cq_time");
		if ($result0->num_rows) {
			$i=0;
			while(	$row0 = $result0->fetch_object()) { 
				$cqIndexData[] = array( 'status'=>$row0->cq_status, 'data'=>json_decode($row0->cq_result, true), 'cqIndex'=>$row0->cq_cq_index, 'mqIndex'=>$row0->cq_mq_index, 'step'=>$row0->cq_step  );
				$result = $this->m_mysqli->query("
					UPDATE `queue_commands` 
					SET `cq_status`= 'R' where cq_index='".$row0->cq_index."' ");
				$i++;
			}
			$retData['data']=$cqIndexData;
			$retData['status']= 'Y';
			$retData['number']= $i;
			
		}
		return $retData;
	}

}
?>