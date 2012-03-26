<?php 

require_once("Logger.php");
$endpoint = "http://podcast-api.open.ac.uk/EVA/www/dashboard/logs/Transfer/";
//$endpoint = "http://podcast-api.open.ac.uk/EVA/www/dashboard/logs/Events/";
//$endpoint = "http://podcast-api.open.ac.uk/EVA/www/dashboard/logs/Events-Dev/";

global $logger;
$logger = new Logger($endpoint);

$source = array(
	'pod' => 'media-transfer@podcast.open.ac.uk:/data/web/podcast.open.ac.uk/www/upload/source/',
	'webcef' => 'transfer@kmi-fm02.open.ac.uk:/web/webcef.open.ac.uk/plugins/upload/processing/drop/',
	'cefcult' => 'transfer@kmi-fm02.open.ac.uk:/web/cefcult.open.ac.uk/plugins/upload/processing/drop/',
	'pod-feeds' => 'media-transfer@podcast.open.ac.uk:/data/web/podcast.open.ac.uk/www/feeds/',
	'subler'  => '/Library/WebServer/Documents/subler/source/',
	'root'=> '',
	'ee'  => '/Users/Shared/EpisodeEngine/Output/'
);

$destination = array(
	'drop' => '/Users/Shared/EpisodeEngine/Input/',
	'webcef' => 'transfer@kmi-fm02.open.ac.uk:/web/webcef.open.ac.uk/plugins/upload/processed',
	'cefcult' => 'transfer@kmi-fm02.open.ac.uk:/web/cefcult.open.ac.uk/plugins/upload/processed',
	'pod-feeds' => 'media-transfer@podcast.open.ac.uk:/data/web/podcast.open.ac.uk/www/feeds/',
	'subler'  => '/Library/WebServer/Documents/subler/destination/',
	'pod'  => 'media-transfer@podcast.open.ac.uk:/data/web/podcast.open.ac.uk/www/upload/processed'
);

// this array of url's is called after a transfer is complete, passing all original POST variables
// and also 'COPY_SUCCESS' which returns success or failure.

$post_report = array(
	'report-transfer-podcast'  => 'http://postcast.open.ac.uk/upload/post_transfer_to_encoder.php'
);

// post_action is intended for 'simple actions' that occur via a Curl and can be run using just the provided
// parameters, namely 'prefix', 'folder', 'file' and is only called if the transfer was successful.  This allows
// for simple 'chaining' of events without needing the process/server that initiated the transfer to be involved.
// For more complex situations events will need to be triggered by the 'post_report' end point which will have
// a better understanding of the context.
//
// Note, that for encoding, the Episode Engine encoder will automatically detect when a new file is added based
// on current setup where files are delivered direct to the input workflows, however in future this might be
// changed so that the files are delivered to separate 'destination' folders and then as a post_action moved
// into the appropriate Episode Engine input folder.

$post_action = array(
	'scc-embed'  => 'http://eon.open.ac.uk/subler/'
);

global $selfUrl;
$selfUrl = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["PHP_SELF"];	

function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
}

function shortUuid($uuid) {
	return str_replace(
				array('=', '/', '+'),
				array('', '_', '-'),
				base64_encode(pack('H*', str_replace('-', '', $uuid))));
}

global $uuid;
$uuid = $_POST['uuid'];
if ($uuid == null) {
	$uuid = shortUuid(uuid());
	$uuid = $_POST['file'];
}

if ($_POST["ident"] != null) {
	$uuid = $_POST["ident"];
}

//TODO rename uuid to ident.

function transfer($src, $dest) {
	global $logger;
	global $uuid;
	
	// Note: SCP is executed as user '_www' and it's home directory is /Library/WebServer/
	// thus the secure keys are located in ~/.shh/ and the relevant key needs to be copied
	// onto the relevant user account of each destination server.  Note: exact user/server set
	// in global array $source.
	
  //	$logger->log($uuid, 103, "src " . $src, data());
  //	$logger->log($uuid, 103, "src esc " . escapeshellcmd($src), data());
  //	$logger->log($uuid, 104, "dest " . escapeshellcmd($dest), data());
  $cmdline = "/usr/bin/scp -v ".escapeshellcmd($src)." ".escapeshellcmd($dest)." 2>&1";
	error_log("Transfer cmd line =".$cmdline);
	exec($cmdline, $out, $code);
	/* exec("/usr/bin/scp -v " 
			.escapeshellcmd($src)
			." "
			.escapeshellcmd($dest)
			." 2>&1", $out, $code); */
	return array($code, $out);
}

