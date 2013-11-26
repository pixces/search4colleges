<?php 
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] !='')
{
	$message = '';
	$expired = 0;
	if(strtolower($_SESSION['user_type']) == strtolower('school')) {
		$userData = get_record_sql('SELECT added_date FROM fe_users WHERE id ='.$_SESSION['s4c_user_id'].' AND status="active"');
		$login30Day = strtotime('+30days', $userData->added_date);
		$currentDate = time();
		$membership = get_record_sql('SELECT expiryon,registeredon FROM school_membership WHERE school_id ='.$_SESSION['s4c_user_id'].' AND status="active"');
		if(empty($membership)){
			$expireDate = round(($login30Day - $currentDate)/ (60*60*24));
			if($currentDate > $login30Day){
				$message = '30 days trial period expired. Please <a href="'.$CFG->siteroot.'/membership_buy.html"><b>Renew Membership</b></a>.';
				$expired = 1;
			}
			else {
				$message = '30 days Trial period will expire in '.$expireDate.' days. Please <a href="'.$CFG->siteroot.'/membership_buy.html"><b>Buy Membership</b></a>.';
			}
		}
		else {
			$last30days = strtotime('-30days', $membership->expiryon);			
			$expireDate = round(($membership->expiryon - $currentDate)/ (60*60*24));
			if(($currentDate >= $last30days) ||($currentDate < $login30Day)){
				$message = 'Membership will expire in '.$expireDate.' days. Please <a href="'.$CFG->siteroot.'/membership_buy.html"><b>Buy</b></a> membership.';
				
			}
			if($currentDate > $membership->expiryon){
				$message = 'Membership expired. Please <a href="'.$CFG->siteroot.'/membership_buy.html"><b>Renew Membership</b></a>.';
				$expired = 1;
			}
		}
	}
	
	$user_info = check_login();
	//print_object($user_info);

	$left_array = array('inbox_messages_sel'=>'inbox_messages',
						'sent_messages_sel'=>'sent_messages',
						'personal_details_sel'=>'personal_details',					
						'about_me_sel'=>'about_me',
						'achievements_awards_sel'=>'achievements_awards',
						'professional_experience_sel'=>'professional_experience',
						'searched_history_sel'=>'searched_history',
						'friends_sel'=>'friends',
						'blog_topics_sel'=>'blog',
						'change_password_sel'=>'change_password',
						'counselor_query_sel'=>'counselor_query',
						'counselors_articles_sel'=>'counselors_articles',
						'college_users_sel'=>'college_users',
						'colleges_degrees_sel'=>'colleges_degrees',
						'colleges_scholarships_sel'=>'colleges_scholarships',
						'colleges_culture_campus_life_sel'=>'colleges_culture_campus_life',
						'colleges_gallery_sel'=>'colleges_gallery',
						'colleges_admissions_sel'=>'colleges_admissions',
						'college_leads_sel'=>'college_leads',
						'profile_sel'=>'colleges_profile',
						'college_membership_sel'=>'college_membership',
						'college_affiliated_banks_sel'=>'college_affiliated_banks',
						'statistics_sel'=>'college_statistics',
						'college_news_sel'=>'college_news',
						'counselor_degrees_sel'=>'counselor_degrees'

						);
	foreach($left_array as $key=>$value){
		if($page_name == $value)
			$left_array[$key] = ' style="color: #C7200F; font-weight: bold;" ';
		else 
			$left_array[$key] = '';
	}


	extract($left_array);
	$pictures_and_videos_sel = '';
	if($page_name =='photo_uploaded' || $page_name =='video_uploaded'  || $page_name =='pictures_and_videos'){
		$pictures_and_videos_sel =' style="color: #C7200F; font-weight: bold;" ';
	}

	?>
	 
	 <div id="inner_left">
	  <?php if($message){ ?>
	  <div class="logout_box">
		<?php echo $message; ?>
	  </div>
	  <div class="logout_box_bottom"></div>
	  <?php }?>
	  

	  <div class="logout_box">
	   <strong>
	   <?php 
			if(isset($_SESSION['user_type']) && $_SESSION['user_type'] =='school' && !empty($user_info) ){
				 echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->school_name).'</a>';
			}
			if(isset($_SESSION['user_type']) && $_SESSION['user_type'] =='staff' && !empty($user_info) ){
				 echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->name).'</a>';
			}
			elseif(isset($user_info->first_name)){
				 echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->first_name).'</a>';
			}
	   ?>
	   </strong>
	   
	   
		<?php
		if(isset($user_info->user_id)){
			$sql ="select * from {$CFG->prefix}friends where friend_id=$user_info->user_id and invitaion_status='inprocess' ";
			$frnd_req_pending = get_records_sql($sql);
			if($frnd_req_pending) {
				$frnd_req_pending_count = count($frnd_req_pending);
				echo "<p><span> <a href='".$CFG->siteroot."/friend_request.html' rel =\"lightbox[frnd 260]\" >New Messages ($frnd_req_pending_count)<span></a></p>";
			}
			else{ ?>
				
			<?php	}
		
		}
		?>
		<p><strong><a href='<?php echo $CFG->siteroot;?>/logout.html'>Logout</a></strong></p>
		
		<!--    
		<p>
			<a href="#">Messages </a>
			<span>(5)</span>
			<strong>
				<a href="#">Logout</a>
			</strong>
		</p> -->
	  </div>
	  <div class="logout_box_bottom"></div>
	  <div class="clear"></div>
	  <?php 
		if($expired != 1){  
		if($_SESSION['user_type'] !='staff'){

		  ?>
				<div class="left_sidebar">
				<h3>Messages</h3>
				<ul>
				<li>
					<a href="<?php echo $CFG->siteroot;?>/inbox_messages.html" <?php echo $inbox_messages_sel; ?> >
						Inbox Messages
						<?php
							if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='counselor' ) {					
								$sql = "select count(id) as count_id from {$CFG->prefix}messages where receiver_id = ".$_SESSION['s4c_user_id']." and isdelete_receiver='no'  and  isread_receiver ='no'"; //and is_query='no'
							}else{					
								$sql = "select count(id) as count_id from {$CFG->prefix}messages where receiver_id = ".$_SESSION['s4c_user_id']." and isdelete_receiver='no' and isread_receiver='no' ";
							}
							$inboxt_list_count = get_record_sql($sql); 
							echo ($inboxt_list_count->count_id ==0)?'':"( ".$inboxt_list_count->count_id." )";				
						?>
					</a>
				</li>
				<li>
					<a href="<?php echo $CFG->siteroot;?>/sent_messages.html" <?php echo $sent_messages_sel; ?> >
						Sent Messages
						<!--
						<?php	
						if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='counselor' ) {
							$sent_list_count = count_records('messages', 'isdelete_sender', 'no', 'is_query', 'no', 'sender_id', $_SESSION['s4c_user_id']); 
							echo ($sent_list_count ==0)?'':"( ".$sent_list_count." )";	
						}else{
							$sent_list_count = count_records('messages', 'parent_id','0','isdelete_sender', 'no', 'sender_id', $_SESSION['s4c_user_id']); 
							echo ($sent_list_count ==0)?'':"( ".$sent_list_count." )";	
						}			
						?>
						-->
					</a>
				</li>
				</ul>

				</div>
			<?php } ?>
	<div class="left_sidebar-bottom"></div>
	<div class="left_sidebar">
	<h3>Profile</h3>
	<ul>
		<?php 
			if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'school' || $_SESSION['user_type'] == 'staff') {

                //get details of the school
                $student_data = get_record_sql('Select * from '.$CFG->prefix.'schools where user_id = '.$_SESSION['s4c_user_id'].' and status = "active"');

                if($_SESSION['user_type'] == 'school'){
				?>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_profile.html?view=<?php echo $_SESSION['s4c_user_id']; ?> " <?php echo $profile_sel; ?> >Profile</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_degrees.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $colleges_degrees_sel; ?> >Majors and Degrees</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_culture_campus_life.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $colleges_culture_campus_life_sel; ?> >Culture & Campus Life</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_scholarships.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $colleges_scholarships_sel; ?> >Scholarships</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_news.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $college_news_sel; ?> >News flash</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_gallery.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $colleges_gallery_sel; ?> >Gallery</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_admissions.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $colleges_admissions_sel; ?> >Examination details</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_leads.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $college_leads_sel; ?> >Leads</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_membership.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $college_membership_sel; ?> >Membership Details</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_affiliated_banks.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $college_affiliated_banks_sel; ?> >List affiliated banks</a></li>
						<?php if ($student_data->statistics) { ?>
                        <li><a href="<?php echo $CFG->siteroot;?>/college_statistics.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $statistics_sel; ?> >Statistics</a></li>
                        <?php } ?>
						<li><a href="<?php echo $CFG->siteroot;?>/college_users.html?view=<?php echo $_SESSION['s4c_user_id']; ?>" <?php echo $college_users_sel; ?> >Manage Users</a></li>
				<?php } else {

                        //get the staff details and school details
                        $school_staff_user = get_record_sql('Select * from '.$CFG->prefix.'school_staff_user where fe_staff_id = '.$_SESSION['s4c_user_id'].' and status = "active"');
                        $student_data = get_record_sql('Select * from '.$CFG->prefix.'schools where user_id = '.$school_staff_user->fe_school_id.' and status = "active"');
                    ?>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_profile.html?view=<?php echo $student_data->user_id; ?> " <?php echo $profile_sel; ?> >Profile</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_degrees.html?view=<?php echo $student_data->user_id; ?>" <?php echo $colleges_degrees_sel; ?> >Majors and Degrees</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_culture_campus_life.html?view=<?php echo $student_data->user_id; ?>" <?php echo $colleges_culture_campus_life_sel; ?> >Culture & Campus Life</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_scholarships.html?view=<?php echo $student_data->user_id; ?>" <?php echo $colleges_scholarships_sel; ?> >Scholarships</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_news.html?view=<?php echo $student_data->user_id; ?>" <?php echo $pictures_and_videos_sel; ?> >News flash</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_gallery.html?view=<?php echo $student_data->user_id; ?>" <?php echo $colleges_gallery_sel; ?> >Gallery</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/colleges_admissions.html?view=<?php echo $student_data->user_id; ?>" <?php echo $colleges_admissions_sel; ?> >Examination details</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_leads.html?view=<?php echo $student_data->user_id; ?>" <?php echo $college_leads_sel; ?> >Leads</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_membership.html?view=<?php echo $student_data->user_id; ?>" <?php echo $college_membership_sel; ?> >Membership Details</a></li>
						<li><a href="<?php echo $CFG->siteroot;?>/college_affiliated_banks.html?view=<?php echo $student_data->user_id; ?>" <?php echo $college_affiliated_banks_sel; ?> >List affiliated banks</a></li>
                    <?php if ($student_data->statistics) { ?>
                        <li><a href="<?php echo $CFG->siteroot;?>/college_statistics.html?view=<?php echo $student_data->user_id; ?>" <?php echo $statistics_sel; ?> >Statistics</a></li>
                    <?php } ?>
				<?php }
				?>
		<?php
		}
		else{
		?>
			<li><a href="<?php echo $CFG->siteroot;?>/personal_details.html" <?php echo $personal_details_sel; ?> >Personal Details</a></li>
			<li><a href="<?php echo $CFG->siteroot;?>/pictures_and_videos.html" <?php echo $pictures_and_videos_sel; ?> >Pictures & Videos</a></li>
			<li><a href="<?php echo $CFG->siteroot;?>/about_me.html" <?php echo $about_me_sel; ?> >About Me</a></li>
			<li><a href="<?php echo $CFG->siteroot;?>/achievements_awards.html" <?php echo $achievements_awards_sel; ?> >Achievements & Awards</a></li>
			<li><a href="<?php echo $CFG->siteroot;?>/professional_experience.html" <?php echo $professional_experience_sel; ?> >Professional Experience</a></li>
		
		<?php 
		} 
		?>
	</ul>

	</div>
	<div class="left_sidebar-bottom"></div>
	<div class="left_sidebar">
	<h4>Manage Sections</h4>
	<ul>
	<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'counselor') { ?>
		<li><a href="<?php echo $CFG->siteroot;?>/counselor_degrees.html" <?php echo $counselor_degrees_sel; ?> >Majors and Degrees</a></li>
		<li><a href="<?php echo $CFG->siteroot;?>/counselor_query.html" <?php echo $counselor_query_sel; ?> >Reply Queries</a></li>
		<li><a href="<?php echo $CFG->siteroot;?>/counselors_articles.html" <?php echo $counselors_articles_sel; ?> >Manage Articles</a></li>
	<?php } ?>
	<li><a href="<?php echo $CFG->siteroot;?>/searched_history.html" <?php echo $searched_history_sel; ?> >Searched History</a></li>
	<?php 
		if($_SESSION['user_type'] !='staff' && $_SESSION['user_type'] !='school'){
	?>
			<li><a href="<?php echo $CFG->siteroot;?>/friends.html" <?php echo $friends_sel; ?> >Friends </a></li>
	<?php 
		}
	 ?>
	<li><a href="<?php echo $CFG->siteroot;?>/blog.html" <?php echo $blog_topics_sel; ?> >BLOG Topics/Comments</a></li>
	<li><a href="<?php echo $CFG->siteroot;?>/change_password.html" <?php echo $change_password_sel; ?> >Change Password</a></li>
	</ul>

	</div>
	<div class="left_sidebar-bottom"></div>
	
	<div class="calender">
	<strong>What's Ahead</strong>
	<h3>My Calendar</h3>
	<a href="<?php echo $CFG->siteroot;?>/my_calendar.html">View / Edit
	</a></div>
	<div class="in_login">
			<div class="in_login-top"></div>
			<h3>Get it from States</h3>
			<div align="center">
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/
				cabs/flash/swflash.cab#version=6,0,40,0" width="219" height="200" id="mymoviename"> 
				<param name="wmode" value="transparent">
				<param name="movie" value="<?php echo $CFG->siteroot;?>/images/usaMap.swf" /> 		 
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<embed src="<?php echo $CFG->siteroot;?>/images/usaMap.swf" quality="high" bgcolor="#ffffff" width="219" height="200" name="mymoviename" align="" wmode="transparent" type="application/x-shockwave-flash" 
				pluginspage="http://www.macromedia.com/go/getflashplayer"> 
				</embed> 
			</object>
			</div>
			<div class="in_login-bottom"></div>
		  </div>
		  <?php }?>
		</div>

