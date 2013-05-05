<?php 	
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();	

	if(isset($_POST) && !empty($_POST))
	{
		$txtMetaTitle		= required_param('txtMetaTitle', PARAM_RAW);
		$txtMetaDescription	= required_param('txtMetaDescription', PARAM_RAW);
		$txtMetaKeywords	= required_param('txtMetaKeywords', PARAM_RAW);
		$seo_id				= required_param('seo_id', PARAM_RAW);
		$added_date 		= time();

		$seo_settings					= new object();
		$seo_settings->meta_title		= $txtMetaTitle;
		$seo_settings->meta_description	= $txtMetaDescription;
		$seo_settings->meta_keywords	= $txtMetaKeywords;

		if(!empty($seo_settings->meta_title))
		{
			$seo_settings->id					= $seo_id;				
			$seo_settings->last_modified_date	= $added_date;
			if(update_record('seo_setting', $seo_settings))
			{
	?>		
				<script  type="text/javascript">	
					parent.Mediabox.close();			
					window.location = 'seo_manage.php';
				</script>
	<?php
			}
		}
	}
	else
	{
		$edit_id				= required_param('edit', PARAM_INT);
		$edit_data				= get_record('seo_setting', 'id', $edit_id);
		$txtPageName			= $edit_data->page_name;
		$txtMetaTitle			= $edit_data->meta_title;
		$txtMetaDescription		= $edit_data->meta_description;
		$txtMetaKeywords		= $edit_data->meta_keywords;
	}

	//print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');

	print_container_start();
?>
<script type="text/javascript">
			window.addEvent('domready', function(){
    new FormCheck('frm_seo');
});
</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top">
			<?php //load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo $CFG->wwwroot;?>/seo_form.php" class="long" id="frm_seo" name="frm_seo">
			<input name="edit_mode" type="hidden" value="true"/>
			<input name="seo_id"  type="hidden"  value="<?php echo $edit_data->id;?>"/>
			<table width="100%" border="0">
			  <tbody>
				<tr> 
				  <td width="5%"> </td>
				  <td align="center" width="90%" colspan="2"> </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="DotedHeader" width="90%" colspan="2"> Edit <?php echo $txtPageName;?>:</td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td style="height: 21px;" width="5%"> </td>
				  <td style="height: 21px;" width="90%" colspan="2"> </td>
				  <td style="height: 21px;" width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="15%"><span style="font-size: 12px; font-family : Verdana;">Page Name</span> : &nbsp;</td>
				  <td width="75%"><?php echo $txtPageName;?></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="15%"><span style="font-size: 12px; font-family : Verdana;">Meta Title</span> : &nbsp;</td>
				  <td width="75%">
					<textarea name="txtMetaTitle" id="txtMetaTitle" style="width: 600px;height:55px" class="validate['required'] txtBox"><?php echo $txtMetaTitle;?></textarea>
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
				  <td class="Label" width="15%"><span style="font-size: 12px; font-family : Verdana;">Meta Description</span> : &nbsp;</td>
				  <td width="75%"><textarea name="txtMetaDescription" id="txtMetaDescription" style="width: 600px;height:55px" class="validate['required'] txtBox"><?php echo $txtMetaDescription;?></textarea></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="15%"><span style="font-size: 12px; font-family : Verdana;">Meta Keywords</span> : &nbsp;</td>
				  <td width="75%"><textarea name="txtMetaKeywords" id="txtMetaKeywords" style="width: 600px;height:55px" class="validate['required'] txtBox"><?php echo $txtMetaKeywords;?></textarea></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> <input name="Save" id="Save" type="submit" value="Save"></td>
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
	//print_container_end();
	//print_footer();
?>