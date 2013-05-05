<?php 	
	require_once("config.php");
	require_once("lib/phpmailer/class.phpmailer.php");
	$theme = current_theme();
	isLogin();
	isallow();
	$edit_flag			= false;
	$caption_txt		= "Email";
	$txtName			= "";
	$txtDescription		= "";
	if(isset($_GET) && isset($_GET['edit']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit', PARAM_INT);
		$edit_data		= get_record('newsletter', 'id', $edit_id);
		$txtName		= $edit_data->title;	
		$txtDescription	= $edit_data->description;
	}
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
	cmi_include_editor();

if(isset($_POST)&&!empty($_POST))
{

$txtName			= required_param('txtname', PARAM_RAW);
$txtDescription		= required_param('txtdescription', PARAM_RAW);
$txtMember			= required_param('txtmember', PARAM_TEXT);

//------------------------------------------ email to all retailers, customers and service providers --------------------------------------------------//

if($txtMember=="rsc")
{
	$retail="SELECT email FROM {$CFG->prefix}agents WHERE agent!='distributor'";
	$retailerdata = get_records_sql($retail);
	if($retailerdata)
	{
		foreach($retailerdata as $retailer)
		{
			$to= $retailer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");

			if($mail->Send())
			{
				$message = "Email Sent to All Retailers, Service Providers ";
			}
			else
			{
				$message = "Email Not Sent to All Retailers, Service Providers ";
			} 	
		}
	}
	else
	{
		$message = "No Retailers and Service Providers Added Yet";
	}

	$customerdata = get_records('customer');
	if($customerdata)
	{
		foreach($customerdata as $customer)
		{
			$to= $customer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");

			if($mail->Send())
			{
				$message.="& Customers";
			}
			else
			{
				$message.="& Customers";
			} 	
		}
	}
	else
	{
		$message.="No Customers Added Yet";
	}
}

//------------------------------------------ email to all retailers and service providers --------------------------------------------------//

if($txtMember=="rs")
{
	$retail="SELECT email FROM {$CFG->prefix}agents WHERE agent!='distributor'";
	$retailerdata = get_records_sql($retail);
	if($retailerdata)
	{
		foreach($retailerdata as $retailer)
		{
			$to= $retailer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");
			if($mail->Send())
			{
				$message = "Email Sent to All Retailers & Service Providers";
			}
			else
			{
				$message = "Email Not Sent to All Retailers & Service Providers";
			} 	
		}
	}
	else
	{
		$message = "No Retailers & Service Providers Added Yet";
	}
}

//------------------------------------------ email to all retailers --------------------------------------------------//

if($txtMember=="retailer")
{
	$retailerdata = get_records('agents','agent','retailer');
	if($retailerdata)
	{
		foreach($retailerdata as $retailer)
		{
			$to= $retailer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");
			if($mail->Send())
			{
				$message = "Email Sent to All Retailers";
			}
			else
			{
				$message = "Email Not Sent to All Retailers";
			} 	
		}
	}
	else
	{
		$message = "No Retailers Added Yet";
	}
}

//------------------------------------------ email to all customers --------------------------------------------------//

if($txtMember=="customer")
{
	$customerdata = get_records('customer');
	if($customerdata)
	{
		foreach($customerdata as $customer)
		{
			$to= $customer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");
			if($mail->Send())
			{
				$message = "Email Sent to All Customers";
			}
			else
			{
				$message = "Email Not Sent to All Customers";
			} 	
		}
	}
	else
	{
		$message = "No Customers Added Yet";
	}
}

//------------------------------------------ email to all service providers --------------------------------------------------//

if($txtMember=="service_provider")
{
	$retailerdata = get_records('agents','agent','service_provider');
	if($retailerdata)
	{
		foreach($retailerdata as $retailer)
		{
			$to= $retailer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");
			if($mail->Send())
			{
				$message = "Email Sent to All Service Providers";
			}
			else
			{
				$message = "Email Not Sent to All Service Providers";
			} 	
		}
	}
	else
	{
		$message = "No Service Providers Added Yet";
	}

}

//------------------------------------------ email to all certified specialists --------------------------------------------------//

if($txtMember=="certified")
{
	$retailerdata = get_records('agents','certificate','1');

	if($retailerdata)
	{
		foreach($retailerdata as $retailer)
		{
			$to= $retailer->email;
			$sub="News Letter";
			$msg="Title: ".$txtName." <br><br> Message:".$txtDescription;
			global $CFG; 
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->From = $CFG->frommail;
			$mail->FromName = $CFG->fromname;
			$mail->Subject = $sub;
			$mail->Body = $msg;
			$mail->AddAddress($to);
			$mail->AddReplyTo($CFG->frommail, "News Letter");
			if($mail->Send())
			{
				$message = "Email Sent to All Certified Specialists";
			}
			else
			{
				$message = "Email Not Sent to All Certified Specialists";
			} 	
		}
	}
	else
	{
		$message = "No Certified Specialists Added Yet";
	}
}

//----------------------------------------------------------------------------------------------------------------------------//


}

?>
<script type="text/javascript">
			window.addEvent('domready', function(){
					new FormCheck('frm_newsletter');
			});
</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post" action="" class="long" id="frm_newsletter" name="frm_newsletter">
			<?php
				if($edit_flag)
				{
					echo '<input name="edit_mode" type="hidden" value="true"/>';
					echo '<input name="user_id"  type="hidden"  value="'.$edit_data->id.'"/>';	
				}
			?>	
			<table width="100%" border="0">
			  <tbody>
				<tr> 
				  <td width="5%"> </td>
				  <td align="center" width="90%" colspan="2">  <?php if(isset($message)) { echo "( ".$message." )"; } ?>  </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="DotedHeader" width="90%" colspan="2"> <?php echo $caption_txt;?> Newsletter :</td>
				  <td width="5%"> </td>
				</tr>				
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%">Title : &nbsp;</td>
				  <td width="70%"><input name="txtname" value="<?php echo $txtName;?>" maxlength="30" id="txtname" style="width: 715px;" type="text" class="validate['required'] form_box" />
				  </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td style="height: 21px;" width="5%"> </td>
				  <td style="height: 21px;" width="90%" colspan="2"> </td>
				  <td style="height: 21px;" width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Description : &nbsp;</td>
				  <td width="70%">
				  <textarea name="txtdescription" id="editor1" class="form_box"><?php if(isset($txtDescription)) echo $txtDescription;?>
					</textarea>
					<script type="text/javascript">
						init_ckfinder('editor1');
					</script>
				  </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%">To All: &nbsp;</td>
				  <td width="70%">
				  <select name="txtmember" id="txtmember" class="validate['required'] form_box">
					<option value="0">Select</option>
					
					<?php if(isset($txtMember) && $txtMember=="rsc" ) { ?>
						<option value="rsc" selected>Students</option>
					<?php } else { ?>
						<option value="rsc">Students</option>

					<?php } if(isset($txtMember) && $txtMember=="rs" ) { ?>
						<option value="rs" selected>Parents</option>
					<?php } else { ?>
						<option value="rs">Parents</option>
					
					<?php } if(isset($txtMember) && $txtMember=="customer" ) { ?>
						<option value="customer" selected>Customer</option>
					<?php } else { ?>
						<option value="customer">Customer</option>
					
					<?php } if(isset($txtMember) && $txtMember=="retailer" ) { ?>
						<option value="retailer" selected>Retailer</option>
					<?php } else { ?>
						<option value="retailer">Retailer</option>
					
					<?php } if(isset($txtMember) && $txtMember=="service_provider" ) { ?>
						<option value="service_provider" selected>Service Provider</option>
					<?php } else { ?>
						<option value="service_provider">Service Provider</option>
					
					<?php } if(isset($txtMember) && $txtMember=="certified" ) { ?>
						<option value="certified" selected>Certified Specialists</option>
					<?php } else { ?>
						<option value="certified">Certified Specialists</option>
					<?php } ?>

				  </select>				 
				  </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"><input name="btnSave" id="btnSave" type="Submit" value="Send">
				  <!-- <INPUT TYPE="button" VALUE="Back" onClick="history.go(-1);return true;"> -->
				  </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"></td>
				  <td width="5%"> </td>
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