<?php }else{ ?>

<div id="inner_left">
      <div class="in_login">
        <div class="in_login-top"></div>
		 <?php include_once("includes/unreg_login.php"); ?>
        
          
        <div class="in_login-bottom"></div>
      </div>
      <div id="join-us">
        <div class="join-us-top"></div>
        <h2> <b style="font-size:20px; margin:0px;">REGISTER NOW</b> </h2>
        <div style=" background-color: #FF9C00; color: #2C2C2C; font-size: 12px; padding-left:10px; padding-right:10px; font-weight:bold;">Join our site to be a member of the Education Community</div>
          <ul style="padding-bottom:5px;">
            <li><a href="<?php echo $CFG->siteroot;?>/student_form.html">Student</a></li>
            <li><a href="<?php echo $CFG->siteroot;?>/parent_form.html">Parent</a></li>
            <li><a href="<?php echo $CFG->siteroot;?>/school_form.html">School / High School / College / Institute</a></li>
            <li><a href="<?php echo $CFG->siteroot;?>/teacher_professor_form.html">Teacher / Professor / Educational Professional</a></li>
            <li><a href="<?php echo $CFG->siteroot;?>/counselor_form.html">Counselor</a></li>
          </ul>
        <div class="join-us-bottom"></div>
      </div>
      <div class="in_login">
        <div class="in_login-top"></div>
        <h3>Get it from States</h3>
        <div align="center">
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/
				cabs/flash/swflash.cab#version=6,0,40,0" width="219" height="200" id="mymoviename"> 
				<param name="wmode" value="transparent">
				<param name="movie" value="<?php echo $CFG->siteroot;?>/images/usaMap.swf" /> 		 
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<embed src="<?php echo $CFG->siteroot;?>/images/usaMap.swf" quality="high" bgcolor="#ffffff" width="219" height="200" name="mymoviename" align="" wmode="transparent" type="application/x-shockwave-flash" 
				pluginspage="http://www.macromedia.com/go/getflashplayer"> 
				</embed> 
			</object>
		</div>
        <div class="in_login-bottom"></div>
      </div>
    </div>

	
<?php }?>
