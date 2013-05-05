<?php 
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag= false;

	$sort				= optional_param('sort', 'id', PARAM_TEXT);
    $dir				= optional_param('dir', 'DESC', PARAM_ALPHA);

	$perpage		= optional_param('perpage', 50, PARAM_INT);	       // how many per page;
    $page			= optional_param('page', 0, PARAM_INT);	
	$record_per_page= optional_param('record_perpage', '', PARAM_INT);		
	if(isset($record_per_page) && $record_per_page != ""){
			$perpage='';
			$perpage .= $record_per_page;	
	}	
	
	$search				= optional_param('search','', PARAM_RAW);
	$actiondata			= optional_param('action', '', PARAM_RAW);
	$datefrom			= optional_param('datefrom', '', PARAM_RAW);
	$dateto				= optional_param('dateto', '', PARAM_RAW);
	$sectiondata		= optional_param('section', '', PARAM_RAW);
	$user_email			= optional_param('user_email', '', PARAM_RAW);
	
	$message	= "";

	// Carry on with the user listing
    $columns = array("id","time","userid","section","action","info","url");
	
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
			$$column = "<a href=\"activities.php?sort=$column&amp;page=$page&amp;perpage=$perpage&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }
	$sort_term    = $sort." ".$dir;				
	$searchfields = array("userid");
	$extraselect = "id != ''";
	if($user_email != ''){
		$extraselect .= " AND userid = '".$user_email."'";
	}
	if($actiondata != ''){
		$extraselect .= " AND action = '".$actiondata."'";
	}
	if($sectiondata != ''){
		$extraselect .= " AND section = '".$sectiondata."'";
	}
	if($datefrom != '' && $dateto != ''){
		$datefrom = make_timestamp(date('Y',$datefrom),date('m',$datefrom),date('d',$datefrom));
		$dateto = make_timestamp(date('Y',$dateto),date('m',$dateto),date('d',$dateto),23,59,59);	
		$extraselect .= " AND time between '".$datefrom."' AND '".$dateto."'";
	}
	if($datefrom != '' && $dateto == ''){
		$datefrom = make_timestamp(date('Y',$datefrom),date('m',$datefrom),date('d',$datefrom));	
		$extraselect .= " AND time >=  '".$datefrom."'";
	}
	if($datefrom == '' && $dateto != ''){
		$dateto = make_timestamp(date('Y',$dateto),date('m',$dateto),date('d',$dateto),23,59,59);	
		$extraselect .= " AND time <=  '".$dateto."'";
	}
	
	$user_data = get_cmi_records('activities', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('activities', false, '', $search, $searchfields, $extraselect);
	
	//$search = $extraselect;
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {
		
		  $table->head = array ($id,$time,$userid,$section,$action,$info);
		  $table->align = array ("left", "left", "left", "left", "left", "center");
		  $table->width = "100%";
			foreach ($user_data as $data) {

				if(isset($data->userid)){
					$user_data1 = get_record("users", "id", $data->userid);
					if(isset($user_data1->email)){
						$username = $user_data1->email;
					}
				}
				if(isset($data->time)){
					$time = date("d-m-y",$data->time);
				}
								
				$table->data[] = array ("$data->id",
									"$time",
									"$username<br />$data->ip",
									"$data->section",
									"$data->action",
									"$data->info"
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
			window.addEvent('domready', function(){
				new FormCheck('frm_search');

				new DatePicker('.demo_vista', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					//toggleElements: '.date_toggler'
				});

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
			function reset_all()
			{
				$('frm_search').reset();
					
			}			
			function confirm_delete(val)
			{
				call_alert_confirm("Are you sure you want to delete this record?",'user_manage.php?delete='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected user?",'user_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","user_manage.php?"+val+"="+tot);
				else{
					Sexy.error("Select atleast one record to mark "+val+"!"); 
					return false;
				}
			}
			
			
		</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>

        <table width="100%">
            <tbody>
				<tr>
					<td class="DotedHeader" width="70%"><?php echo get_string('manage_reports','cmi');?></td>
				</tr>
            </tbody>
		</table>
        <br>
		<?php notify($message);?>
		<form method="post" name="frm_search" id="frm_search" action="activities.php<?php echo "?sort=$sort&amp;dir=$dir&amp;"?>">
		<table width="100%">
             <tbody><tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
				 <table width="100%">
					<tr>
						<td>User: </td>
						<td style="display:none">Action: </td>
						<td>Date From: </td>
						<td>Date To: </td>
						<td>Section: </td>
					</tr>
					<tr>
						<td>
							<select name="user_email" id="search" class="txtBox">
								<?php echo get_all_users('userid',$user_email);?>
							</select>
						</td>
						<td>
							<input name="datefrom" id="datefrom" type="text" class="validate[''] date txtBox demo_vista" value="" style="display: inline;" />	
						</td>
						<td>
							<input name="dateto" id="dateto" type="text" class="validate[''] date txtBox demo_vista" value="" style="display: inline;"/>
						</td>
						<td>
							<select name="section" id="section" class="txtBox">
								<?php echo get_all_info('section');?>
							</select>
						</td>
					</tr>
									
					<tr>
						<td colspan="4" align="center">
							<input name="btnSearch" id="btnSearch" type="submit" value="Submit"> 
							<input name="btnSearch" id="btnSearch" type="submit" value="Clear"> 
						</td>
					</tr>
				</table>
				<div>&nbsp;</div
			 ></td>
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
					if (!empty($table)) {
						$section = $action = '';
						print_table($table);
						if($sectiondata != ''){
							$section = "section=$sectiondata&amp;";
						}
						if($actiondata != ''){
							$action = "action=$actiondata&amp;";
						}
						if($datefrom != ''){
							$datefrom = "datefrom=$datefrom&amp;";
						}
						if($dateto != ''){
							$dateto = "dateto=$dateto&amp;";
						}
						print_paging_bar($user_count, $page, $perpage,
										 "activities.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$section.$action.$datefrom.$dateto."$perpage=$perpage&amp;");
					}
					else
					{	
						//print_heading('No Records Found');
						?>
						<table width= "600">
							<tr>
								<td align="center"><?php echo get_string('no_records_found','cmi');?></td>
							</tr>
						</table>
					<?php
					}
				?>
				</div>
             </td>
             <td width="5%"></td>
             </tr>
			<tr>
				<td width="5%" class="Label" align="center"></td>
				<td class="Label" align="center" width="90%">No.of Records per Page:
				<select id="record_perpage" name="record_perpage" class="txtBox" onchange="$('frm_search').submit();"><?php $arr = array('50','100','250','500','1000');echo generate_per_page_activities($perpage,5,$arr); ?></select></td>
				<td width="5%"></td>
			</tr>				 
             <tr>
             <td width="5%"></td>
             <td width="90%"></td>
             <td width="5%"></td>
             </tr>
             </tbody></table>
			 </form>
			</td>
	</tr>
</table>
<?php
	print_container_end();

	print_footer();
?>