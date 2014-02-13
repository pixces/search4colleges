<?php 
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme();	
			
	$name='';
	$message	= "";
	$approved_msg = "";
	$master_table_name = 'school_image_video';
	$master_image_path = 'school_gallery';

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'date', PARAM_TEXT);
	$dir			= optional_param('dir', 'DESC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 20;      
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);

	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'txtsearch_name'=> $txtsearch_name
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

	if(isset($_GET) && isset($_GET['approved_declined']))
	{
		$id					= required_param('approved_declined', PARAM_INT);
		$status				= required_param('approved', PARAM_TEXT);

		$update_mode =new object();
		$update_mode->id =$id;
		if($status=="unpublish")
		{
		$update_mode->publish = '0';
		}else
		{
		$update_mode->publish = '1';
		}
		
		update_record($master_table_name, $update_mode);
	}	

	// Carry on with the user listing
	$columns = array("Name","image");	
	$column = get_sorting_header($page_arr,$page_name,$theme,$columns);
	extract($column);

	$sort_term		= $sort." ".$dir;				
	$searchfields	= array("name");
	$extraselect	= " status != 'delete'";

	$master_data		= get_cmi_records($master_table_name, true, '*', $txtsearch_name, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$master_count		= get_cmi_records($master_table_name, false, '', $txtsearch_name, $searchfields, $extraselect);

	if (!$master_data)
	{
		$match = array();
		$table = NULL;
		
	}
	else
	{
		$table->head = array ('Name',$image,"status");
		$table->align = array ("center","center", "center","center");
		$table->width = "100%";
		$get_count=count($master_data);
		
		foreach ($master_data as $data)
		{
			
			$approved = 'publish';
			$image = 'inactive.png';
			if($data->publish == '1')
			{
				$approved = 'unpublish';
				$image = 'active.png';
			}
			if($data->type =='image')
			{
				$approved_msg=$approved .'&nbsp;&nbsp;image';
			}
			if($data->type =='video')
			{
				$approved_msg=$approved .'&nbsp;&nbsp;video';
			}

			$approvedbutton = get_buttons($approved_msg,"$page_name?approved_declined=$data->id&approved=$approved&amp;$page_var&amp;page=$page"," theme/$theme/images/$image");

			$name='';
			$type='';
			$display='';
			if($data->filename!='' && $data->type=='image')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->filename' rel ='lightbox' title='$data->filename'><img src='$CFG->siteroot/file.php/$master_image_path/f1_$data->filename' alt='$data->filename'/></a>";
				}

				elseif($data->filename!='' && $data->type=='video')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->filename' rel ='lightbox' title='$data->filename'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$data->filename'/></a>";
					
				}
				elseif($data->filename!='' && $data->type=='link')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->filename' rel ='lightbox' title='$data->filename'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$data->filename'/></a>";
					
				}
			$sql="select * from {$CFG->prefix}fe_users where id=$data->school_id  and status='active'";
			$get_id	= get_record_sql($sql);
			
			if($get_id != '')
			{
			 
				if($get_id->user_type !='' && $get_id->user_type =='student')
				{
					$name = get_field('student','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='counselor')
				{
					$name = get_field('counselors','first_name','user_id',$get_id->id);
					
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='parent')
				{
					$name = get_field('parent','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='teacher')
				{
					$name = get_field('teacher','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='school')
				{
					$name = get_field('schools','school_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				
				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id,'$page_name','$page_var','$page'); return false;\"");			

				$table->data[] = array (
					"$name",
					"$display",
					"$approvedbutton  $deletebutton"					   
				);
			}
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
		get_datepicker();
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
				<br><br>
				<?php notify($message);?>
				<center>
					<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name; ?>">
						<table width="90%">
							<tbody>
								<tr>
									<td class="Label" align="center" width="90%">
										<table>
											<!---<tr>
												<td>Name&nbsp;:&nbsp; </td>
												<td>
													<input name="txtsearch_name" id="txtsearch_name" class="txtBox" value="<?php echo $txtsearch_name;?>" type="text">
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<center>
														<input name="btnSearch" id="btnSearch" type="submit" value="Search">
														<input name="btnreset" id="btnreset" type="button" value="Reset" onclick="txtsearch_name.value=''">
														<input name="btnclear" id="btnclear" type="button" value="Clear Search"  onclick="window.location ='<?php echo $page_name; ?>'">
													</center>
												</td>
											</tr>--->
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
                                                                                                $page_name='college_gallery_manage.php';
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