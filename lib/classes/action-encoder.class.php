<?php
/*========================================================================================*\
	#	Coder    :  Ian Newton
	#	Date     :  20th Feb,2011
	#	Test version  
	#  Class File to handle file service actions and provide responses.
\*=========================================================================================*/

class Default_Model_Action_Class
 {
	
	/**  * Constructor  */
    function Default_Model_Action_Class($mysqli){}  

// ------ User stuff

	function transfer($src, $dest) {
	
	// Note: SCP is executed as user '_www' and it's home directory is /Library/WebServer/
	// thus the secure keys are located in ~/.shh/ and the relevant key needs to be copied
	// onto the relevant user account of each destination server.  Note: exact user/server set
	// in global array $source and $destination.
	
	$cmdline = "/usr/bin/scp -p ".escapeshellcmd($src)." ".escapeshellcmd($dest)." 2>&1";
//	echo "<p>Transfer cmd line =".$cmdline."</p>\n";  // debug
	
	//error_log("Transfer cmd line =".$cmdline);  // debug
	
	exec($cmdline, $out, $code);
	
	return array($code, $out);
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
		global $mysqli;

		$retData= array( 
			'command'=>$action, 
			'number'=>'', 
			'data'=>$mArr, 
			'status'=>'N', 
			'error'=>''
			) ;
		$sqlQuery = "INSERT INTO `queue_commands` (`cq_command`, `cq_cq_index`, `cq_mq_index`, `cq_step`, `cq_data`, `cq_time`, `cq_update`, `cq_status`) VALUES ('".$action."','".$cqIndex."','".$mqIndex."','".$step."','".serialize($mArr)."','".date("Y-m-d H:i:s", $timestamp)."', '', 'N')"; 
		$mysqli->query($sqlQuery);
		$error = $mysqli->error;
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

	public function doQueueAction($function, $mArr, $cqIndex)
	{
		global $mysqli,$outObj,$mediaUrl;

			$retData = $this->$function($mArr,1,$cqIndex);
//			echo $function." - ";
			if ($retData['result']=='Y') {
				$sqlQuery = "UPDATE `queue_commands` SET `cq_update` = '".date("Y-m-d H:i:s", time())."' ,`cq_status`= 'Y', cq_result='".serialize($mArr)."' where cq_index='".$cqIndex."' ";
	//	echo $sqlQuery;
				$result = $mysqli->query($sqlQuery);
			}
	}

	public function doDirectAction($function, $mArr)
	{
		global $mysqli,$outObj,$mediaUrl;
			$retData = $this->$function($mArr,1);
		return $retData;
	}

	function doEncoderPushToMedia($mArr,$mNum,$cqIndex)
	{
		global $source, $destination; 

		$retData= array('cqIndex'=>$cqIndex, 'workflow' =>$mArr['workflow'], 'source_path'=> '', 'filename'=> $mArr['filename'], 'scp'=>'', 'number'=> $mNum, 'result'=> 'N') ;
		
		$nameArr = pathinfo($mArr['filename']);
//		$nameArr['emanelif'] = strrev( $nameArr['filename'] );
//		$nameArr['shortname'] = strrev(substr(strrchr($nameArr['emanelif'],'-'),1);
//		$nameArr['realname'] = $nameArr['shortname'].".".$nameArr['extension'];
		
		$outFile = urlencode($mArr['source_path'].$mArr['filename']);
		$inFile = $mArr['workflow']."/".$nameArr['filename']."-480p.".$nameArr['extension'];
 		$retData['scp'] = $this->transfer($source['encoder'].$inFile , $destination['media'].$outFile);
//		print_r($retData['scp']);
		if ($retData['scp'][0]==0) {
			$retData['result']='Y'; 
			unlink($inFile);
		}
		return $retData;
	}

	public function doEncoderPullFile($mArr,$mNum,$cqIndex)
	{
		global $source, $destination; 

		$retData= array('cqIndex'=>$cqIndex, 'workflow' =>$mArr['workflow'], 'source_path'=> '', 'filename'=> $mArr['filename'], 'scp'=>'', 'number'=> $mNum, 'result'=> 'N') ;

// echo $source['admin'].$mArr['source_path'].$mArr['filename'].", ".$destination['encoder'].$mArr['workflow']."/".$mArr['filename'];
 		$retData['scp'] = $this->transfer($source['admin'].$mArr['source_path'].$mArr['filename'], $destination['encoder'].$mArr['workflow']."/".$mArr['filename']);
		print_r($retData['scp']);
		if ($retData['scp'][0]==0) $retData['result']='Y';
		return $retData;
	}

	public function doEncoderCheckOutput($mArr,$mNum,$cqIndex)
	{
		global $source, $destination; 

		$retData= array('cqIndex'=>$cqIndex, 'workflow' =>$mArr['workflow'], 'source_path'=> '', 'filename'=> $mArr['filename'], 'scp'=>'', 'number'=> $mNum, 'result'=> 'N') ;

		$nameArr = pathinfo($mArr['filename']);
		$fileToCkeck =  $source['encoder'].$mArr['workflow']."/".$nameArr['filename']."-480p.".$nameArr['extension'];

//		echo $fileToCkeck;	

		if ( is_file($fileToCkeck) ) {
			for ( $i = 0; $i <= 3; $i++) {
				$size[$i] = filesize($fileToCkeck);
				sleep(2);
				if (isset($size)) print_r($size);
			}
			if (isset($size) && $size[0] == $size[1] && $size[1] == $size[2]) $retData['result']='Y' ;
		}
		return $retData;
	}

	public function doStatusEncoder($mArr,$mNum,$cqIndex)
	{
		$retData= array( 'infile'=> '', 'outfile'=> '','number'=> $mNum,  'result'=> 'Y') ;

		return $retData;
	}

	public function doPollEncoder($mArr,$mNum)
	{
		global $mysqli;
		
		$retData = array( 'command'=>'poll-encoder', 'status'=>'ACK', 'data'=>'Nothing to do!', 'number'=>0, 'timestamp'=>time());

		$sqlQuery0 = "SELECT * FROM queue_commands AS cq WHERE  cq.cq_status = 'Y' ORDER BY cq.cq_time";
//	echo $sqlQuery0;
		$result0 = $mysqli->query($sqlQuery0);
		if ($result0->num_rows) {
			$i=0;
			while(	$row0 = $result0->fetch_object()) { 
				$cqIndexData[] = array(	'status'=>$row0->cq_status, 'data'=>$row0->cq_result, 'cqIndex'=>$row0->cq_cq_index, 'mqIndex'=>$row0->cq_mq_index, 'step'=>$row0->cq_step  );
				$sqlQuery = "UPDATE `queue_commands` SET `cq_status`= 'R' where cq_index='".$row0->cq_index."' ";
				$result = $mysqli->query($sqlQuery);
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