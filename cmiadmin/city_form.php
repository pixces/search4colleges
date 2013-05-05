<?php 
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	//isallow();
	$message = "";
	
	$country_name = "";
	$country_id = "";
	$state_id = "";
	
	if(isset($_GET) && isset($_GET['edit']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit', PARAM_INT);
		$edit_data		= get_record('city', 'id', $edit_id);
		$cname			= $edit_data->name;
		$pincode		= $edit_data->pincode;
		$zipcode		= $edit_data->zipcode;
		
		$edit_data1		= get_record('state', 'id',$edit_data->state_id);
		$state_id		= $edit_data1->id;	
		
		$edit_data2		= get_record('country', 'id',$edit_data1->country_id);		
		$country_id		= $edit_data2->id;
		
	} 
	if(isset($_POST) && !empty($_POST) && !isset($_POST['search']) && !isset($_POST['delete_flag']))
	{
		
		$txtState		= optional_param('statename_form','', PARAM_RAW);
		$txtCname		= optional_param('cname','', PARAM_RAW);
		$txtPincode		= optional_param('pincode','', PARAM_RAW);
		$txtZipcode		= optional_param('zipcode','', PARAM_RAW);
		$added_date 	= time();

		$city				= new object();
		$city->name			= $txtCname;
		$city->pincode		= $txtPincode;
		$city->zipcode		= $txtZipcode;
		$city->status		= 'active';	
		$city->state_id		= $txtState;		
		$city->added_date	= $added_date;
		if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == "true"){
			$city_id	= required_param('city_id', PARAM_INT);
		}
		
		if(!empty($city->name))
		{
			if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == "true")
			{
				$city->id				= $city_id;	
				if(update_record('city', $city)){
					cmi_add_to_log('City','Update','City data is updated of city id='.$city_id);
					$message = "City Updated Successfully !!";
			?>
					<script  type="text/javascript">	
						parent.window.location = 'city_manage.php';
						parent.Mediabox.close();			
					</script>
			<?php
				}
			}
			else
			{	
				if(insert_record('city', $city)){
					cmi_add_to_log('City','Insert','New City '.$txtCname.' added');
					$message = "City Added Successfully !!";
			?>
					<script  type="text/javascript">	
						parent.window.location = 'city_manage.php';
						parent.Mediabox.close();			
					</script>
			<?php
				}
			}
		}
	}
	cmi_include_head('css');
	cmi_include_head('js');
?>
<script type="text/javascript">
		window.addEvent('domready', function(){
			new FormCheck('frm_addcity');
		});
		
		function get_state(id,flag){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'ajax_handler.php',
				 data: { 'id' : id,'flag' : 'country' },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(flag).innerHTML = response;
				}
			 
			}).send();	
			}
		}
</script>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	
		<td VALIGN="top">
			<div class="DotedHeader" width="70%">
				<?php if(isset($_GET['edit'])){ 
					echo 'Update City';
				 }
					else echo 'Add New City:'; 
				?>
			</div>
		    <br>
			<?php notify($message);?>
			
			<form method="post" name="frm_addcity" id="frm_addcity" action="city_form.php" enctype="multipart/form-data">
				<table border="0" width="100%" align="center" cellspacing="5" cellpadding="5">
					 <tbody>
						<!--<tr>
							 <td class="Label" width="20%"><?php echo 'Country name:'; ?> 
							 </td>
							 <td >
							 <select id="country" name="country" class="validate['required'] txtBox" onchange="javascript:get_state(this.value,'statename_form');">
								<?php //echo get_country_option($country_id); ?>
							</td>
						 </tr>-->
						 <tr>
							 <td class="Label" width="20%"><?php echo 'State name:'; ?></td>
							 <td>
								<select id="statename_form" name="statename_form" class="validate['required'] txtBox">
									<?php echo get_state_option($state_id,$country_id); ?>
								</select>							
							</td>
						 </tr>
						 <tr>
							 <td class="Label" width="20%"><?php echo 'City name:'; ?> 
							 </td>
							 <td ><input name="cname" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($cname)) echo $cname;?>" /></td>
						 </tr>
						 </tr>
						 <tr>
							 <td class="Label" width="20%"><?php echo 'Pincode:'; ?> 
							 </td>
							 <td ><input name="pincode" class="validate['number'] txtBox" type="text" size="11" value="<?php if(isset($pincode)) echo $pincode;?>" /></td>
						 </tr>						 
						 <tr>
							 <td class="Label" width="20%"><?php echo 'Zipcode:'; ?> 
							 </td>
							 <td ><input name="zipcode" class="validate['number'] txtBox" type="text" size="11" value="<?php if(isset($zipcode)) echo $zipcode;?>" /></td>
						 </tr>
						  <?php 
							if(isset($_GET['edit'])){
								$valuebutt = "btnUpdate.gif";
								$submitname = "updatecity";
								?>
								<input type="hidden" name="edit_mode" value="true" />
								<?php
								$hiddenid = $_GET['edit'];
							}
							else{ 
								$valuebutt = "btnSave.gif";
								$submitname = "addcity";
								?>
								<input type="hidden" name="addcity" value="addcity" />
								<?php
								$hiddenid = '';
							}
						?>
						 <tr>
							 <td colspan="2" style="text-align:left">
								<input type="hidden" name="city_id" value="<?php echo $hiddenid;?>" />
								<input name="<?php echo $submitname;?>" type="submit" value="Save">
							 </td>
						 </tr>

					</tbody>
				</table>
			</form>
		</td>
	</tr>
</table>