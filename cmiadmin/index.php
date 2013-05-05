<?php
	if(!file_exists("config.php"))
		header("Location:upgrade.php");

	require_once("config.php");
	
	isLogin();
	print_header(get_string('cmi','cmi'));	
	cmi_include_head("css");
	cmi_include_head('js');
	print_container_start();
	
	/*define('ONE_DAY', 60*60*24);
	function start_date($arg)
	{   
		$DayNumber = date($arg);
		$WeekBegin = date("Y-m-d",time() - ($DayNumber - 1)*ONE_DAY);
		return $WeekBegin;
	}
	function month_date($arg)
	{   
		$mon = date('m');
		$Year = date('Y');
		$month="1-".$mon."-".$Year;
		//$WeekBegin = date("Y-m-d",time() - ($DayNumber - 1)*ONE_DAY);
		return $month;
	}

	$week_date=start_date('w');
	$week=strtotime($week_date);
	$month_date=month_date('m');
    $month=strtotime($month_date);
	date("Y-m-d",$month);*/
	
	date("Y-m-d",$week);
	$today = time();
	$week = time() - (7 * 24 * 60 * 60);
	$month = time() - (30 * 24 * 60 * 60);
	$year = time() - (365 * 24 * 60 * 60);
	$totime=time();

	//$nextWeek = time() + (-7 * 24 * 60 * 60);
	############################## Query For Student #############################
	$student_d=get_record_sql("SELECT COUNT(*) as s_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'student' ");
	$student_w=get_record_sql("SELECT COUNT(*) as s_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND added_date<=$totime) AND user_type = 'student'");
	$student_m=get_record_sql("SELECT COUNT(*) as s_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND added_date<=$totime) AND user_type = 'student'");
	############################## Query End For Student #########################
	
	
	############################## Query For Parents  #############################
	$parents_d=get_record_sql("SELECT COUNT(*) as p_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'parent' ");
	$parents_w=get_record_sql("SELECT COUNT(*) as p_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND added_date<=$totime) AND user_type = 'parent'");
	$parents_m=get_record_sql("SELECT COUNT(*) as p_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND added_date<=$totime) AND user_type = 'parent'");
	############################## Query End For Parents  #########################
	
	############################## Query For Schools #############################
	$school_d=get_record_sql("SELECT COUNT(*) as sc_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'school' ");
	$school_w=get_record_sql("SELECT COUNT(*) as sc_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND added_date<=$totime) AND user_type = 'school'");
	$school_m=get_record_sql("SELECT COUNT(*) as sc_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND added_date<=$totime) AND user_type = 'school'");
	############################## Query End For Schools #########################
	
	
	############################## Query For Teachers #############################
	$teacher_d=get_record_sql("SELECT COUNT(*) as t_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'teacher' ");
	$teacher_w=get_record_sql("SELECT COUNT(*) as t_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND added_date<=$totime) AND user_type = 'teacher'");
	$teacher_m=get_record_sql("SELECT COUNT(*) as t_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND added_date<=$totime) AND user_type = 'teacher'");
	############################## Query End For Teachers #########################
	
	############################## Query For counselors  #############################
	$counselor_d=get_record_sql("SELECT COUNT(*) as c_day FROM {$CFG->prefix}fe_users WHERE added_date>='".strtotime(date("Y-m-d"))."' AND user_type = 'counselor' ");
	$counselor_w=get_record_sql("SELECT COUNT(*) as c_weak FROM {$CFG->prefix}fe_users WHERE (added_date>=$week AND added_date<=$totime) AND user_type = 'counselor'");
	$counselor_m=get_record_sql("SELECT COUNT(*) as c_month FROM {$CFG->prefix}fe_users WHERE (added_date>=$month AND added_date<=$totime) AND user_type = 'counselor'");
	############################## Query End For counselors  #########################
	
	############################## Query For leads  #############################
	$leads_d=get_record_sql("SELECT COUNT(*) as l_day FROM {$CFG->prefix}schools_enquiry WHERE added_date>='".strtotime(date("Y-m-d"))."' ");
	$leads_w=get_record_sql("SELECT COUNT(*) as l_weak FROM {$CFG->prefix}schools_enquiry WHERE (added_date>=$week AND added_date<=$totime)");
	$leads_m=get_record_sql("SELECT COUNT(*) as l_month FROM {$CFG->prefix}schools_enquiry WHERE (added_date>=$month AND added_date<=$totime)");
	############################## Query End For leads  #########################
	
	############################## Query For image/videos  #############################
	$iv_d_a=get_record_sql("SELECT COUNT(*) as iv_day_a FROM {$CFG->prefix}school_image_video WHERE date>='".strtotime(date("Y-m-d"))."' AND publish=1");
	$iv_d_d=get_record_sql("SELECT COUNT(*) as iv_day_d FROM {$CFG->prefix}school_image_video WHERE date>='".strtotime(date("Y-m-d"))."' AND publish=0");
	$iv_d_u=get_record_sql("SELECT COUNT(*) as iv_day_u FROM {$CFG->prefix}school_image_video WHERE date>='".strtotime(date("Y-m-d"))."'");
	$iv_w_a=get_record_sql("SELECT COUNT(*) as iv_weak_a FROM {$CFG->prefix}school_image_video WHERE (date>=$week AND date<=$totime) AND publish=1");
	$iv_w_d=get_record_sql("SELECT COUNT(*) as iv_weak_d FROM {$CFG->prefix}school_image_video WHERE (date>=$week AND date<=$totime) AND publish=0");
	$iv_w_u=get_record_sql("SELECT COUNT(*) as iv_weak_u FROM {$CFG->prefix}school_image_video WHERE (date>=$week AND date<=$totime)");
	$iv_m_a=get_record_sql("SELECT COUNT(*) as iv_month_a FROM {$CFG->prefix}school_image_video WHERE (date>=$month AND date<=$totime)  AND publish=1");
	$iv_m_d=get_record_sql("SELECT COUNT(*) as iv_month_d FROM {$CFG->prefix}school_image_video WHERE (date>=$month AND date<=$totime)  AND publish=0");
	$iv_m_u=get_record_sql("SELECT COUNT(*) as iv_month_u FROM {$CFG->prefix}school_image_video WHERE (date>=$month AND date<=$totime)");
	/*$iv_d_a=get_record_sql("SELECT COUNT(*) as iv_day_a FROM {$CFG->prefix}gallery WHERE added_date>='".strtotime(date("Y-m-d"))."' AND approved='approved'  ");
	$iv_d_d=get_record_sql("SELECT COUNT(*) as iv_day_d FROM {$CFG->prefix}gallery WHERE added_date>='".strtotime(date("Y-m-d"))."' AND approved='declined'");
	$iv_d_i=get_record_sql("SELECT COUNT(*) as iv_day_i FROM {$CFG->prefix}gallery WHERE added_date>='".strtotime(date("Y-m-d"))."' AND approved='inprocess'");
	
	$iv_w_a=get_record_sql("SELECT COUNT(*) as iv_weak_a FROM {$CFG->prefix}gallery WHERE (added_date>=$week AND added_date<=$totime) AND approved='approved'");
	$iv_w_d=get_record_sql("SELECT COUNT(*) as iv_weak_d FROM {$CFG->prefix}gallery WHERE (added_date>=$week AND added_date<=$totime) AND approved='declined'");
	$iv_w_i=get_record_sql("SELECT COUNT(*) as iv_weak_i FROM {$CFG->prefix}gallery WHERE (added_date>=$week AND added_date<=$totime) AND approved='inprocess'");
	
	$iv_m_a=get_record_sql("SELECT COUNT(*) as iv_month_a FROM {$CFG->prefix}gallery WHERE (added_date>=$month AND added_date<=$totime) AND approved='approved'");
	$iv_m_d=get_record_sql("SELECT COUNT(*) as iv_month_d FROM {$CFG->prefix}gallery WHERE (added_date>=$month AND added_date<=$totime)  AND approved='declined'");
	$iv_m_i=get_record_sql("SELECT COUNT(*) as iv_month_i FROM {$CFG->prefix}gallery WHERE (added_date>=$month AND added_date<=$totime) AND approved='inprocess'");*/
	
	
	############################## Query End For image/videos  #########################
	
	############################## Query For  Articles  #############################
	$articles_d=get_record_sql("SELECT COUNT(*) as a_day FROM {$CFG->prefix}articles WHERE added_date>='".strtotime(date("Y-m-d"))."' ");
	$articles_w=get_record_sql("SELECT COUNT(*) as a_weak FROM {$CFG->prefix}articles WHERE (added_date>=$week AND added_date<=$totime) ");
	$articles_m=get_record_sql("SELECT COUNT(*) as a_month FROM {$CFG->prefix}articles WHERE (added_date>=$month AND added_date<=$totime)");
	############################## Query End For  Articles  #########################
	
	############################## Query For  Articles  #############################
	
	/*$membership_d=get_records_sql("SELECT s.school_name as school, mt.title as membership FROM {$CFG->prefix}school_membership as m, {$CFG->prefix}school_member_ship_types as mt,{$CFG->prefix}schools as s WHERE s.user_id= m.school_id AND mt.id=m.school_memberShip_typeid AND FROM_UNIXTIME(m.added_date, '%Y-%d-%m') ='".date("Y-d-m")."' AND m.status='active'");
	
	$membership_w=get_records_sql("SELECT s.school_name as school, mt.title as membership FROM {$CFG->prefix}school_membership as m, {$CFG->prefix}school_member_ship_types as mt,{$CFG->prefix}schools as s WHERE s.user_id= m.school_id AND mt.id=m.school_memberShip_typeid AND m.added_date>=$week  AND m.status='active'");
	
	$membership_m=get_records_sql("SELECT s.school_name as school, mt.title as membership FROM {$CFG->prefix}school_membership as m, {$CFG->prefix}school_member_ship_types as mt,{$CFG->prefix}schools as s WHERE s.user_id= m.school_id AND mt.id=m.school_memberShip_typeid AND m.added_date>=$month  AND m.status='active'");*/

	$membership_d=get_record_sql("SELECT  COUNT(*) as day, SUM(total_amount) as day_sum FROM {$CFG->prefix}school_membership as m WHERE FROM_UNIXTIME(m.added_date, '%Y-%d-%m') ='".date("Y-d-m")."' AND m.status='active'");	
	$membership_w=get_record_sql("SELECT  COUNT(*) as week, SUM(total_amount) as week_sum FROM {$CFG->prefix}school_membership as m WHERE (m.added_date>=$week AND added_date<=$totime)  AND m.status='active'");
	$membership_m=get_record_sql("SELECT  COUNT(*) as month, SUM(total_amount) as month_sum FROM {$CFG->prefix}school_membership as m WHERE (m.added_date>=$month AND added_date<=$totime) AND m.status='active'");
	
	############################## Query End For  Articles  #########################
