<?php 
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();
	$edit_flag		= false;
	$sort			= optional_param('sort', 'name', PARAM_TEXT);
    $dir			= optional_param('dir', 'ASC', PARAM_ALPHA);
	$perpage		= optional_param('perpage', 10, PARAM_INT);	       // how many per page;
    $page			= optional_param('page', 0, PARAM_INT);	
	$search			= optional_param('search', '', PARAM_RAW);
	$statename_form = optional_param('statename_form', '', PARAM_RAW);
	$state			= optional_param('state', '', PARAM_RAW);
	$state1			= optional_param('parent_id', '', PARAM_RAW);
	$message		= "";
	$country_id		= optional_param('country_id', '', PARAM_RAW);	
	$record_per_page= optional_param('record_perpage', '', PARAM_INT);
	
	$base_url		= "city_manage.php?search=$search&amp;";
	if($country_id != "" && $country_id !=0)
	{
		$base_url .= "country_id=$country_id&amp;";
	}
	$state_url = '';
	if($state != "")
	{
		$state_url .= "parent_id=$state&amp;";
	}
	else{
		$state = $state1;
		$state_url .= "parent_id=$state&amp;";
	}
	$base_url .= $state_url;
	
	if(isset($_GET) && isset($_GET['delete']))
	{
		$location_id			= required_param('delete', PARAM_INT);
		$delete_record			= new object();
		$delete_record->id		= $location_id;
		$delete_record->status	= 'delete';

		if(update_record('city', $delete_record)){
			$message = "City Deleted Successfully !!";		
		}
	}
	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record('city', $delete_record)){
				cmi_add_to_log('city','Delete','Deleted city id='.$value);
				$message = "City Deleted Successfully !!";		
			}
		}
	}
	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$location_id		= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);

		$record				= new object();
		$record->id			= $location_id;
		$record->status		= $status;

		if(update_record('city', $record)){
			cmi_add_to_log('City',$status,$status.'d city id: '.$city_id);
			$message = "City Status Updated Successfully !!";
		}
	}
	if(isset($_GET) && isset($_GET['inactive']) || isset($_GET['active']))
	{
		$status = '';
		$ids = optional_param('inactive','',PARAM_RAW);
		$idexploded = explode(',',$ids);
		$status = 'inactive';
		if($ids == ''){
			$ids = optional_param('active','',PARAM_RAW);
			$idexploded = explode(',',$ids);
			$status = 'active';
		}
		foreach($idexploded as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = $status;
		
			if(update_record('city', $delete_record)){
				//$status = str_replace('e','a',$status);
				cmi_add_to_log('city',$status,$status.'city id: '.$value);
				$message = "City Status Updated Successfully !!";		
			}
		}
	}
	
	
	// Carry on with the user listing

	$columns = array("check","id", "name");

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
			$columnicon = " <img src=\"theme/$theme/images/$columnicon.gif\" alt=\"\" />";

		}
		if($column != 'check'){
			$$column = "<a href=\"".$base_url."sort=$column&amp;dir=$columndir&amp;search=$search&amp;\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
	}
	
	$sort_term    = $sort." ".$dir;				
	$searchfields = array("id", "name");
	$extraselect = " status != 'delete' ";
	
	if(isset($record_per_page) && $record_per_page != ""){
			$perpage='';
			$perpage .= $record_per_page;	
	}

	if(isset($country_id) && $country_id != "" && $country_id!='0'){
			$extraselect .= " AND country_id = $country_id ";	
	}

	if(isset($state) && $state != '' && $state != '0'){
		$extraselect .= " AND state_id = $state";
	}
	
	
	$city_data = get_cmi_records('city', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$city_count = get_cmi_records('city', false, '', $search, $searchfields, $extraselect);
	
	if (!$city_data) {
			$match = array();
			$table = NULL;
	} 
	else{
			
		$table->head = array ($check,$id, $name, "State","Country","");
		$table->align = array ("center","center", "left", "left", "left", "left", "center", "center", "center", "center");
		$table->width = "95%";
		foreach ($city_data as $data) {

			$status = 'active';
			$image = 'delete1.png';
			if($data->status == 'active'){
				$status = 'inactive';
				$image = 'add1.png';
			}
			$state_data		= get_record('state', 'id', $data->state_id);
			$country_data	= get_record('country', 'id', $state_data->country_id);

			$editbutton = get_buttons('Edit city',"city_form.php?edit=$data->id","theme/$theme/images/edit.gif", " rel='lightbox[60% 60%]' ");

			$disablebutton = get_buttons('Active/inactive',"city_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");

			$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");
				
			$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" 	value=\"".$data->id."\" >",
									"$data->id",
									"$data->name",
									"$state_data->name",
									"$country_data->name",
									$editbutton.''.$disablebutton.''.$deletebutton
									);
		}
	}
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
		
