<?php
	if(!file_exists("config.php"))
		header("Location:upgrade.php");

	require_once("config.php");
	
	isLogin();
	print_header(get_string('cmi','cmi'));	
	cmi_include_head("css");
	cmi_include_head('js');
	print_container_start();
	
	define('ONE_DAY', 60*60*24);
	function start_date($arg)
	{   
		$DayNumber = date($arg);
		$WeekBegin = date("Y-m-d",time() - ($DayNumber - 1)*ONE_DAY);
		return $WeekBegin;
	}

	$week_date=start_date('w');
	$week=strtotime($week_date);
	$month_date=start_date('m');
    $month=strtotime($month_date);

	$id = optional_param('id',PARAM_RAW);

	$today = time();
	$week = time() - (7 * 24 * 60 * 60);
	$month = time() - (30 * 24 * 60 * 60);
	$year = time() - (365 * 24 * 60 * 60);

	//$nextWeek = time() + (-7 * 24 * 60 * 60);
		
	/*  Query For Schools */
	
	$school_d=get_record_sql("SELECT COUNT(*) as sc_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'school' ");
	$school_w=get_record_sql("SELECT COUNT(*) as sc_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND <=$today) AND user_type = 'school'");
	$school_m=get_record_sql("SELECT COUNT(*) as sc_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND <=$today) AND user_type = 'school'");
	
	/*  Query For Schools */

	$student_data = get_record_sql('Select * from '.$CFG->prefix.'schools where user_id = '.$id.' and status = "active"');

	$school_enquiry = get_records_sql('Select * from '.$CFG->prefix.'schools_enquiry where school_id = '.$id.' and status = "active" order by added_date DESC');

	$school_enquiry_week = get_records_sql('Select * from '.$CFG->prefix.'schools_enquiry where school_id = '.$id.' and status = "active" and (added_date >= '.$week.' and added_date <= '.$today.') order by added_date DESC');

	$school_enquiry_month = get_records_sql('Select * from '.$CFG->prefix.'schools_enquiry where school_id = '.$id.' and status = "active" and (added_date >= '.$month.' and added_date <= '.$today.') order by added_date DESC');

	$school_enquiry_year = get_records_sql('Select * from '.$CFG->prefix.'schools_enquiry where school_id = '.$id.' and status = "active" and (added_date >= '.$year.' and added_date <= '.$today.') order by added_date DESC');

		
