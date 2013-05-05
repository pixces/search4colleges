<?php 
	require_once("config.php");	
	$theme = current_theme();	
	
	$message = $date = $time ="";
	$master_table_name = 'faq';

	$txtQuestion	= optional_param('txtQuestion','', PARAM_RAW);
	$txtAnswer		= optional_param('txtAnswer','', PARAM_RAW);
	$txtSortOrder	= optional_param('txtSortOrder', '1', PARAM_INT);	
	$txtSortOrder	= get_sort_order($master_table_name);

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
		
	if(isset($_GET) && isset($_GET['edit_load']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit_load', PARAM_INT);
		$edit_data		= get_record($master_table_name, 'id', $edit_id);
		$txtQuestion	= $edit_data->question;
		$txtAnswer		= $edit_data->answer;
		$txtSortOrder	= $edit_data->sort_order;
	}
	else
	{
		$edit_id = 0;
	}

	if(isset($_POST['update_mode']))
	{
		$hiddenid			= optional_param('hiddenid','', PARAM_TEXT);		
		$added_date			= '';

		$update_mode = new object();

		$update_mode->id				= $hiddenid;
		$update_mode->question			= $txtQuestion;
		$update_mode->answer			= $txtAnswer;
		$update_mode->sort_order		= $txtSortOrder;
		
		$message = '';
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
		$added_date 			= time();
		$allowed_sections		= "";

		$add_mode				= new object();
		$add_mode->question		=$txtQuestion ;
		$add_mode->answer		=$txtAnswer ;
		$add_mode->sort_order	=$txtSortOrder ;
		$add_mode->added_date	=$added_date ;
		
		
		if(!empty($add_mode->answer))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				//print_r($add_mode);
				//exit();
				
				cmi_add_to_log('FAQ','Insert','New faq '.$answer.' added');
				?>	<script  type="text/javascript">
					parent.window.location = '<?php echo $page_manage; ?>';
					parent.Mediabox.close();			
				</script>
				<?php
			}
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
		get_datepicker_without_toggler();
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
							  <td width="5%"> </td>
							  <td align="center" width="90%" colspan="2"> </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="DotedHeader" width="90%" colspan="2">FAQ:</td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td style="height: 21px;" width="5%"> </td>
							  <td style="height: 21px;" width="90%" colspan="2"> </td>
							  <td style="height: 21px;" width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%" valign="top"> Question : &nbsp;</td>
							  <td width="70%">
							  <textarea name="txtQuestion" id="editor1" class="validate['required'] txtBox" style="width: 350px;height:200px;"/><?php if(isset($txtQuestion)) echo $txtQuestion;?></textarea>
							  </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%" valign="top"> Answer : &nbsp;</td>
							  <td width="70%"><textarea name="txtAnswer" maxlength="255" id="txtAnswer" style="width: 350px;height:200px;" class="validate['required'] txtBox"><?php if(isset($txtAnswer)) echo $txtAnswer;?></textarea></td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>	
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%"> Sort Order : &nbsp;</td>
							  <td width="70%"><input name="txtSortOrder" value="<?php echo $txtSortOrder;?>" maxlength="10" id="txtSortOrder" style="width: 350px;" type="text" class="validate['required','digit'] txtBox" /></td>
							  <td width="5%"> </td>
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
								<td colspan="2" style="text-align:center">
									<input type="hidden" name="master_table_name" value="<?php echo $master_table_name;?>" />
									<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />								
									<input name="<?php echo $submitname;?>" type="submit" value="Save">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</td>
		</tr>
	</table>