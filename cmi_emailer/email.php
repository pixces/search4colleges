<?php
if(empty($_POST))
{
	header('Location:../');
	exit;
}

require_once 'class.phpmailer.php';
require_once 'securimage.php';
require_once '../cmiadmin/config.php';

if(isset($_POST['captcha']))
{
	$captcha_flag = true;
	$img = new Securimage();
	if($img->check($_POST['captcha']))
	{
		$valid = true;
	}
	else
	{
		$valid = false;
	}
}
else
{
	$captcha_flag = false;
	$valid = false;
}

if(isset($_POST['config'])  && $_POST['config'] == 'colleges_enquiry'){
	
	$fullname				= optional_param('Fullname','', PARAM_TEXT);	
	$email					= optional_param('Email','', PARAM_TEXT);	
	$phone					= optional_param('Telephone','', PARAM_TEXT);	
	$enquiry				= optional_param('Your_Message','', PARAM_TEXT);
	$interest				= optional_param('interest','', PARAM_TEXT);
	$time   				= optional_param('time','', PARAM_TEXT);
	$s4c_user_id			= optional_param('s4c_user_id','', PARAM_TEXT);

	$add_enquiry					= new object();
	
	if(!isset($_SESSION['s4c_user_id']) || $s4c_user_id != ''){
		$add_enquiry->school_id			= $s4c_user_id;
	}
	else{
		$add_enquiry->school_id			= $_SESSION['s4c_user_id'];
	}
		
	$add_enquiry->fullname			= $fullname;
	$add_enquiry->email				= $email;
	$add_enquiry->enquiry			= $enquiry;
	$add_enquiry->phone				= $phone;
	$add_enquiry->interest			= $interest;
	$add_enquiry->time_contact		= $time;
	$add_enquiry->added_date		= time();
	$add_enquiry->status			= 'active';

   
	$new = insert_record('schools_enquiry', $add_enquiry);
}
if(isset($_POST['Email']) && !isset($_POST['colleges_enquiry'])){

	$to_email_id			= optional_param('to_email_id','', PARAM_TEXT);
	$email					= optional_param('Email','', PARAM_TEXT);	
	$password				= optional_param('Password','', PARAM_TEXT);	
	$first_name				= optional_param('Full_Name','', PARAM_TEXT);	
	$gender					= optional_param('Gender','', PARAM_TEXT);	
	$date_of_birth			= optional_param('Date_of_Birth','', PARAM_TEXT);	
	$educational_interest	= optional_param('Primary_Educational_Interest','', PARAM_TEXT);
	$added_date				= time();
	
	$add_mode_student						= new object();
	$add_mode_student->first_name			= $first_name;
	$add_mode_student->gender				= $gender;
	$add_mode_student->date_of_birth		= $date_of_birth;
	$add_mode_student->educational_interest = $educational_interest;
	$add_mode_student->added_date			= $added_date;
	$new = insert_record('student', $add_mode_student);

	$sql = " SELECT max( id ) id FROM {$CFG->prefix}student ";
	$student_id = get_record_sql($sql);	
	$add_mode_fe				= new object();	
	$add_mode_fe->email			= $email ;
	$add_mode_fe->user_id		= $student_id->id ;
	$add_mode_fe->password		= md5($password);
	$add_mode_fe->added_date	= $added_date;
	$new = insert_record('fe_users', $add_mode_fe);
}
require_once 'config.php';
if((!$captcha_flag && !$valid) || ($captcha_flag && $valid))
{	
	
		$mail = new PHPMailer(true);
		if(isset($_POST['config']) && $_POST['config'] && $_POST['config'] != '')
		{
			$tag = $_POST['config'];
		}
		else
		{
			echo 'Error !!! <br/>Config tag not Specified inside the form tag!!!';
			exit;
		}

		$mail->IsMail();
		if(isset($_POST['Email'])){
			$mail->From       = $_POST['Email'];
		}
		if(isset($_POST['EmailId'])){
			$mail->From       = $_POST['EmailId'];
		}
		foreach ($_POST as $key => $value)
		{
			if($key=='Firstname')
			{
				$firstname   = $value;
			}
			if($key=='Lastname')
			{
				$lastname   = $value;
			}
			if($key=='Middlename')
			{
				$middlename   = $value;
			}
			if($key=='Fullname')
			{
				$fullname   = $value;
			}
		}

		if(isset($firstname) && isset($lastname) && isset($middlename))
		{
			$mail->FromName = $firstname." ".$middlename." ".$lastname;
		}
		elseif(isset($firstname) && isset($lastname))
		{
			$mail->FromName = $firstname." ".$lastname;
		}
		elseif(isset($firstname))
		{
			$mail->FromName = $firstname;
		}
		elseif(isset($fullname))
		{
			$mail->FromName = $fullname;
		}
		else
		{
			$mail->FromName = 'No Name';
		}

		if($tag)
		{
			$mail->Subject = $CFG[$tag]->subject;
			$to_list = explode(',',$CFG[$tag]->to);
			if(!empty($to_list))
			{
				foreach($to_list as $to)
				{
					$mail->AddAddress($to);
				}
			}

			if(!empty($CFG[$tag]->cc))
			{
				$cc_list = explode(',',$CFG[$tag]->cc);
				if(!empty($cc_list))
				{
					foreach($cc_list as $cc)
					{
						$mail->AddCC($cc);
					}
				}
			}

			if(!empty($CFG[$tag]->bcc))
			{
				$bcc_list = explode(',',$CFG[$tag]->bcc);
				if(!empty($bcc_list))
				{
					foreach($bcc_list as $bcc)
					{
						$mail->AddBCC($bcc);
					}
				}
			}

			$mail->AddReplyTo($_POST['Email'],$fullname);

		}
		else
		{
			echo 'Error !!! <br/>Config tag not Specified inside the form tag!!!';
			exit;
		}

		$mail->WordWrap   = $CFG['common']->emailwordwrap;
		$mail->IsHTML(true);

		$message = '';

		foreach ($_POST as $key => $value)
		{
			if($key !='B2' && $key !='Submit_x' && $key !='Submit_y' && $key !="button" && $key !='tag' && $key != 'config' && $key !='captcha' && $key !='B3' && $key !='x' && $key !='y' && $key !='Submit' && $key !='submit' && $key !='countdown' &&$key !='s4c_user_id' && $key !='to_email_id')
			{
				if($key =='depart_date' || $key =='pickup_date' || $key == 'return_date' ){
					$message .= "\n".ucwords(str_replace('_',' ',$key))." : ".date("m:d:Y   H:i:s",$value);
				}
				else if(is_array($value))
				{
					$message .= "\n".ucwords(str_replace('_',' ',$key))." : ".implode(',',$value);
				}
				else 
				{
					$message .= "\n".ucwords(str_replace('_',' ',$key))." : ".$value;
				}
				$message .= "<hr noshade size=1>\n";
				$message .= "</b>";
			}
		
		} 
		if($_FILES)
		{
			foreach($_FILES as $key=>$value)
			{
				if($value['error'] == '0')
				{
					$mail->AddAttachment($value['tmp_name'],$value['name']);
				}
			}
		}

		if(file_exists('email_content.html'))
		{
			$body = file_get_contents('email_content.html');
			$body = str_replace('##content##',$message,$body);
		}
		else
		{
			echo 'Email Content HTML file not Found !!!';
		}

		$mail->Body =$body;
		$ans = $mail->Send($message);

		if($CFG[$tag]->thank_you_mail)
		{
			if($ans)
			{
				$tmail = new PHPMailer(true);
				$tmail->IsMail();
				$tmail->WordWrap   = $CFG['common']->emailwordwrap;
				$tmail->IsHTML(true);
				$tmail->Subject		= $CFG[$tag]->thank_you_subject;
				$tfname = 'thank_you.html';
				if(file_exists($tfname))
				{
					$message			= file_get_contents($tfname);
				}
				else
				{
					$message			= 'Thank You Message HTML file "'.$tfname.'" not found !!!<br/>Create a new file called "'.$tfname.'" inside cmi_emailer folder to replace this message with the Thank you email content...';
				}
				$tmail->AddAddress($_POST['Email'],$mail->FromName);
				$tmail->AddReplyTo($mail->From,$mail->FromName);
				$tmail->FromName	= $CFG[$tag]->thank_from_name;
				$tmail->From		= $CFG[$tag]->thank_from;
				$tmail->Body = $message;
				if($tmail->Send($message))
				{
					header('Location:../thank_you.html');
				}
				else
				{
					//do nothing
					header('Location:../thank_you.html');
				}
			}
			else
			{
				//header('Location:../'.$tag.'_email_error.html');
				header('Location:../email_error.html');
			}
		}
		else
		{
			if($ans)
			{
				header('Location:../thank_you.html');
			}
			else
			{
				//header('Location:../'.$tag.'_email_error.html');
				header('Location:../email_error.html');
			}
		}

}
else
{
	header('Location:../invalid_code.html');
}

?>