?>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				new FormCheck('frm_search');
				
				//call Motools tipz initialization
				show_tipz();
				if($('check')){ 
				 $('check').addEvent('click', function() {
					if($('check').get('rel') == 'yes' || $('check').get('rel') == 'null')
					{
						do_check = false;
						$('check').set('checked','').set('rel','no');
					}
					else{
						do_check = true;
						$('check').set('checked','true').set('rel','yes');
					}
					$$('.check-me').set('checked',do_check) 
				 });
				}
				
			});
		function confirm_delete(val)
		{
			call_alert_confirm("Are you sure you want to delete this record?",'city_manage.php?delete='+val);
					
		}
		function confirm_deleteall(val)
		{
			var total = '';
			$$('.check-me').each(function(el){
				 if(el.checked == true){
					total += el.value+',';
				 }
				 
			});
			tot = total.substring(0,total.length-1);
			if(total != '')
				call_alert_confirm("Are you sure you want to delete selected City?",'city_manage.php?deleteall='+tot);
			else{
				Sexy.error('Select atleast one record to delete!'); 
				return false;
			}
		}
		function active_inactive(val)
		{
			var total = '';
			$$('.check-me').each(function(el){
				 if(el.checked == true){
					total += el.value+',';
				 }
				 
			});
			tot = total.substring(0,total.length-1);
			if(total != '')
				call_alert_confirm("Are you sure you want to mark selected records "+val+"?","city_manage.php?"+val+"="+tot);
			else{
				Sexy.error("Select atleast one record to mark "+val+"!"); 
				return false;
			}
		}
		
		</script>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td VALIGN="top" width="200">
					<?php load_menu(); ?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="DotedHeader" width="70%">Manage City:</div>
					<a href="city_form.php" style="float:right;margin-right:60px;" rel="lightbox[set1 60% 60%]" class="clearfix" ><img src="<?php echo "theme/$theme/images/add.png" ?>" /></a><br>
					<br>
					<?php notify($message);?>		
					<script type="text/javascript">
						window.addEvent('domready', function(){
							new FormCheck('frm_search');
						});
					</script>
					<form method="post" name="frm_search" id="frm_search" action="city_manage.php">
					<table width="100%">
						<tbody>
							<!--<tr>
								<td width="5%"></td>
								<td class="Label" align="center" width="90%"><?php echo 'Zone name:'; ?>
								<select id="zone_id" name="zone_id" class="txtBox" onchange="$('state').selectedIndex=0;$('frm_search').submit()">
								<?php //echo get_zone_option_new($zone_id); ?></select>
								</td>
								<td width="5%"></td>
							</tr>
							<tr>
								<td width="5%"></td>
								<td class="Label" align="center" width="90%">
								<?php echo 'State name:'; ?>
								<select id="state" name="state" class="txtBox" onchange="$('frm_search').submit()">
								<?php //echo get_state_option_new($state,$zone_id); ?>
								</select>
									</td>
								<td width="5%"></td>
							</tr>-->
							<tr>
								 <td width="5%"></td>
								 <td class="Label" align="center" width="90%">
										City Name: &nbsp;<input name="search" id="search" class="txtBox" value="<?php echo $search;?>" style="width: 150px;" type="text">&nbsp;<br><br>
										<input name="btnSearch" id="btnSearch" type="submit" value="Search">&nbsp;
										<input name="btnReset" id="btnReset" type="reset" value="Clear Search" onclick="window.location ='city_manage.php'">
								</td>
								<td width="5%"></td>
							</tr>
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
											if(empty($city_data))
											{
												print_heading('No Records Found');
											}
											else if (!empty($table)) {
												print_table($table);

													echo '<div align="left">
															<a onclick="confirm_deleteall(); return false;" href="city_manage.php?deleteall=1">Delete All</a>
																<a id="active" onclick="active_inactive(this.id); return false;" href="city_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
																<a id="inactive" onclick="active_inactive(this.id); return false;" href="city_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
															</div>';

												print_paging_bar($city_count, $page, $perpage,
																 $base_url."sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");
											}
										?>	
									</div>
								</td>
								<td width="5%"></td>
							</tr>
							<tr>
								<td width="5%" class="Label" align="center"></td>
								<td class="Label" align="left" width="90%"></td>
								<td width="5%"></td>
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