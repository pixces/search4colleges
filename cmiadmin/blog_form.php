<?php 
	require_once("config.php");		
	$theme = current_theme();	

	$message = $date = $time =$edit_flag="";
	$master_table_name = 'blog';

	$topic				= optional_param('title','', PARAM_TEXT);
	$category			= optional_param('description','', PARAM_RAW);
	$date				= optional_param('story','', PARAM_INT);
	$short_description	= optional_param('short_description','', PARAM_RAW);
	$sort_order			= get_sort_order($master_table_name);

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
		
	if(isset($_GET) && isset($_GET['edit_load']))
	{
		$caption_txt		= "Edit";
		$edit_flag			= true;
		$edit_id			= required_param('edit_load', PARAM_INT);
		$edit_data			= get_record($master_table_name, 'id', $edit_id);
		$topic				= $edit_data->topic;
		$category			= $edit_data->parent_id;
		$short_description	= $edit_data->short_description;		
		$date				= $edit_data->date;		
		$sort_order			= $edit_data->sort_order;
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
		$update_mode->topic				= $topic;
		$update_mode->parent_id			= $category;
		$update_mode->short_description	= $short_description;
		$update_mode->date				= $date;
		$update_mode->sort_order		= $sort_order;

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

		$add_mode	 = new object();	

		$add_mode->topic				=$topic ;
		$add_mode->parent_id			=$category;
		$add_mode->short_description	=$short_description ;
		$add_mode->date					=$date ;		
		$add_mode->added_date			=$added_date ;
		$add_mode->sort_order			=$sort_order ;
		
		if(!empty($add_mode->topic))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				cmi_add_to_log('category Management','Insert','New category '.$topic.' added');
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
	
	function get_category(id,flag,pid){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'test.php',
				 data: { 'id' : id,'flag' : flag },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(pid).innerHTML = response;
				}
			 
			}).send();	
			}
		}
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
								<td colspan="0" class="Label" width="20%"><?php echo get_string('label_category','cmi'); ?>
								</td>
								<td>
									<select id="category" name="category" class="txtBox">
									<?php echo get_category_option_blog($category); ?>									
									</select>
								</td>
								 <td>&nbsp;&nbsp;&nbsp;</td>
							</tr>
							<tr>
								<td class="Label" width="30%">Title: </td>
								<td >
									<textarea name="topic" id="topic" class="validate['required'] txtBox" rows="3" cols="40"><?php if(isset($topic)) echo $topic;?></textarea>
								</td>
							</tr>							
							<tr>
								<td class="Label" width="20%">Date  : </td>
								<td >
									<input readonly="true" name="date" value="<?php echo $date;?>" maxlength="30" id="date" style="width: 150px;" type="text" class="validate['required'] date  demo_vista_without_toggler" value=<?php echo $date;?> />
								</td>
							</tr>
							<tr>
								<td colspan="2" > 
									<span class="Label" align="center">Description</span> 
									<textarea name="short_description" id="editor1" class="editor txtBox"><?php 							if(isset($short_description)) echo $short_description;?></textarea>
										<script type="text/javascript">
											init_ckfinder('editor1');
										</script>
								 </td>
								
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