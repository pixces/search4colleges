<?php 
	require_once("config.php");	
	$theme = current_theme();	
	
	$message = $date = $time ="";
	$master_table_name = 'financial_provider';
	
	$contact_person			=	optional_param('contact_person','', PARAM_TEXT);
	$title					=	optional_param('title','', PARAM_TEXT);
	$company_name			=	optional_param('company_name','', PARAM_TEXT);
	$email_id				=	optional_param('email_id','', PARAM_TEXT);
	$contact_no				=	optional_param('contact_no','', PARAM_TEXT);
	$address				=	optional_param('address','', PARAM_TEXT);
	$zipcode				=	optional_param('zipcode','', PARAM_TEXT);
	$city					=	optional_param('city','', PARAM_TEXT);
	$state					=	optional_param('state','', PARAM_INT);
	$short_description		=	optional_param('short_description','', PARAM_TEXT);
	$time_from				=	optional_param('time_from','', PARAM_TEXT);
	$time_to				=	optional_param('time_to','', PARAM_TEXT);
	$image					=	optional_param('image','', PARAM_TEXT);
	

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
		
	if(isset($_GET) && isset($_GET['edit_load']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit_load', PARAM_INT);
		$edit_data		= get_record($master_table_name, 'id', $edit_id);
		$company_name	= $edit_data->company_name;
		$contact_person	= $edit_data->contact_person;		
		$title			= $edit_data->title;		
		$email_id		= $edit_data->email_id;		
		$contact_no		= $edit_data->contact_no;		
		$address		= $edit_data->address;
		$zipcode		= $edit_data->zipcode;
		$city			= $edit_data->city;
		$state			= $edit_data->state;		
		$short_description= $edit_data->short_description;		
		$time_from		= $edit_data->available_time_from;		
		$time_to		= $edit_data->available_time_to;		
		$image			= $edit_data->Logo;		
				
		
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
		$update_mode->company_name		= $company_name;
		$update_mode->contact_person	= $contact_person;		
		$update_mode->title				= $title;		
		$update_mode->address			= $address;
		$update_mode->zipcode			= $zipcode;
		$update_mode->state				= $state;		
		$update_mode->email_id			= $email_id;		
		$update_mode->contact_no		= $contact_no;		
		$update_mode->short_description	= $short_description;		
		$update_mode->available_time_from= $time_from;		
		$update_mode->available_time_to	= $time_to;		
		                            
									$city_string=$city;
									$sql = ("SELECT * FROM {$CFG->prefix}city WHERE name LIKE '%$city_string%' and status = 'active'");
                                    $city_data = get_record_sql($sql);
                                    
                                    if(!empty($city_data)){
									    $update_mode->city = $city_data->id;
                                        
                                        }else{
                                            $rec  = execute_sql("INSERT INTO ".$CFG->prefix."city SET
                                                                        state_id		= ".$state.", 	
                                                                        pincode 		= '',
                                                                        zipcode 		= '".$zipcode."',
                                                                        code 			= '".$city_string."',
                                                                        name 			= '".$city_string."',
                                                                        added_date 		= ".time().",
                                                                        status 			= 'active'",false);
                            
                                            if($rec){
                                                    $city_id = mysql_insert_id();
													$update_mode->city = $city_id;
                                                 }
                                    }
				
				
		
		$target_path = $CFG->dataroot."/financial_provider_logo/";		
		if(!file_exists($target_path)){
			mkdir($CFG->dataroot."/financial_provider_logo",777);
		}
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$update_mode_image = upload_image('image',$target_path);			
			if($update_mode_image)
				$update_mode->Logo = $update_mode_image;
			else
				$update_mode->Logo = '';
		}
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

		$add_mode					  = new object();
		$add_mode->added_date		  = $added_date ;
		$add_mode->company_name		  = $company_name;		
		$add_mode->contact_person	  = $contact_person;		
		$add_mode->title			  = $title;		
		$add_mode->address			  = $address;
		$add_mode->zipcode			  = $zipcode;
		$add_mode->state			  = $state;		
		$add_mode->email_id			  = $email_id;		
		$add_mode->contact_no		  = $contact_no;		
		$add_mode->short_description  = $short_description;		
		$add_mode->available_time_from= $time_from;		
		$add_mode->available_time_to  = $time_to;	

									$city_string=$city;
									$sql = ("SELECT * FROM {$CFG->prefix}city WHERE name LIKE '%$city_string%' and status = 'active'");
                                    $city_data = get_record_sql($sql);
                                    
                                    if(!empty($city_data)){
									    $add_mode->city = $city_data->id;
                                        
                                        }else{
                                            $rec  = execute_sql("INSERT INTO ".$CFG->prefix."city SET
                                                                        state_id		= ".$state.", 	
                                                                        pincode 		= '',
                                                                        zipcode 		= '".$zipcode."',
                                                                        code 			= '".$city_string."',
                                                                        name 			= '".$city_string."',
                                                                        added_date 		= ".time().",
                                                                        status 			= 'active'",false);
                            
                                            if($rec){
                                                    $city_id = mysql_insert_id();
													$add_mode->city = $city_id;
                                                 }
                                    }
		
		$target_path = $CFG->dataroot."/financial_provider_logo/";		
		if(!file_exists($target_path)){
			mkdir($CFG->dataroot."/financial_provider_logo",777);
		}
		if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
			$update_mode_image = upload_image('image',$target_path);			
			if($update_mode_image)
				$add_mode->Logo = $update_mode_image;
			else
				$add_mode->Logo = '';
		}
		
		if(!empty($add_mode->email_id))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				cmi_add_to_log('Financial Provider Management','Insert','New category '.$contact_person.' added');
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
		new DatePicker('.demo_vista', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					format: 'd F, Y',
			});
		mootime('time_from');
		mootime('time_to');
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
							 <td class="Label" width="20%"><?php echo 'Company Name:'; ?> 
							 </td>
							 <td ><input name="company_name" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($company_name)) echo $company_name;?>" /></td>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'Contact Person:'; ?> 
							 </td>
							 <td ><input name="contact_person" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($contact_person)) echo $contact_person;?>" /></td>
						</tr>	
						<tr>
							 <td class="Label" width="20%"><?php echo 'Title:'; ?> 
							 </td>
							 <td ><input name="title" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($title)) echo $title;?>" /></td>
						</tr>	
						<tr>
							 <td class="Label" width="20%"><?php echo 'Email Id:'; ?> 
							 </td>
							 <td ><input name="email_id" class="validate['required','email'] txtBox" type="text" size="25" value="<?php if(isset($email_id)) echo $email_id;?>" /></td>
						</tr>	
						<tr>
							 <td class="Label" width="20%"><?php echo 'Contact Number:'; ?> 
							 </td>
							 <td ><input name="contact_no" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($contact_no)) echo $contact_no;?>" /></td>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'Addess:'; ?> 
							 </td>
							 <td ><textarea name="address" class="validate['required'] txtBox" type="text" rows="5" cols='25' /><?php if(isset($address)) echo $address;?></textarea>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'Zip Code:'; ?> 
							 </td>
							 <td ><input name="zipcode" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($zipcode)) echo $zipcode;?>" /></td>
						</tr>
						<tr>
							 <?php $city = get_field('city','name','id',$city); ?>
							 <td class="Label" width="20%"><?php echo 'City:'; ?> 
							 </td>
							 <td ><input name="city" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($city)) echo $city;?>" /></td>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'State:'; ?> 
							 </td>
							 <td >
							 <?php
									$getstate = get_records_sql('SELECT * FROM '.$CFG->prefix.'state WHERE status="active"');
									echo '<select name="state" id="state">';
									echo '<option value="">Select State</option>';
									foreach($getstate as $data)
									{
										if($data->id == $state)
										{
											echo '<option value="'.$data->id.'" selected="selected" >'.$data->name.'</option>';
										}
										else
										{
											echo '<option value="'.$data->id.'">'.$data->name.'</option>';
										}
										
									}
								echo '</select>';
								?>
							 </td>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'Short Description:'; ?> 
							 </td>
							 <td ><textarea name="short_description" class="validate['required'] txtBox" type="text" rows="5" cols='25' /><?php if(isset($short_description)) echo $short_description;?></textarea>
						</tr>
						<tr>
							 <td class="Label" width="20%"><?php echo 'Available Time From - TO:'; ?> 
							 </td>
							 <td ><input type='text' readonly="true" name='time_from' id='time_from' value="<?php if(isset($time_from)) echo $time_from;?>"/>-<input type='text' readonly="true" name='time_to' id='time_to' value="<?php if(isset($time_to)) echo $time_to;?>"/></td>
						</tr>	
						<tr>
								 <td class="Label" width="20%"><span style="font-size: 12px; font-family : Verdana;">Logo</span> :
								 </td>
								 <td>
								 <?php if(isset($edit_flag) && !empty($edit_flag)) { ?>
										<input name="image" id="image" class="validate['image'] txtBox" type="file" />
								 <?php } else { ?> <input name="image" id="image" class="validate['image'] txtBox" type="file" /> 
								 <?php } if(isset($edit_flag) && !empty($edit_flag)) {
								  		if(isset($image) && $image != '')
										{
											if(file_exists($CFG->dataroot.'/financial_provider_logo/f2_'.$image))
											{												
								 ?>					
												&nbsp;&nbsp;<a rel="lightbox[img]" href="<?php echo $CFG->siteroot; ?>/file.php/financial_provider_logo/<?php echo $image; ?>" title="<?php echo $image; ?>"><img src="<?php echo $CFG->siteroot; ?>/file.php/financial_provider_logo/f2_<?php echo $image; ?>" alt="<?php echo $image; ?>" height="50"/></a>	
												<br>

								 <?php } } } ?>
								 </td>
								 <td >&nbsp;&nbsp;&nbsp;</td>
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