?>	
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		    <tr>
                <td VALIGN="top" >
                    <?php load_menu(); ?>
                </td>
			</tr>
			<tr>
                <td VALIGN="top" >
                    <h2>Leads  Detail:</h2></center>
                </td>
		    </tr>
			<tr>
				<td>
					<div> <h3><span style="vertical-align:super">This Week</span></h3></div>
					 <?php  if(!empty($school_enquiry_week)) { ?>
						<table border="1" cellspacing="0" cellpadding="5" width="100%" id="view_table" >
							<tr>
								<th bgcolor="#e6e6e6" >Name</th>
								<th bgcolor="#e6e6e6" >Email</th>
								<th bgcolor="#e6e6e6" >Enquiry</th>
								<th bgcolor="#e6e6e6" >Phone No.</th>
								<th bgcolor="#e6e6e6" >Interested in.</th>
								<th bgcolor="#e6e6e6" >Best time to contact .</th>
								<th bgcolor="#e6e6e6" >Date</th>
							</tr>
							<?php 
							foreach($school_enquiry_week as $data){
							?>
								<tr>
									<td valign="top"><?php if(isset($data->fullname)) echo $data->fullname; ?></td>
									<td valign="top"><?php if(isset($data->email)) echo $data->email; ?></td>
									<td valign="top"><?php if(isset($data->enquiry)) echo $data->enquiry; ?></td>
									<td valign="top"><?php if(isset($data->phone)) echo $data->phone; ?></td>
									<td valign="top"><?php if(isset($data->interest)) echo $data->interest; ?></td>
									<td valign="top"><?php if(isset($data->time_contact)) echo $data->time_contact; ?></td>
									<td valign="top"><?php if(isset($data->added_date)) echo date("d/m/Y",$data->added_date); ?></td>
								</tr>	
							<?php 
							}
							?>
						</table>
						<?php }
						else{
							echo "<div align='center'> No Records Found...</div>";
						}
						?>

				</td>
            </tr>
			<tr>
				<td>
					<div> <h3><span style="vertical-align:super">This Month</span></h3></div>
					 <?php  if(!empty($school_enquiry_month)) { ?>
						<table border="1" cellspacing="0" cellpadding="5" width="100%" id="view_table" >
							<tr>
								<th bgcolor="#e6e6e6" >Name</th>
								<th bgcolor="#e6e6e6" >Email</th>
								<th bgcolor="#e6e6e6" >Enquiry</th>
								<th bgcolor="#e6e6e6" >Phone No.</th>
								<th bgcolor="#e6e6e6" >Interested in.</th>
								<th bgcolor="#e6e6e6" >Best time to contact .</th>
								<th bgcolor="#e6e6e6" >Date</th>
							</tr>
							<?php 
							foreach($school_enquiry_month as $data){
							?>
								<tr>
									<td valign="top"><?php if(isset($data->fullname)) echo $data->fullname; ?></td>
									<td valign="top"><?php if(isset($data->email)) echo $data->email; ?></td>
									<td valign="top"><?php if(isset($data->enquiry)) echo $data->enquiry; ?></td>
									<td valign="top"><?php if(isset($data->phone)) echo $data->phone; ?></td>
									<td valign="top"><?php if(isset($data->interest)) echo $data->interest; ?></td>
									<td valign="top"><?php if(isset($data->time_contact)) echo $data->time_contact; ?></td>
									<td valign="top"><?php if(isset($data->added_date)) echo date("d/m/Y",$data->added_date); ?></td>
									
							<?php }	?>
						</table>
						<?php }
						else{
								echo "<div align='center'> No Records Found...</div>";
						}
						?>

				</td>
            </tr>
			<tr>
				<td>
					<div> <h3><span style="vertical-align:super">This Year</span></h3></div>
					 <?php  if(!empty($school_enquiry_year)) { ?>
						<table border="1" cellspacing="0" cellpadding="5" width="100%" id="view_table" >
							<tr>
								<th bgcolor="#e6e6e6" >Name</th>
								<th bgcolor="#e6e6e6" >Email</th>
								<th bgcolor="#e6e6e6" >Enquiry</th>
								<th bgcolor="#e6e6e6" >Phone No.</th>
								<th bgcolor="#e6e6e6" >Interested in.</th>
								<th bgcolor="#e6e6e6" >Best time to contact .</th>
								<th bgcolor="#e6e6e6" >Date</th>
							</tr>
							<?php 
								foreach($school_enquiry_year as $data)
								{
								?>
									<tr>
										<td valign="top"><?php if(isset($data->fullname)) echo $data->fullname; ?></td>
										<td valign="top"><?php if(isset($data->email)) echo $data->email; ?></td>
										<td valign="top"><?php if(isset($data->enquiry)) echo $data->enquiry; ?></td>
										<td valign="top"><?php if(isset($data->phone)) echo $data->phone; ?></td>
										<td valign="top"><?php if(isset($data->interest)) echo $data->interest; ?></td>
									<td valign="top"><?php if(isset($data->time_contact)) echo $data->time_contact; ?></td>
										<td valign="top"><?php if(isset($data->added_date)) echo date("d/m/Y",$data->added_date); ?></td>
										
								<?php 
								}
								?>
						</table>
						<?php }
						else{
								echo "<div align='center'>No Records Found..</div>";
						}
						?>

				</td>
            </tr>
		</table>
<?php
	print_container_end();

	print_footer();
?>