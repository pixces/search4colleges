<?php
	unset($CFG);
	//1st form
	$CFG['Contact_us']->to						= 'info@search4colleges.com';
	$CFG['Contact_us']->cc						= 'siddhesh@cmitech.in';
	$CFG['Contact_us']->bcc						= 'faisal.ali@cmitech.in';
	$CFG['Contact_us']->subject					= 'For Contact Us';
	$CFG['Contact_us']->thank_you_subject		= 'Thank You';
	$CFG['Contact_us']->thank_you_mail			= 1;
	$CFG['Contact_us']->thank_from				= 'info@search4colleges.com';
	$CFG['Contact_us']->thank_from_name			= 'Administrator';

	$CFG['common']->emailwordwrap				= 80;
	
	// value for college enquiry
	$CFG['colleges_enquiry']->to						= $to_email_id;
	$CFG['colleges_enquiry']->cc						= 'siddhesh@cmitech.in';
	$CFG['colleges_enquiry']->bcc						= 'info@search4colleges.com';
	$CFG['colleges_enquiry']->subject					= 'College Enquiry from search4colleges.com';
	$CFG['colleges_enquiry']->thank_you_subject		    = 'Thank You';
	$CFG['colleges_enquiry']->thank_you_mail			= 1;
	$CFG['colleges_enquiry']->thank_from				= 'info@search4colleges.com';
	$CFG['colleges_enquiry']->thank_from_name			= 'Administrator';

?>