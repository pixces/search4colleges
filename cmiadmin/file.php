<?php 	
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();

	if(isset($_POST) && !empty($_POST))
	{
		$txtPageContents		= required_param('txtPageContents', PARAM_RAW);
		$manage_page_content_id	= required_param('manage_page_content_id', PARAM_RAW);
		$added_date 			= time();

		$page_content_settings					= new object();
		$page_content_settings->page_contents	= $txtPageContents;

		if(!empty($page_content_settings->page_contents))
		{
			$page_content_settings->id					= $manage_page_content_id;				
			$page_content_settings->last_modified_date	= $added_date;
			if(update_record('page_contents', $page_content_settings))
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
		$edit_data				= get_record('page_contents', 'id', $edit_id);
		$txtPageName			= $edit_data->page_name;
		$txtPageContents		= $edit_data->page_contents;
	}

	//print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	cmi_include_editor();
	//print_container_start();
?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		new FormCheck('frm_manage_page_content');
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
			<form method="post" action="<?php echo $CFG->wwwroot;?>/page_content_form.php" class="long" id="frm_manage_page_content" name="frm_manage_page_content">
			<input name="edit_mode" type="hidden" value="true"/>
			<input name="manage_page_content_id"  type="hidden"  value="<?php echo $edit_data->id;?>"/>
			<table width="100%" border="0">
			  <tbody>
				<tr> 
				  <td width="1%"> </td>
				  <td align="center" width="98%" colspan="2"> </td>
				  <td width="1%"> </td>
				</tr>
				<tr> 
				  <td width="1%"> </td>
				  <td class="DotedHeader" width="12%"> Edit <?php echo $txtPageName;?>:</td>
				  <td align="right" width="86%"><input name="Save" id="Save" type="submit" value="Save"></td>
				  <td width="1%">  </td>
				</tr>
				<tr> 
				  <td width="1%"> </td>
				  <td class="Label" width="12%"> <span style="font-size: 12px; font-family : Verdana;">Page Name</span> : &nbsp;</td>
				  <td width="86%"><?php echo $txtPageName;?></td>
				  <td width="1%"> </td>
				</tr>
				
				<tr> 
				  <td width="1%"> </td>
				  <td class="Label" width="12%"><span style="font-size: 12px; font-family : Verdana;">Page Content</span> : &nbsp;</td>
				  <td width="86%">
					<textarea name="txtPageContents" id="txtPageContents" style="width: 250px;height:100px" class="validate['required'] editor txtBox"><?php echo $txtPageContents;?></textarea>
					<script type="text/javascript">
					init_ckfinder('txtPageContents');
					</script>
				</td>
				  <td width="1%"> </td>
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