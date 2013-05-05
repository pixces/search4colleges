<?php 
	require_once("config.php");		
	$theme = current_theme();	

	$message = $date = $time =$edit_flag="";
	$master_table_name = 'testimonial';
	$master_image_path = 'testimonial';	

	$name				= optional_param('name','', PARAM_TEXT);	
	$testimonial_date	= optional_param('testimonial_date','', PARAM_INT);
	$sender_name		= optional_param('sender_name','', PARAM_RAW);
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

		$name				= $edit_data->name;
		$short_description	= $edit_data->short_description;
		$image				= $edit_data->image;
		$testimonial_date	= $edit_data->testimonial_date;
		$sender_name		= $edit_data->sender_name;
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


		$target_path = $CFG->dataroot."/$master_image_path/";
		
		if(!file_exists($target_path)){
			mkdir($CFG->dataroot."/$master_image_path",777);
		}
		$car_image = '';
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$update_mode_image = upload_image('image',$target_path);
			
			if($update_mode_image)
				$update_mode->image = $update_mode_image;
			else
				$update_mode->image = '';
		}
		$image = $update_mode;

		$update_mode->id				= $hiddenid;
		$update_mode->name				= $name;	
		$update_mode->short_description	= $short_description;
		$update_mode->testimonial_date	= $testimonial_date;
		$update_mode->sender_name		= $sender_name;
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
		$image					= '';

		$target_path = $CFG->dataroot."/$master_image_path/";
		if(!file_exists($target_path)){
			mkdir($CFG->dataroot."/$master_image_path/",777);
		}
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$add_mode_image = upload_image('image',$target_path);
			
			if($add_mode_image)
				$add_mode->image = $add_mode_image;
			else
				$add_mode->image = '';
		}
		$image = $add_mode_image;

		$add_mode->name =$name ;
		$add_mode->short_description	=$short_description ;
		$add_mode->testimonial_date		=$testimonial_date ;
		$add_mode->sender_name			=$sender_name ;
		$add_mode->image				= $image;
		$add_mode->added_date			=$added_date ;
		$add_mode->sort_order =$sort_order ;
		
		if(!empty($add_mode->name))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				cmi_add_to_log('category Management','Insert','New category '.$name.' added');
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
								<td class="Label" width="30%">Title Of Testimonial : </td>
								<td >
									<input name="name" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($name)) echo $name;?>" />
								</td>
							</tr>	
							<tr>
								<td class="Label" width="20%">Name of Sender  : </td>
								<td >
									<input name="sender_name" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($sender_name)) echo $sender_name;?>" />
									
								</td>
							</tr>
							<tr>
								<td class="Label" width="20%">Date  : </td>
								<td >
									<input readonly="true" name="testimonial_date" value="<?php echo $testimonial_date;?>" maxlength="30" id="date" style="width: 150px;" type="text" class="validate['required'] date  demo_vista_without_toggler" value=<?php echo $testimonial_date;?> />
								</td>
							</tr>
							<?php 
							if(isset($image) && $image != ''){ 
							?>
							<tr>
								<td class="Label" width="20%">Image:</td>
								<td >
									<a href="<?php echo $CFG->siteroot?>/file.php/<?php echo $master_image_path;?>/<?php echo $image; ?>" rel ="lightbox" title="<?php echo $image; ?>">
										<img src="<?php echo $CFG->siteroot?>/file.php/<?php echo $master_image_path;?>/f1_<?php echo $image; ?>" alt="<?php echo $image; ?>"/>
									</a>
								</td>
								<td>&nbsp;&nbsp;&nbsp;</td>
							</tr>
						<?php } ?>	
						 <tr>
							 <td class="Label" width="20%"><?php echo get_string('label_image',"cmi"); ?> 
							 </td>
							 <td ><input name="image"class="<?php if($edit_flag) { ?> validate['image'] <?php } else { ?>validate['required','image']<?php } ?> txtBox"  type="file" size="25" value="<?php if(isset($image)) echo $image;?>" /></td>
							 <td >&nbsp;&nbsp;&nbsp;</td>
						 </tr>

							<tr>
								<td class="Label" width="20%">Description</td>
								<td >
									<textarea name="short_description" id="short_description" class="validate['required'] txtBox" rows="3" cols="40"><?php if(isset($short_description)) echo $short_description;?></textarea>
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
									<input type="hidden" name="master_image_path" value="<?php echo $master_image_path;?>" />
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