?>	
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		    <tr>
                <td VALIGN="top" >
                    <?php load_menu(); ?>
                </td>
			</tr>
			<tr>
                <td VALIGN="top" >
                    <center><h2>Welcome <?php echo $_SESSION['username']."!"; ?></h2></center>
                </td>
		    </tr>
			<tr>
            <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td>
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header" >
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Students</strong></td>
                                                      </tr>
                                                             
                                                              <tr>
                                                                <td align="left">&nbsp;Total Student Registered today</td>
                                                                <td align="left">&nbsp;<?php echo $student_d->s_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Student Registered This week</td>
                                                                <td align="left">&nbsp;<?php echo $student_w->s_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Student Registered This month</td>
                                                                <td align="left">&nbsp;<?php echo $student_m->s_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header" >
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Parents </strong></td>
                                                      </tr>
                                                              
                                                              <tr>
                                                                <td align="left">&nbsp;Total Parents Registered today</td>
                                                                <td align="left">&nbsp;<?php echo $parents_d->p_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Parents Registered This week</td>
                                                                <td align="left">&nbsp;<?php echo $parents_w->p_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Parents Registered This month</td>
                                                                <td align="left">&nbsp;<?php echo $parents_m->p_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                        
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header" >
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Schools</strong></td>
                                                      </tr>
                                                              
                                                              <tr>
                                                                <td align="left">&nbsp;Total Schools Registered today</td>
                                                                <td align="left">&nbsp;<?php echo $school_d->sc_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Schools Registered This week</td>
                                                                <td align="left">&nbsp;<?php echo $school_w->sc_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Schools Registered This month</td>
                                                                <td align="left">&nbsp;<?php echo $school_m->sc_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                
                                
                               
                              </tr>
                    </table>

            </td>
            </tr>
            <tr>
            <td>&nbsp;</td>
            </tr>		
			<tr>
            <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td>
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header" >
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Teachers</strong></td>
                                                      </tr>
                                                              
                                                              <tr>
                                                                <td align="left">&nbsp;Total Teachers Registered today</td>
                                                                <td align="left">&nbsp;<?php echo $teacher_d->t_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Teachers Registered This week</td>
                                                                <td align="left">&nbsp;<?php echo $teacher_w->t_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Teachers Registered This month</td>
                                                                <td align="left">&nbsp;<?php echo $teacher_m->t_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Counselors  </strong></td>
                                                      </tr>
                                                              
                                                              <tr>
                                                                <td align="left">&nbsp;Total Counselors Registered today</td>
                                                                <td align="left">&nbsp;<?php echo $counselor_d->c_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Counselors Registered This week</td>
                                                                <td align="left">&nbsp;<?php echo $counselor_w->c_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total Counselors Registered This month</td>
                                                                <td align="left">&nbsp;<?php echo $counselor_m->c_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                        
                                    <td width="33%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Leads </strong></td>
                                                      </tr>
                                                              
                                                              <tr>
                                                                <td align="left">&nbsp;Total leads generated by students today</td>
                                                                <td align="left">&nbsp;<?php echo $leads_d->l_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total leads generated by students This week</td>
                                                                <td align="left">&nbsp;<?php echo $leads_w->l_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total leads generated by students This month</td>
                                                                <td align="left">&nbsp;<?php echo $leads_m->l_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                
                                
                               
                              </tr>
							  <tr>
								<td colspan="3">
								</td>
							  </tr>

                    </table>

            </td>
            </tr>	
			 <tr>
            <td>&nbsp;</td>
            </tr>		
			<tr>
            <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td>
                                    <td width="50%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header" >
                                                              <tr>
                                                                <td height="35" colspan="4" align="center" bgcolor="#e6e6e6">&nbsp;<strong>images/videos</strong></td>
                                                      </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;<strong></strong></td>
                                                                <td align="left">&nbsp;<strong>approved</strong></td>
                                                                <td align="left">&nbsp;<strong>disapproved </strong></td>
																<td align="left">&nbsp;<strong>uploaded </strong></td>
                                                                
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of images/videos uploaded today</td>
                                                                <td align="left">&nbsp;<?php echo $iv_d_a->iv_day_a; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_d_d->iv_day_d; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_d_u->iv_day_u; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of images/videos uploaded This week</td>
                                                                <td align="left">&nbsp;<?php echo $iv_w_a->iv_weak_a; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_w_d->iv_weak_d; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_w_u->iv_weak_u; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of images/videos uploaded This month</td>
                                                                <td align="left">&nbsp;<?php echo $iv_m_a->iv_month_a; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_m_d->iv_month_d; ?></td>
                                                                <td align="left">&nbsp;<?php echo $iv_m_u->iv_month_u; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                                    <td width="50%">
                                                    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
                                                              <tr>
                                                                <td height="35" colspan="2" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Articles</strong></td>
                                                      </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of Articles published under each category today</td>
                                                                <td align="left">&nbsp;<?php echo $articles_d->a_day; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of Articles published under each category This week</td>
                                                                <td align="left">&nbsp;<?php echo $articles_w->a_weak; ?></td>
                                                              </tr>
                                                              <tr>
                                                                <td align="left">&nbsp;Total no. of Articles published under each category This month</td>
                                                                <td align="left">&nbsp;<?php echo $articles_m->a_month; ?></td>
                                                              </tr>
                                                    </table>
                                    </td>
                        
                                    <td width="33%">&nbsp;
                                                    
                                    </td>
                
                                
                               
                              </tr>
                    </table>

            </td>
            </tr>
			<tr>
            <td>&nbsp;</td>
            </tr>
			<tr>
				<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="">
                                                              <tr>
                                                                <td width="33%">
																	<table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
																	<tr>
																	<td height="35" colspan="3" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Paid Registration Today</strong></td>
																  </tr>
																  <tr>
																	<td><strong>School</strong></td>
																	<td><strong>Membership</strong></td>
																	<td><strong>Amount</strong></td>
																  </tr>
																  <?php if($membership_d->day > 0){
																  ?>
																		<tr>
																			<td><?php echo $membership_d->day;?></td>
																			<td><?php echo $membership_d->day;?></td>
																			<td><?php echo $membership_d->day_sum;?></td>
																		</tr>
																  <?php																			
																		}
																		else{
																	?>
																			<tr><td style="text-align:center; padding-top:10px;" colspan="3">No record</td></tr>
																	<?php
																		}
																  ?>
														</table>
														</td>
														 <td width="33%">
																	<table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
																	<tr>
																	<td height="35" colspan="3" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Paid Registration in Week</strong></td>
																  </tr>
																  <tr>
																	<td><strong>School</strong></td>
																	<td><strong>Membership</strong></td>
																	<td><strong>Amount</strong></td>
																  </tr>
																  <?php if($membership_w->week > 0){																			
																  ?>
																		<tr>
																			<td><?php echo $membership_w->week;?></td>
																			<td><?php echo $membership_w->week;?></td>
																			<td><?php echo $membership_w->week_sum;?></td>
																		</tr>
																  <?php																			
																		}
																		else{
																	?>
																			<tr><td style="text-align:center; padding-top:10px;" colspan="3">No record</td></tr>
																	<?php
																		}
																  ?>
														</table>
														</td>
														 <td width="33%">
																	<table width="90%" border="1" cellspacing="0" cellpadding="0" class="main_header">
																	<tr>
																	<td height="35" colspan="3" align="center" bgcolor="#e6e6e6">&nbsp;<strong>Paid Registration in Month</strong></td>
																  </tr>
																  <tr>
																	<td><strong>School</strong></td>
																	<td><strong>Membership</strong></td>
																	<td><strong>Amount</strong></td>
																  </tr>
																  <?php if($membership_m->month > 0){
																  ?>
																		<tr>
																			<td><?php echo $membership_m->month;?></td>
																			<td><?php echo $membership_m->month;?></td>
																			<td><?php echo $membership_m->month_sum;?></td>
																		</tr>
																  <?php																			
																		}
																		else{
																	?>
																			<tr><td style="text-align:center; padding-top:10px;" colspan="3">No record</td></tr>
																	<?php
																		}
																  ?>
														</table>
														</td>
													</tr>
												</table>
				</td>

				
			</tr>

	</table>
<?php
	print_container_end();

	print_footer();
?>