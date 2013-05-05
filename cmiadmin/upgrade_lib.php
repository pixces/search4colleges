<?php
		unset($CFG_UPGRADE);
		$CFG_UPGRADE->framename					= 'framename';
		$CFG_UPGRADE->perpage					= '50';
		$CFG_UPGRADE->maxbytes					= '0';
		$CFG_UPGRADE->version					= '';
		$CFG_UPGRADE->smtphosts					= '';
		$CFG_UPGRADE->debug						= '6143';
		$CFG_UPGRADE->debugdisplay				= '1';
		$CFG_UPGRADE->server_status				= 'dev';
		$CFG_UPGRADE->theme						= 'cmi';
		$CFG_UPGRADE->admin						= 'admin';
		$CFG_UPGRADE->session_error_counter		= '12';
		$CFG_UPGRADE->dbsessions				= '0';
		$CFG_UPGRADE->sessiontimeout			= '7200';
		$CFG_UPGRADE->sessioncookiepath			= '/';
		$CFG_UPGRADE->sessioncookiedomain		= '';
		$CFG_UPGRADE->gdversion					= '2';
		$CFG_UPGRADE->log_per_page				= '10';
		$CFG_UPGRADE->testimonial_per_page		= '5';
		$CFG_UPGRADE->image_limit				= '5';
		$CFG_UPGRADE->video_limit				= '5';
		$CFG_UPGRADE->faq_per_page				= '5';
		$CFG_UPGRADE->sessioncookie				= str_replace(".com","",$CFG->dbname);
		$CFG_UPGRADE->backup_frommail			= 'cmiadmin';
		$CFG_UPGRADE->backup_tomail				= 'info@domain.com';
		$CFG_UPGRADE->frommail					= 's4c@cmitech.in';
		$CFG_UPGRADE->fromname					= 's4c@cmitech.in';

		$CFG_UPGRADE->backup_mailbody			= 'Thanks';

		unset($SECTION_ARRAY);
		$SECTION_ARRAY = array	( 


	array('Manage Site',						'',							'0',					'1',	'active','unhide'),
	array('Manage Entities',					'',							'0',					'2',	'active','unhide'),
	array('Manage CMS',							'',							'0',					'3',	'active','unhide'),
	array('Manage Blog',						'',							'0',					'4',	'active','unhide'),	
	array('Manage Articles',					'',							'0',					'5',	'active','unhide'),	
	array('Manage Media',						'',							'0',					'6',	'active','unhide'),	
	array('Manage Schools',						'',							'0',					'7',	'active','unhide'),
	array('Newsletter',							'',							'0',					'7',	'active','unhide'),
	array('Users',								'',							'0',					'8',	'active','unhide'),
	array('Logout',								'logout.php',				'0',					'99',	'active','unhide'),
	
	//Manage Site
	array('Upgrade',							'upgrade.php',				'Manage Site',			'0',	'active','unhide'),
	array('Preview Site',						'../',						'Manage Site',			'0',	'active','unhide'),
	array('Change Password',					'change_password.php',		'Manage Site',			'0',	'active','unhide'),

	//Manage Entities
	array('View Students',						'student_manage.php',		'Manage Entities',		'0',	'active','unhide'),
	array('View Parents',						'parents_manage.php',		'Manage Entities',		'0',	'active','unhide'),
	array('View Education Professional',		'teachers_manage.php',		'Manage Entities',		'0',	'active','unhide'),	
	array('View Schools/colleges/institutes',	'school_manage.php',		'Manage Entities',		'0',	'active','unhide'),
	array('View Counselor',						'counselor_manage.php',		'Manage Entities',		'0',	'active','unhide'),


	//Manage CMS
	array('Manage Faq',							'faq_manage.php',			'Manage CMS',			'0',	'active','unhide'),
	array('Manage Majors',						'majors_manage.php',		'Manage CMS',			'0',	'active','unhide'),
	array('Manage Blog',						'blog_manage.php',			'Manage CMS',			'0',	'inactive','unhide'),
	array('Manage State',						'state_manage.php',			'Manage CMS',			'0',	'active','unhide'),
	array('Manage City',						'city_manage.php',			'Manage CMS',			'0',	'active','unhide'),
	array('About Search4Colleges',				'page_content_manage.php',	'Manage CMS',			'0',	'active','unhide'),
	array('Manage Link',						'usefullink_manage.php',	'Manage CMS',			'0',	'active','unhide'),
	array('Manage Financial Provider',			'financial_provider_manage.php', 'Manage CMS',		'0',	'active','unhide'),

	//Manage Blog
	array('Blog Category',						'blog_category_manage.php',	'Manage Blog',			'0',	'active','unhide'),
	array('Blog',								'blog_manage.php',			'Manage Blog',			'0',	'active','unhide'),

	//Manage Article
	array('Article Category',					'articles_category_manage.php','Manage Articles',	'0',	'active','unhide'),
	array('Articles',							'articles_manage.php',		'Manage Articles',		'0',	'active','unhide'),

	//Manage Media
	array('Event Details',						'event_details_manage.php',	'Manage Media',			'0',	'active','unhide'),
	array('Manage Photos/Videos',				'gallery_manage.php',		'Manage Media',			'0',	'active','unhide'),
	array('Manage College Photos/Videos',				'college_gallery_manage.php','Manage Media',			'0',	'active','unhide'),

	//Manage Schools
	array('School Type',						'school_type_manage.php',					'Manage Schools',	'0',	'active',	'unhide'),
	array('Type of Campus Area',				'type_of_campus_area_manage.php',					'Manage Schools',	'0',	'active',	'unhide'),
	array('School Student Population',			'school_student_population_manage.php',				'Manage Schools',	'0',	'active',	'unhide'),
	array('Affiliations/ Accreditation',		'schools_affiliations_accreditation_manage.php',	'Manage Schools',	'0',	'active',	'unhide'),
	array('School Body',						'school_student_body_manage.php',					'Manage Schools',	'0',	'active',	'unhide'),
	array('Cultural Diversity',					'school_cultural_diversity_manage.php',				'Manage Schools',	'0',	'active',	'unhide'),
	array('Cultural Campus Life GPA',			'campus_life_gpa_manage.php',						'Manage Schools',	'0',	'active',	'unhide'),
	array('Cultural Campus Life Club',			'campus_life_club_manage.php',						'Manage Schools',	'0',	'active',	'unhide'),
	array('Degrees Offered',					'school_degrees_offered_manage.php',				'Manage Schools',	'0',	'active',	'unhide'),
	array('Membership type',					'membership_manage.php',					'Manage Schools',	'0',	'active',	'unhide'),

	array('Renew Membership',					'renew_membership_manage.php',					'Manage Schools',	'0',	'active',	'unhide'),

	array('Manage Newsletter',					'newsletters_manage.php',							'Newsletter',		'0',	'active',	'unhide'),

	array('Manage Users',						'user_manage.php',							'Users',		'0',	'active',	'unhide')



);

	/* Menu Access based on role*/
	/*
		Note : Role Name would come from {$CFG->prefix}user.role_name */

		/*$menu_access[] = array('normal','View Students');
		$menu_access[] = array('normal','View Parents');
		$menu_access[] = array('normal','View Education Professional');
		$menu_access[] = array('normal','View Schools/colleges/institutes'); 
		$menu_access[] = array('normal','View Counselor'); */
		/*$menu_access[] = array('normal','Page Name 6');
		$menu_access[] = array('normal','school_type_manage.php');
		$menu_access[] = array('normal','View Customer');
		$menu_access[] = array('normal','Ingredient Stock');
		$menu_access[] = array('normal','Packaging Stock');*/
	

?>