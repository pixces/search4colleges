<?php 
	require_once("config.php");	
	$theme = current_theme();		
	$message = $type = '';
	$master_table_name = 'campus_type';
	$type = optional_param('type','', PARAM_TEXT);	
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
	
	if(isset($_GET) && isset($_GET['edit_load']))
	{
		$caption_txt		= "Edit";
		$edit_flag			= true;
		$edit_id			= required_param('edit_load', PARAM_INT);
		$edit_data			= get_record($master_table_name, 'id', $edit_id);
		$type				= $edit_data->type;		
	}
	else
	{
		$edit_id = 0;
	}

	if(isset($_POST['update_mode']))
	{
		$hiddenid = optional_param('hiddenid','', PARAM_TEXT);		
		$added_date = '';

		$update_mode = new object();
		$update_mode->id = $hiddenid;
		$update_mode->type = $type;		

		if(!empty($update_mode))
		{
			$update = update_record($master_table_name, $update_mode);
			if($update)
			{
?>
				<script  type="text/javascript">	
					parent.window.location = '<?php echo $page_manage; ?>';
					parent.Mediabox.close();			
				</script>
<?php
			}
		}
	}

	if( isset($_POST['add_mode']) && $_POST['add_mode'] )
	{			
		$added_date	= time();
		$allowed_sections = "";

		$add_mode	 = new object();
		$add_mode->type = $type;
		$add_mode->added_date = $added_date ;
	
		if($new = insert_record($master_table_name, $add_mode))
		{
			cmi_add_to_log('School Type','Insert','New school_type '.$type.' added');
?>	
			<script  type="text/javascript">
				parent.window.location = '<?php echo $page_manage; ?>';
				parent.Mediabox.close();			
			</script>
<?php
		}
		
	}	

	cmi_include_head('css');
	cmi_include_head('js');
	cmi_include_editor();	

?>

<script type="text/javascript">

	window.addEvent('domready', function()
	{		
		new FormCheck('frm_add_mode');			
	});

</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">	
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					<?php
					if(isset($_GET['edit_load']))
					{ 
						echo "Update $page_title :";
					}
					else 
						echo "Add $page_title:"; 
					?>
				</div>
				<br>

				<?php notify($message);?>

				<form method="post" name="frm_add_mode" id="frm_add_mode" action="<?php echo $page_name; ?>" enctype="multipart/form-data">
					<table border="0" width="100%" align="center" cellspacing="1" cellpadding="1">
						<tbody>
							<tr>
								<td colspan="2"><br></td>												
							</tr>
							<tr>
								<td class="Label" width="20%"><span style="font-size: 12px; font-family : Verdana;">Type</span> : </td>
								<td >
									<input name="type" class="validate['required'] txtBox" type="text" size="40" value="<?php echo $type; ?>" />
								</td>
							</tr>	
							<tr>
								<td colspan="2"><br></td>												
							</tr>
							<?php 
							if(isset($_GET['edit_load']))
							{
								$submitname = "update_mode";
								?>
								<input type="hidden" name="update_mode" value="update_mode" />
								<?php
								$hiddenid = $_GET['edit_load'];
							}
							else
							{ 
								$submitname = "add_mode";
								?>
								<input type="hidden" name="add_mode" value="add_mode" />
								<?php
								$hiddenid = '';
							}
							?>
							<tr>
								<td></td>
								<td>									
									<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />									
									<input name="<?php echo $submitname; ?>" type="submit" value="Save">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</td>
		</tr>
	</table>