function data() {
	global $selfUrl;	
	return array(
				"prefix" 	=> $_POST['prefix'],
				"folder" 	=> $_POST['folder'],
				"file" 		=> $_POST['file'],
				"src" 		=> $_POST['src'],
				"dest" 		=> $_POST['dest'],
				"self"		=> $selfUrl
			);
}

if ($_POST['file'] 		!= null 
 && $_POST['src']	 	!= null  
 && $_POST['dest']	 	!= null  
 && $_POST['sync'] 		== 'yes') {

	if ($_POST['folder'] == null) {
		$_POST['folder'] = "";
	}
	

	$logger->log($uuid, 102, "Transfering " . $_POST['file'], data());

	$transfer = transfer($source[$_POST['src']] . $_POST['prefix']. $_POST['folder']."/".$_POST['file'],
			$destination[$_POST['dest']] . $_POST['folder']."/".$_POST['file']);
	

	if ($transfer[0] == 1) {
		$data = data();
		$data["error"] = $transfer[1];
		$logger->log($uuid, 500, "Error transfering " . $_POST['file'], $data);
		header("HTTP/1.0 500 Error transfering ".$uuid);
		exit;
	} else {
		$data = data();
		$data["out"] = $transfer[1];
		$logger->log($uuid, 201, "Success transfering " . $_POST['file'], $data);
		
		// report success via 'post_report' if populatuted
		if (isset($_POST['report'])) {
		  if (isset($post_report[$_POST['report']])) {
		    
		  } else {
		    // do nothing, invalid 'post_report' provided
		  }
		} else {
		  // do nothing, no post_report provided
		}
		
		// call 'post_action' if populated
		if (isset($_POST['action'])) {
		  if (isset($post_report[$_POST['action']])) {
		    
		  } else {
		    // do nothing, invalid 'post_report' provided
		  }
		} else {
		  // do nothing, no post_report provided
		}
		
	}

	header("Location: " . $endpoint . $uuid);
	exit;
	
} elseif ($_POST['sync'] == 'no') {
	//$uuid = shortUuid(uuid());
	$logger->log($uuid, 100, "Backgrounding transfer of " . $_POST['file'], data());
			
	exec("curl -d \"sync=yes&uuid=".$uuid."&src=".$_POST['src']."&dest=".$_POST['dest']."&prefix=".$_POST['prefix']."&folder=".$_POST['folder']."&file=".$_POST['file']."\" " . $selfUrl . " > /dev/null &");
	
	header("Location: " . $endpoint . $uuid);
	exit;
}
	
	
	
////////////////////////////////////////////////////

function selected($param, $val) {
	if ($_GET[$param] == $val) {
		return "selected='selected'";
	}
}

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" version="XHTML+RDFa 1.0" xml:lang="en-GB">
<head profile="http://www.w3.org/1999/xhtml/vocab http://www.w3.org/2003/g/data-view http://ns.inria.fr/grddl/rdfa/ http://purl.org/uF/2008/03/">
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<link href="stylesheets/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
  	<link href="stylesheets/print.css" media="print" rel="stylesheet" type="text/css" />
  	<!--[if IE]>
      <link href="stylesheets/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
  	<![endif]-->
	<style type="text/css">
	<!--/*--><![CDATA[/*><!--*/
	
	/*]]>*/-->
	</style>
	
	<script type="text/javascript">
	<!--//--><![CDATA[//><!-- 
	
	//--><!]]>
	</script>
</head>
<body>


<div id="container">
	<h1>Transfer</h1>
	<form method="post">
		<fieldset>
		<legend>Secure copy</legend>
		
		<label for="sync">Synchronous</label>
		<div class="input">
		<select name="sync" id="sync">
			<option value="yes" <?php echo selected("sync", "yes")?>>yes</option>
			<option value="no" <?php echo selected("sync", "no")?>>no</option>
		</select>
		</div>
		
		<label for="folder">Folder</label>
		<div class="input">
			<input type="text" name="folder" id="folder" value="<?php echo $_GET["folder"];?>" />
		</div>
		
		<label for="file">File</label>
		<div class="input">
			<input type="text" name="file" id="file" value="<?php echo $_GET["file"];?>" />
		</div>
		
		<label for="src">Source</label>
		<div class="input">
			<select name="src" id="src">
			<?php
				foreach($source as $key => $value) {
					echo "<option value='$key' ".selected("src", $key).">[$key] $value</option>";
				}
			?>
			</select>
		</div>
		
		<label for="dest">Destination</label>
		<div class="input">
			<select name="dest" id="dest">
			<?php
				foreach($destination as $key => $value) {
					echo "<option value='$key' ".selected("dest", $key).">[$key] $value</option>";
				}
			?>
			</select>
		</div>
		
		<input type="submit" value="Transfer" />
		
		</fieldset>
	</form>
</div>


</body>
</html>
