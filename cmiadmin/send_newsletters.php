<?php 
	require_once("config.php");
	define('menu_status', 'Manage News');
	$theme = current_theme();
	isLogin();
	isallow();
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();

	$not_send_address = array();
	
	$news_id	= required_param('news_id', PARAM_INT);
	$send_id	= required_param('send_id', PARAM_RAW);
	$member_id	= optional_param('member_id', PARAM_RAW);

	
	$txtSubject	= get_field('news_letters','subject','id',$news_id);
	$txtMessage	= get_field('news_letters','new_content','id',$news_id);
	
	require_once 'lib/phpmailer/class.phpmailer.php';

	if($send_id == 'selectall')
	{
		if($member_id == '0')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '1')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'student' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '2')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'parent' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '3')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'counselor' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '4')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'teacher' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '5')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'staff' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
		elseif($member_id == '6')
		{
			$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE user_type = 'school' AND newsletter='yes'";
			$data=get_records_sql($sql);
		}
	}
	else
	{
		$sql="SELECT * FROM {$CFG->prefix}fe_users WHERE id IN (".$send_id.")";
		$data=get_records_sql($sql);
	}
	if($data)
	{
		foreach($data as $value)
		{
			$mail = new PHPMailer();

			$mail->From = "info@search4colleges.com";
			$mail->FromName = "Search 4 Colleges";
			$mail->IsHTML(true); 
			
			$mail->AddAddress($value->email);

			$hash = md5($value->email.$value->id);
			$verify = "<br><br>If you want to stop receiving Newsletters from us,then Please click the below Link<br><a href='{$CFG->siteroot}/unsubscribe.html?email=$value->email&hash=$hash'>{$CFG->siteroot}/unsubscribe.html?email=$value->email&hash=$hash</a>";

			$msg = "<br /><a href='".$CFG->siteroot."'><img src='".$CFG->siteroot."/cmiadmin/theme/cmi/images/cmi_logo.png' alt='Logo' border='0' /></a><br /><br />"; 
					
			$msg .= $txtMessage.$verify;

			$msg .= <<<OP
				<div style="background-color:#FFFFFF;">
					  <br /><b>Thanks,</b><br />
						<b>S4c </b>
				</div>
OP;

			//$txtMessages = $txtMessage.$verify; 

			$mail->Subject = $txtSubject;
			$mail->Body    = $msg;
		
			if($mail->Send())
			{	$send_address[] = $value->email;
				$message = "Message sent to All Subscribed Email ID !!";
				cmi_add_to_log('email','delivery','Email send to  All Subscribed Emails');
			}
			else
			{
				$not_send_address[] = $value->email;
				$message = "Message not sent to All Subscribed Email ID !!";
				cmi_add_to_log('email','delivery','Email not send to  Subscribed Emails');
			}
			

		}
	}
	else
	{
		$message = "No Subscribers Present !!";
	}
	$i=1;
	 if(count($not_send_address)>0)
	 {
	
		$table->head = array ("Srno","Email");
		$table->align = array ("center", "center");
		$table->width = "95%";
		foreach($not_send_address as $value) {

			$table->data[] = array ("$i","$value");
			$i++;
		}
	 }
							
?>
<script type="text/javascript">
		window.addEvent('domready', function(){
			new FormCheck('frm_addnewsletters');
			new DatePicker('.demo_vista', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					format: 'd F, Y',
					toggleElements: '.date_toggler'
			});
		});
		
		function confirm_send()
			{
				call_alert_confirm_send_newsletters("Are you sure you want to Send this News Letter?",'newsletters_manage.php');
					
			}

		function get_category(id,flag,pid){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'ajax_handler.php',
				 data: { 'id' : id,'flag' : flag },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(pid).innerHTML = response;
				}
			 
			}).send();	
			}
		}
</script>
	<script type="text/javascript">
	window.addEvent('domready', function(){
				
	});
	</script>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<div class="DotedHeader" width="70%">
				Email News Letter
			</div>
		    
		</td>
	</tr>
	<tr>
		<tr>
			<td>		
				<table width="100%">
					 <tbody>
						<tr>
							 <td width="5%"></td>
							 <td align="center" width="90%">
								<div>
									<?php					
										if(empty($not_send_address))
										{
											print_heading('News Letters was Successfully Sent To All Selected Users');
										}
										else if (!empty($table)) {
											print_heading('News Letters Were Not Sent To the Below List of Users');
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