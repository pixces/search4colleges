<?php 
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();
	$sort         = optional_param('sort', 'id', PARAM_TEXT);
	$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $search		  = optional_param('search', '', PARAM_RAW);
	$message		= "";

	// Carry on with the user listing
    $columns = array("id","page_name");
	
    foreach ($columns as $column) {
		$string[$column] = get_string("$column","cmi");
		if ($sort != $column) {
			$columnicon = "";
			if ($column == "id") {
				$columndir = "DESC";
			} else {
				$columndir = "ASC";
			}
		} else {
			$columndir = $dir == "ASC" ? "DESC":"ASC";
			if ($column == "id") {
				$columnicon = $dir == "ASC" ? "up":"down";
			} else {
				$columnicon = $dir == "ASC" ? "down":"up";
			}
			$columnicon = "<img src=\"theme/$theme/images/$columnicon.gif\" alt=\"\" />";

		}
		if($column != 'check'){
			$$column = "<a href=\"page_content_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$extraselect = " status != 'delete'";								
	$page_content_settings_data = get_cmi_records('page_contents', true, '*', '', '', $extraselect, '', '', '');
	$page_content_settings_count = get_cmi_records('page_contents', false, '', '', '', $extraselect);

	if (!$page_content_settings_data) {
			$match = array();
			$table = NULL;
	} else {				
			$table->head = array ('ID', 'Page Name', "");
			$table->align = array ("center", "left", "center");
			$table->width = "95%";
			foreach ($page_content_settings_data as $data) {

				$status = 'active';
				$image = 'delete1.png';
				if($data->status == 'active'){
					$status = 'inactive';
					$image = 'add1.png';
				}

	$editbutton = get_buttons('Edit Page Content',"page_content_form.php?edit=$data->id","theme/$theme/images/edit.gif","rel='lightbox[$data->page_name 800 520]'");
			
	$table->data[] = array ("$data->id",
							"$data->page_name",
	 						 $editbutton);
			}
	}	
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');

	print_container_start();
?>

	<script type="text/javascript">
		window.addEvent('domready', function(){
			show_tipz();
		});
	</script>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td VALIGN="top" width="270">
				<?php load_menu(); ?>
			</td>
	</tr>
	<tr>
			<td>

				<div class="DotedHeader" width="70%">Manage Page Content :</div><br>
				<?php notify($message); ?>		
				<table width="100%">
					 <tbody>
						<tr>
							<td width="5%"></td>
							<td align="center" width="90%">
							</td>
							<td width="5%"></td>
						</tr>
						<tr>
							 <td width="5%"></td>
							 <td align="center" width="90%">
								<div>
									<?php					
										if(empty($page_content_settings_data))
										{
											print_heading('No Records Found');
										}
										else if (!empty($table)) {
											print_table($table);
										}
									?>	
								</div>
							</td>
							<td width="5%"></td>
						</tr>
						<tr>
							<td width="5%"></td>
							<td width="90%"></td>
							<td width="5%"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>

			
<?php 
	print_container_end();
	print_footer();
?>