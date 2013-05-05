<?php 

	require_once("config.php");
	require_once 'lib/phpmailer/class.phpmailer.php';
	isLogin();
	//isallow();
	
	$theme = current_theme();
	$message	= "";	
	$send_id	= optional_param('send_id','',PARAM_RAW);
	$txtSubject	= get_field('news_letters','subject','id',$send_id);
	$txtMessage	= get_field('news_letters','new_content','id',$send_id);

	if(isset($_POST['send_email']))
	{
		$to_data = optional_param('to','',PARAM_RAW);
		
		$to_ids = explode(',',$to_data);

		foreach($to_ids as $to)
		{	
			$mail = new PHPMailer();

			$mail->From = $CFG->newletterfromemail="info@search4colleges.com";
			$mail->FromName = $CFG->newletterfromname="Search 4 Colleges";

			$mail->IsHTML(true); 
			
			$msg = "<br /><a href='".$CFG->siteroot."'><img src='".$CFG->siteroot."/cmiadmin/theme/cmi/images/cmi_logo.png' alt='Logo' border='0' /></a><br /><br />"; 
					
			$msg .= $txtMessage;

			$msg .= <<<OP
				<div style="background-color:#FFFFFF;">
					  <br /><b>Thanks,</b><br />
						<b>S4c </b>
				</div>
OP;

			$mail->AddAddress($to);

			$mail->Subject = $txtSubject;
			$mail->Body    = $msg;
			
			if($mail->Send())
			{	
				$message = "Mail sent !!";
			}
			else
			{
				$message = "Mail not sent !!";
			}
		}
	}
	
	cmi_include_head('css');
	cmi_include_head('js');
	cmi_include_editor();
	
?>
<script type="text/javascript">
			window.addEvent('domready', function(){
    new FormCheck('frm_test');
});
</script>		
<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
		<td>
			<div class="DotedHeader" width="70%">Manage Emails :</div><br>			
		<?php notify($message);?>
		<table width="100%">
             <tbody>
			 <tr>
             <td align="center">
                 <div>
						
				<form method="post" action="" class="long" id="frm_test" name="frm_test">		
				
				<table align="left">
					<tr>						
						<td colspan="2">
							<input type="hidden" name="send_email" value="send_email">
							<input type="submit" value="Send Email">
						</td>
					</tr>
					<tr>
						<td><br></td>
					</tr>
					<tr>
						<td><span style="font-size: 12px; font-family : Verdana;">To</span> : </td>
						<td><textarea name="to" cols="50" rows="2" class="validate['required']" name="to"></textarea></td>
					</tr>
					<tr>
						<td><br></td>
					</tr>
					<tr>
						<td><span style="font-size: 12px; font-family : Verdana;">Subject</span> : </td>
						<td><span style="font-size: 12px; font-family : Verdana;"><b><?php echo $txtSubject; ?></b></span></td>
					</tr>	
					<tr>
						<td></td>
					</tr>
					<tr>
						<td valign="top"><span style="font-size: 12px; font-family : Verdana;">Message</span> : </td>
						<td>
							<span style="font-size: 12px; font-family : Verdana;"><?php echo $txtMessage; ?></span>							
						</td>
					</tr>	
					<tr>
						<td><br></td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="send_email" value="send_email">
							<input type="submit" value="Send Email">
						</td>
					</tr>
			 </table>

					
				</table>					
				</form>				
				
				</div>
             </td>             
             </tr>            
             </tbody></table>
			 
			</td>
	</tr>
</table>
