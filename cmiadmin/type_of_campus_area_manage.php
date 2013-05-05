<?php 
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme(); 
	$decription='';
	$message	= "";
	$master_table_name = 'campus_type';	

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);	
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'id', PARAM_TEXT);
	$dir			= optional_param('dir', 'ASC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 10;      
	$txtsearch_type	= optional_param('txtsearch_type', '', PARAM_RAW);

	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'page'			=> $page,
							'txtsearch_type'=> $txtsearch_type
						);	

	$page_var = http_build_query($page_arr,'','&amp;');	

	if(isset($_GET) && isset($_GET['delete']))
	{
		$status_id= required_param('delete', PARAM_INT);
		$status = 'delete';
		update_status_record($status_id,$status,$master_table_name);
	}

	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$status_id			= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);
		update_status_record($status_id,$status,$master_table_name);	
	}


	// Carry on with the user listing
	$columns = array('id','type','');
	$column = get_sorting_header($page_arr,$page_name,$theme,$columns);
	extract($column);

	$sort_term		= $sort." ".$dir;				
	$searchfields	= array("type");
	$extraselect	= " status != 'delete' ";
	if($txtsearch_type!='')
	{
		$extraselect .= " AND type LIKE '%".$txtsearch_type."%'";
	}

	$master_data = get_cmi_records($master_table_name, true, '*', $txtsearch_type, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$master_count = get_cmi_records($master_table_name, false, '', $txtsearch_type, $searchfields, $extraselect);

	if (!$master_data)
	{
		$match = array();
		$table = NULL;
	}
	else
	{
		$table->head = array ($id,$type,"");
		$table->align = array ("center","center","center");
		$table->width = "80%";
		foreach ($master_data as $data)
		{
			$status = 'active';
			$image = 'inactive.png';
			if($data->status == 'active')
			{
				$status = 'inactive';
				$image = 'active.png';
			}

			$editbutton = get_buttons("Edit $page_title", "$page_form?edit_load=$data->id","theme/$theme/images/edit.gif", " rel='lightbox[edit_$data->id 400 200]'");

			$disablebutton = get_buttons('Active/inactive',"$page_name?active_inactive=$data->id&status=$status&amp;$page_var&amp;page=$page"," theme/$theme/images/$image");

			$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id,'$page_name','$page_var','$page'); return false;\"");			

			$table->data[] = array (
					"$data->id",
					"$data->type",					
					$editbutton . $disablebutton . $deletebutton
			);
		}
	}
?>

<?php 
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
?>

<script type="text/javascript">

	window.addEvent('domready', function()
	{
		new FormCheck('frm_search');		
		show_tipz();			
	});

	
</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top" width="200"><?php load_menu(); ?></td>
		</tr>
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					Manage <?php echo $page_title;?>:
				</div>
				<a href="<?php echo $page_form; ?>" style="float:right;margin-right:60px;" rel="lightbox[set1 400 200]" class="tipz"  title=":: Add <?php echo $page_title;?>">
					<img src="<?php echo "theme/$theme/images/add.png" ?>" />
				</a>
				<br><br>
				<?php notify($message);?>
				<center>
					<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name; ?>">
						<table width="90%">
							<tbody>
								<tr>
									<td class="Label" align="center" width="90%">
										<table>
											<tr>
												<td>Type&nbsp;:&nbsp; </td>
												<td>
													<input type="text" size="40" name="txtsearch_type" id="txtsearch_type" class="txtBox" value="<?php echo $txtsearch_type; ?>">
												</td>
											</tr>											
											<tr>
												<td colspan="2">
													<center>
														<input name="btnSearch" id="btnSearch" type="submit" value="Search">
														<input name="btnreset" id="btnreset" type="button" value="Reset" onclick="txtsearch_type.value=''">
														<input name="btnclear" id="btnclear" type="button" value="Clear Search"  onclick="window.location ='<?php echo $page_name; ?>'">
													</center>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td align="center" width="90%"></td></tr>
								<tr>
									<td align="center" width="90%">
										<div>
											<?php
											if (!empty($table)) 
											{
												print_table($table);								
												print_paging_bar($master_count, $page, $perpage,"$page_name?$page_var&amp;");
											}
											else
											{	
											?>
												<table width= "600">
												<tr>
													<td align="center">No Records Found</td>
												</tr>
												</table>
											<?php
											}
											?>
										</div>
									</td>
								</tr>
								<tr><td width="90%"></td></tr>
							</tbody>
						</table>
					</form>
				</center>
			</td>
		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>