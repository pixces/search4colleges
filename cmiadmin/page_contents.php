<?php 	
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag			= false;
	$caption_txt		= "Add";
	if(isset($_GET['edit']))
		$edit_id			= optional_param('edit', PARAM_INT);
	else
		$edit_id	= optional_param('page_contents_id', PARAM_INT);
	
	$edit_data			= get_record('page_contents', 'id', $edit_id);
	$txtPageName		= $edit_data->page_name;
	$txtPageContents	= $edit_data->page_contents;

	if(isset($_POST['submit'])){
		$id 		= optional_param('page_contents_id', PARAM_INT);
		$page_contents 	= optional_param('page_contents', PARAM_INT);

		$update = new object();
		$update->id = $id;
		$update->page_contents = $page_contents;
		$update->last_modified_date  = time();

		if(!empty($update)){
			if(update_record('page_contents', $update)){
				cmi_add_to_log('page_contents','Added page content','updated record id: '.$id);
				$message = "Page_Contents Updated Successfully !!";		
			}

		}
	}

	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');

	print_container_start();
?>
<script type="text/javascript">
			window.addEvent('domready', function(){
				new FormCheck('frm_blog');
});
</script>
<link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/lib/editor/ckeditor/samples/sample.css" type="text/css">
<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/lib/editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/lib/editor/ckeditor/samples/sample.js"></script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo $CFG->wwwroot;?>/page_contents.php" class="long" id="frm_blog" name="frm_blog">
			<input name="edit_mode" type="hidden" value="true"/>
			<input name="page_contents_id"  type="hidden"  value="<?php echo $edit_id;?>"/>
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
				  <td width="90%">
				  	<textarea name="page_contents" id="editor1" style="width: 150px;" class="txtBox" /><?php echo $txtPageContents;?></textarea>
					<script type="text/javascript">
						CKEDITOR.replace( 'editor1',
						{
							filebrowserBrowseUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/ckfinder.html',
							filebrowserImageBrowseUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/ckfinder.html?Type=Images',
							filebrowserFlashBrowseUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/ckfinder.html?Type=Flash',
							filebrowserUploadUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
							filebrowserImageUploadUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
							filebrowserFlashUploadUrl : 'http://192.168.0.103/cmiadmin@php/cmiadmin/lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
						});
					</script>
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
				  <td width="90%" colspan="2"><input name="submit" type="hidden" /><input name="save" id="btnSave" src="theme/<?php echo $theme; ?>/images/btnSave.gif" style="border-width: 0px;" type="image"></td>
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
	print_container_end();
	print_footer();
?>