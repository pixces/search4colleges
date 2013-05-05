<?php 	
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();

	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');

	print_container_start();

	$admin_access = optional_param('admin','',PARAM_TEXT);
	
	$captions_array = array(
								'testimonials_perpage'=>array('Testimonials Per Page','Testi Description'), 
								'perpage'=>array('Admin Per Page','Records to be shown in per page'),
								'image_limit'=>array('User Image Upload Limit',''),
								'video_limit'=>array('User Video Upload Limit',''),
								'log_per_page'=>array('Log Per Page','Records to be shown in Log report'),
								'backup_frommail'=>array('Backup Mail From','Email addresss from which Back Mail is Sent'),
								'backup_tomail'=>array('Backup Mail To','Email addresss to which Back Mail is Sent')
							);

	if(isset($_POST) && !empty($_POST))
		{	
			echo '<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td VALIGN="top" width="200">';
							load_menu(); 
						echo '</td></tr><tr>
						<td VALIGN="top">';
							$added_date 		= time();
							for($i=0; $i<count($_POST['ids']); $i++)
							{
								$config_id	= $_POST['ids'][$i];
								$txtValue	= $_POST['values'][$i];

								$config						= new object();
								$config->value				= $txtValue;
								$config->id					= $config_id;				
								$config->last_modified_date	= $added_date;
								
								update_record('config', $config);						
							}
							cmi_add_to_log('Misc','Config Setting','Config setting updated by id='.$_SESSION['user_id']);
							$message = "Config Settings Updated Successfully !!";
							echo "<br><br><br>";
							notify($message);
echo '</td></table>';
		}
	else{
	$config_data	= get_records('config','','','name');
?>
<script type="text/javascript">
			window.addEvent('domready', function(){
    new FormCheck('frm_config');
});
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo $CFG->wwwroot;?>/config_manage.php" class="long" id="frm_config" name="frm_config">
			<input name="edit_mode" type="hidden" value="true"/>
				<table width="100%" border="0">
					<tbody>
						<tr> 
							<td width="5%"> </td>
							<td align="center" width="90%" colspan="2"> </td>
							<td width="5%"> </td>
						</tr>
						<tr> 
							<td width="5%"> </td>
							<td class="DotedHeader" width="90%" colspan="2"> Edit Config Settings:</td>
							<td width="5%"> </td>
						</tr>
						<tr> 
							<td style="height: 21px;" width="5%"> </td>
							<td style="height: 21px;" width="90%" colspan="2"> </td>
							<td style="height: 21px;" width="5%"> </td>
						</tr>
						<?php
							foreach($config_data as $key=>$config){
								$config_value = $config->value;
								if($admin_access != 'true')
								{
									if(isset($captions_array[$config->name])){
										if(is_array($captions_array[$config->name])){
											$label_name = $captions_array[$config->name][0];
											$description = $captions_array[$config->name][1];
										}
										else{
											$label_name = $captions_array[$config->name];
											$description = '';
										}
									}
									else{
											$label_name = '';
									}
								}
								else
								{
									$label_name = $config->name;
									$description = '';
								}
								if(!empty($label_name))
								{
								
									$config_value = htmlentities($config->value);
								?>
									
									<tr> 
									  <td width="5%"> </td>
									  <td class="Label" width="30%"> <?php echo $label_name; ?> : &nbsp;</td>
									  <td width="20%">
										<input name="ids[]" value="<?php echo $config->id; ?>" type="hidden" />
										<input name="values[]" value="<?php echo $config_value;?>" type="text" class="txtBox" />
										</td>
									  <td width="30%" class="config_desc"><?php echo $description; ?></td>
									</tr>

									<tr> 
									  <td width="5%"> </td>
									  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
									  <td width="5%"> </td>
									</tr>
								<?php
								}
							}
						?>
						<tr> 
							<td width="5%"> </td>
							<td width="90%" colspan="2"> <input name="btnSave" id="btnSave" type="submit" value="Save"></td>
							<td width="5%"> </td>
						</tr>
						<tr> 
							<td width="5%"> </td>
							<td width="90%" colspan="2"> </td>
							<td width="5%"> </td>
						</tr>
					</tbody>
				</table>
			</form>
		</td>
	</tr>
</table>
	<?php
	}		
	print_container_end();

	print_footer();
?>