<?php  /// Moodle Configuration File 

if($_POST){
	if($_POST['dbtype'] && $_POST['dbhost'] && $_POST['dbname'] && $_POST['dbuser'] && $_POST['dbpass'] && $_POST['dbpersist'] && 	$_POST['dataroot'] != ''){
		
		$myFile = "config.php";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = "<?php\n";
		$stringData .= 'unset($CFG);'."\n\n";
		$dataroot = '';
		foreach($_POST as $key=> $value){
			if($key != 'submit' && $key != 'dataroot'){
				if($key == 'dbpersist'){
					$stringData .= '$CFG->'.$key." = ".$value.";\n";
				}
				else{
					$stringData .= '$CFG->'.$key." = '".$value."';\n";
				}
			}
		}

		/* $CFG->wwwroot */
		
		$replaced = str_replace('/make_config.php','',$_SERVER['REQUEST_URI']);
		$replaced = "http://".$_SERVER['HTTP_HOST'] .$replaced;
		$wwwroot = '$CFG->wwwroot = "'.$replaced.'";';
		$stringData .= "\n$wwwroot";
		
		/* $CFG->siteroot */
		
		$replaced = str_replace('/cmiadmin/make_config.php','',$_SERVER['REQUEST_URI']);
		$replaced = "http://".$_SERVER['HTTP_HOST'] .$replaced;
		$siteroot = '$CFG->siteroot = "'.$replaced.'";';
		$stringData .= "\n$siteroot";
		
		/* $CFG->dirroot */

		$replaced  = str_replace('/make_config.php','',$_SERVER['SCRIPT_FILENAME']);
		$dirroot = '$CFG->dirroot = "'.$replaced.'";';
		$stringData .= "\n$dirroot\n";
		
		/* $CFG->datroot */

		if($_POST['dataroot']){
					$key  = 'dataroot';
					$replaced  = str_replace('cmiadmin/make_config.php','',$_SERVER['SCRIPT_FILENAME']);
					$dataroot = '$CFG->'.$key." = '".$replaced.$_POST['dataroot']."';\n";
		}
		$stringData .= "$dataroot";
		$stringData  .= '$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode'."\n\n";

		$stringData  .= 'require_once("$CFG->dirroot/lib/setup.php")'.";\n";
		$stringData  .= 'require_once("cmi_lib.php");'."\n";
		
		$stringData .= "?>";

		if(fwrite($fh, $stringData)){			
		}
		if($_POST['server_status'] == 'live')
			copy('config.php', 'live_config.php');
		fclose($fh);

		header('Location:upgrade.php');
	}
}

?>

<form method="post" name="frm_adduser" id="frm_adduser" action="make_config.php" enctype="multipart/form-data">
	<table border="0" width="85%" align="center" cellspacing="5" cellpadding="5">
			 <tbody>
				<tr>
					 <td align="center"></td>
					 <td >Config Maker:</td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
 				 <tr>
					 <td align="right">Sever Type:
					 </td>
					 <td >
						 <input name="server_status" type="radio" value="dev" /> Development
						 <input checked="checked" name="server_status" type="radio" value="live" /> Live
					 </td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Type:
					 </td>
					 <td ><input name="dbtype" type="text" value="mysql" /></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Host:</td>
					 <td ><input name="dbhost" type="text" value="localhost" /></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Name:</td>
					 <td ><input name="dbname" type="text" ></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database User:</td>
					 <td ><input name="dbuser" type="text" ></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Pass:</td>
					 <td ><input name="dbpass" type="text" ></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Persist:</td>
					 <td ><input name="dbpersist" type="text" value="false" ></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td align="right">Database Prefix:</td>
					 <td ><input name="prefix" type="text" value=""></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				  <tr>
					 <td align="right">File Data Root:</td>
					 <td ><input name="dataroot" type="text" value="uploads"></td>
					 <td width="30%">&nbsp;&nbsp;&nbsp;</td>
				 </tr>
				 <tr>
					 <td colspan="2" style="text-align:center">
						
						<input name="submit" id="btnSave" type="submit">
					 </td>
				 </tr>
			</tbody>
		</table>
	</form>

