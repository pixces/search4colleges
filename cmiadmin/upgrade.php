<?php 	
	if(!file_exists('config.php'))
	{
		header('Location:make_config.php');
	}

	require_once("config.php");
	require_once("version.php");
	require_once("upgrade_lib.php");

	$current_debug = $CFG->debug;
	$CFG->debug = '';
	
	print_header(get_string('cmi','cmi'));
		
	$CFG->cmi_version;
	$oldversion = 0;
	if(!isset($CFG->cmi_config))
	{
		$config_create = "
						CREATE TABLE IF NOT EXISTS `{$CFG->prefix}config` (
						  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(255) NOT NULL DEFAULT '',
						  `value` text NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `mdl_conf_nam_uix` (`name`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Moodle configuration variables' AUTO_INCREMENT=1;
					";

		execute_sql($config_create);
		$in->name = 'cmi_config';
		$in->value = 0;
		insert_record('config',$in);
	}

	//update the config values from upgrade_lib.php
	if(isset($CFG_UPGRADE))
	{
		foreach($CFG_UPGRADE as $key=>$upgrade)
		{
			$check = get_record('config','name',$key);

			//we only insert. Update should be done from config_manage.php
			if(!isset($check->id))
			{
				$cnf_update = new object();
				$cnf_update->name = $key;
				$cnf_update->value = $upgrade;
				insert_record('config',$cnf_update);
			}		
		}
	}

	if(isset($SECTION_ARRAY))
	{
		$section_drop = "DROP TABLE IF EXISTS `{$CFG->prefix}section`";
		execute_sql($section_drop,false);
		
		$section_create = "
						CREATE TABLE IF NOT EXISTS `{$CFG->prefix}section` (
						  `id` int(5) NOT NULL AUTO_INCREMENT,
						  `section_name` varchar(60) NOT NULL,
						  `link_url` varchar(50) NOT NULL,
						  `parent_id` int(11) NOT NULL DEFAULT '0',
						  `sort_order` int(11) NOT NULL DEFAULT '0',
						  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
						  `hide_menu` tinyint(1) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
					";
		execute_sql($section_create,false);

		foreach($SECTION_ARRAY as $key=>$upgrade)
		{
            if($upgrade[2] == '0')
			{
				$parent_id = '0';
			}
			else
			{
				$parent_id = get_field('section','id','section_name',$upgrade[2]);
			}

			if($upgrade[5] == 'hide')
				$hide_menu = '1';
			else
				$hide_menu = '0';
	
			$cnf_update = new object();	
			$cnf_update->section_name	= $upgrade[0];
			$cnf_update->link_url		= $upgrade[1];
			$cnf_update->parent_id		= $parent_id;
			$cnf_update->sort_order		= $upgrade[3];
			$cnf_update->status			= $upgrade[4];
			$cnf_update->hide_menu		= $hide_menu;
			insert_record('section',$cnf_update);

		}
	}
		
	$CFG = get_config();

	$current_version = $CFG->cmi_config;

	notify("Upgrading CMI Admin");

	/* 
		Added By SG
		Reading sql files from db and executing them */
	
	$basedir = 'db';
	if ($handle = opendir($basedir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$myFile = $basedir.'/'.$file;
				$fh = file_get_contents ($myFile);
				$fh = str_replace('$prefix',$CFG->prefix,$fh);
				
				if(execute_sql("$fh",false) or die(mysql_error())){
				
				}

			}
		}
		closedir($handle);
	}

	if($current_version < 2010040601){

		execute_sql("DROP TABLE IF EXISTS `{$CFG->prefix}users`;");

		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}users` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `first_name` varchar(100) NOT NULL,
					  `last_name` varchar(100) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `phone` varchar(20) NOT NULL,
					  `username` varchar(50) NOT NULL,
					  `password` varchar(50) NOT NULL,
					  `allowed_sections` VARCHAR( 250 ) NULL,
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
					  `role_name` VARCHAR( 255 ) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created  `users` datatype fields");
				
				$user_update = new object();
				$user_update->first_name	= 'admin';
				$user_update->username		= 'admin';
				$user_update->password		= md5('admin');
				$user_update->last_name		= '';
				$user_update->email		= '';
				$user_update->phone		= '';
				$user_update->allowed_sections	= '';
				$user_update->status		= 'active';
				$user_update->role_name		= 'admin';
				$user_update->added_date		= ".time().";
  				$user_update->last_modified_date = ".time().";
				$insert = insert_record('users',$user_update);
				
				if($insert){
					notify("Record inserted in users table");
				}
				else{
					notify("Failed to insert record in users table");
				}
		}
		else{
				notify("Failed Modified zip datatype fileds");
		}
		
	}	
		
	if($current_version < 2010062202){
		
		$role = new object();
		$role->rolename ="admin";
		$role->added_date = ".time().";
		insert_record('role',$role);

	}
	
	if($current_version < 2011062301){
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD code VARCHAR(255) NULL");
	}

	if($current_version < 2011062801){
		execute_sql("ALTER TABLE {$CFG->prefix}student	ADD	image text NULL");
	}

	if($current_version < 2011062802){
		execute_sql("ALTER TABLE {$CFG->prefix}academic	ADD	institute_name varchar(255) NOT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}academic	CHANGE	`user_id` `student_id` INT(11) NOT NULL");
	}


	if($current_version < 2011062901){
		execute_sql("ALTER TABLE {$CFG->prefix}news	ADD	seo_keyword varchar(255) NULL");
	}

	if($current_version < 2011063001){
		execute_sql("ALTER TABLE {$CFG->prefix}gallery CHANGE `image` `type` TEXT NOT NULL ");
		execute_sql("ALTER TABLE {$CFG->prefix}gallery ADD `approved` ENUM('approved','inprocess','declined') NOT NULL DEFAULT  'inprocess' ");
		execute_sql("ALTER TABLE {$CFG->prefix}gallery	ADD	`user_id` INT(11) NOT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}gallery	ADD	`comment` VARCHAR(255) NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}gallery DROP `seo_keyword` ");
		execute_sql("ALTER TABLE {$CFG->prefix}gallery DROP `sort_order` ");

	}
	if($current_version < 2011070201){		
		execute_sql("ALTER TABLE {$CFG->prefix}counselors	ADD	`about_me` TEXT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}parent	ADD	`about_me` TEXT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}schools	ADD	`about_me` TEXT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}teacher	ADD	`about_me` TEXT NULL");
	}
	if($current_version < 2011070601){		
		execute_sql("ALTER TABLE {$CFG->prefix}achievement	CHANGE	`caption` `title` VARCHAR( 255 ) NOT NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`organization` VARCHAR(255) NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`designation` VARCHAR(255) NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`work_from` VARCHAR(255) NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`work_till` VARCHAR(255) NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`work_nature` VARCHAR(255) NULL");		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users	ADD	`short_description` VARCHAR(255) NULL");
	}
	if($current_version < 2011071101){
		execute_sql("ALTER TABLE {$CFG->prefix}friends	ADD	`code` VARCHAR( 255 ) NOT NULL AFTER `invitaion_status` ");
	}

	if($current_version < 2011071201){
		execute_sql("ALTER TABLE {$CFG->prefix}student	ADD	`user_id` int(11)  NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}counselors	ADD	`user_id` int(11)  NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}parent	ADD	`user_id` int(11)  NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}schools	ADD	`user_id` int(11)  NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}teacher	ADD	`user_id` int(11)  NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users DROP `user_id` ");
	}

	if($current_version < 2011071501){
		execute_sql("TRUNCATE TABLE {$CFG->prefix}fe_users");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}student");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}teacher");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}parent");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}schools");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}counselors");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}academic");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}achievement");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}gallery");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}messages");
	}

	if($current_version < 2011071801){
		execute_sql("ALTER TABLE {$CFG->prefix}event ADD event_type VARCHAR( 50 ) NOT NULL AFTER closing_date ,
						ADD priority VARCHAR( 100 ) NOT NULL AFTER event_type");
	}

	if($current_version < 2011071802){
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `read` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER message");
	}
	if($current_version < 2011071901){
		execute_sql("ALTER TABLE {$CFG->prefix}messages CHANGE `sender_id` `receiver_id` INT( 11 ) NOT NULL");
	}
	if($current_version < 2011071902){
		execute_sql("ALTER TABLE {$CFG->prefix}messages CHANGE `user_id`  `sender_id` INT( 11 ) NOT NULL");
	}
	
	if($current_version < 2011071903){
		execute_sql("ALTER TABLE {$CFG->prefix}messages CHANGE `read` `isread` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no'");
	}
	if($current_version < 2011072001){
		execute_sql("ALTER TABLE {$CFG->prefix}schools ADD `featured` ENUM('featured','no') NOT NULL DEFAULT  'no'");
	}
	if($current_version < 2011072002){
		execute_sql("ALTER TABLE {$CFG->prefix}schools ADD `image` VARCHAR( 255 ) NOT NULL AFTER `featured`");
	}

	if($current_version < 2011072101){
		execute_sql("ALTER TABLE {$CFG->prefix}messages CHANGE `isread` `isread_sender` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no'");
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `isread_reciever` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER `isread_sender`");
	}
	if($current_version < 2011072102){
		execute_sql("ALTER TABLE {$CFG->prefix}news ADD `parent_id` int(11 ) NOT NULL ");
	}

	if($current_version < 2011072103){
		execute_sql("ALTER TABLE {$CFG->prefix}messages CHANGE `isread_reciever` `isread_receiver` ENUM( 'yes', 'no' )  NOT NULL DEFAULT 'no'");
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `isdelete_sender` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER `isread_receiver` ");
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `isdelete_receiver` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER `isdelete_sender`");
	}
	
	if($current_version < 2011072104){
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `parent_id` int(11 ) NOT NULL ");
	}
	if($current_version < 2011072201){
		execute_sql("ALTER TABLE {$CFG->prefix}blog ADD `parent_id` int(11 ) NOT NULL ");
	}
	


	if($current_version < 2011072501){
		execute_sql("ALTER TABLE `schools` ADD `web_url` VARCHAR( 250 ) NOT NULL AFTER `about_me`");
	}

	if($current_version < 2011072601){
		execute_sql("ALTER TABLE `schools` DROP `school_website` ,
							DROP `first_name` ,
							DROP `last_name` ,
							DROP `gender` ,
							DROP `designation` ,
							DROP `department` ,
							DROP `department_head` ,
							DROP `department_emailid` ");
	}
	
	
	if($current_version < 2011072603){
		execute_sql("TRUNCATE TABLE {$CFG->prefix}blog");
		execute_sql("ALTER TABLE {$CFG->prefix}blog CHANGE `topic` `category_id` INT(11) NOT NULL  ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog CHANGE `short_description` `user_id` INT(11) NOT NULL  ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog CHANGE `date` `title` TEXT NOT NULL  ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog CHANGE `sort_order` `description` TEXT NOT NULL  ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog   DROP `parent_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog ADD `story`  TEXT NULL AFTER `description` ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog ADD `isapproved` int(1) NOT NULL DEFAULT 0  AFTER `story` ");
		execute_sql("ALTER TABLE {$CFG->prefix}blog_comment ADD `isapproved` int(1) NOT NULL DEFAULT 0  AFTER `comment` ");
	}
	if($current_version < 2011072801){
			execute_sql("ALTER TABLE {$CFG->prefix}schools_additional ADD `department` VARCHAR( 100 ) NULL AFTER `only_for_local` ,
				ADD `department_head` VARCHAR( 100 ) NULL AFTER `department` ,
				ADD `department_email` VARCHAR( 100 ) NULL AFTER `department_head`");
	}

	if($current_version < 2011072802){
		execute_sql("TRUNCATE TABLE {$CFG->prefix}article_category");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Planning timelines', 'planning_timelines', '1', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Build your credentials', 'build_credentials', '2', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Admission essays', 'admission_essays', '3', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'College selection', 'college_selection', '4', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Common topics', 'common_topics', '5', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Career guidance for their children', 'career_guidance', '6', '1310047250', 'active');");
		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`name` ,`seo_keyword` ,`sort_order` ,`added_date` ,`status`)VALUES (NULL , 'Examination information', 'examination_information', '7', '1310047250', 'active');");
		
		execute_sql("TRUNCATE TABLE {$CFG->prefix}articles");		
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `seo_keyword` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `sort_order` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `article_date` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `parent_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `user_id` int(11) NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `category_id` INT(11) NOT NULL AFTER `user_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `isapproved` int(1) NOT NULL DEFAULT 0  AFTER `long_description` ");
		execute_sql("DROP TABLE {$CFG->prefix}article");
	}


	if($current_version < 2011072803){		
		execute_sql("ALTER TABLE {$CFG->prefix}article_category   ADD `parent_id` INT(11) NOT NULL AFTER `id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}article_category ADD `article_date` int(11) NOT NULL AFTER `sort_order` ");
		execute_sql("TRUNCATE TABLE {$CFG->prefix}article_category");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Planning timelines', 'planning_timelines', '1', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Build your credentials', 'build_credentials', '2', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Admission essays', 'admission_essays', '3', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'College selection', 'college_selection', '4', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Common topics', 'common_topics', '5', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Career guidance for their children', 'career_guidance', '6', '1310047250', '1310047250', 'active');");

		execute_sql("INSERT INTO {$CFG->prefix}article_category(`id` ,`parent_id`,`name` ,`seo_keyword` ,`sort_order` ,`article_date`, `added_date` ,`status`)VALUES (NULL , '0', 'Examination information', 'examination_information', '7', '1310047250', '1310047250', 'active');");

		execute_sql("ALTER TABLE {$CFG->prefix}article_category   DROP `article_date` ");
		
		execute_sql("TRUNCATE TABLE {$CFG->prefix}articles");		
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `user_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `article_date` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles   DROP `isapproved` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `article_date` int(11) NOT NULL AFTER `long_description` ");
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `seo_keyword` varchar(255) NOT NULL AFTER `name` ");
	}

	
	if($current_version < 2011072804)
	{
		$category = array();
		$category[1] = "Agricultural Sciences";
		$category[2] = "Arts & Architecture";
		$category[3] = "Biology & Sciences";
		$category[4] = "Business";
		$category[5] = "Communications";
		$category[6] = "Computers and Information";
		$category[7] = "Consumer Sciences";
		$category[8] = "Criminal Justice & Law";
		$category[9] = "Education";
		$category[10] = "Engineering";
		$category[11] = "English ";
		$category[12] = "Environment";
		$category[13] = "Health Sciences";
		$category[14] = "History ";
		$category[15] = "Humanities";
		$category[16] = "Language Studies";
		$category[17] = "Mathematics";
		$category[18] = "Military";
		$category[19] = "Physical Ed. & Recreation";
		$category[20] = "Pre-professional";
		$category[21] = "Social Sciences";
		$category[22] = "Theology";

		$subcategory[1][1] = "Agribusiness";
		$subcategory[1][2] = "Agriculture";
		$subcategory[1][3] = "Agronomy";
		$subcategory[1][4] = "Animal Sciences";
		$subcategory[1][5] = "Horticulture";

		$subcategory[2][1] = "Architecture";
		$subcategory[2][2] = "Art";
		$subcategory[2][3] = "Film";
		$subcategory[2][4] = "Graphic Design";
		$subcategory[2][5] = "Interior Design";
		$subcategory[2][6] = "Music";

		$subcategory[3][1] = "Biology";
		$subcategory[3][2] = "Chemistry";
		$subcategory[3][3] = "Marine Biology";
		$subcategory[3][4] = "Physics";
		$subcategory[3][5] = "Zoology";

		$subcategory[4][1] = "Accounting";
		$subcategory[4][2] = "Business";
		$subcategory[4][3] = "Finance";
		$subcategory[4][4] = "Marketing";

		$subcategory[5][1] = "Communications";
		$subcategory[5][2] = "Journalism";
		$subcategory[5][3] = "Public Relations";
		$subcategory[5][4] = "Radio and Television";

		$subcategory[6][1] = "Artificial Intelligence";
		$subcategory[6][2] = "Computer Science";
		$subcategory[6][3] = "Information Technology";
		$subcategory[6][4] = "Library Science";

		$subcategory[7][1] = "Cosmetology";
		$subcategory[7][2] = "Food Science";
		$subcategory[7][3] = "Nutrition";
		$subcategory[7][4] = "Restaurant Management";

		$subcategory[8][1] = "Criminal Justice";
		$subcategory[8][2] = "Pre-law";
		$subcategory[8][3] = "Public Affairs";
		$subcategory[8][4] = "Social Work";
		
		$subcategory[9][1] = "Childhood Education";
		$subcategory[9][2] = "Education";
		$subcategory[9][3] = "Music Education";
		$subcategory[9][4] = "Special Education";

		$subcategory[10][1] = "Chemical Engineering";
		$subcategory[10][2] = "Electrical Engineering";
		$subcategory[10][3] = "Engineering";
		$subcategory[10][4] = "Industrial Engineering";
		$subcategory[10][5] = "Mechanical Engineering";		
		
		$subcategory[11][1] = "American Literature";
		$subcategory[11][2] = "Creative Writing";
		$subcategory[11][3] = "English";
		$subcategory[11][4] = "Speech and Rhetoric";
		
		$subcategory[12][1] = "Conservation";
		$subcategory[12][2] = "Environmental Science";
		$subcategory[12][3] = "Fisheries Science and Management";
		$subcategory[12][4] = "Forestry";

		$subcategory[13][1] = "Kinesiology";
		$subcategory[13][2] = "Nursing";
		$subcategory[13][3] = "Occupational Therapy";
		$subcategory[13][4] = "Pharmacy";
		$subcategory[13][5] = "Physical Therapy";

		$subcategory[14][1] = "American History";
		$subcategory[14][2] = "Asian History";
		$subcategory[14][3] = "European History";
		$subcategory[14][4] = "History";

		$subcategory[15][1] = "African-American Studies";
		$subcategory[15][2] = "Classical Studies";
		$subcategory[15][3] = "Philosophy";
		$subcategory[15][4] = "Women's Studies";
		
		$subcategory[16][1] = "French";
		$subcategory[16][2] = "German";
		$subcategory[16][3] = "Japanese";
		$subcategory[16][4] = "Portuguese";
		$subcategory[16][5] = "Spanish";

		$subcategory[17][1] = "Computational Mathematics";
		$subcategory[17][2] = "Mathematics";
		$subcategory[17][3] = "Probability";
		$subcategory[17][4] = "Statistics";

		$subcategory[18][1] = "Air Force ROTC";
		$subcategory[18][2] = "Army ROTC";
		$subcategory[18][3] = "Marine ROTC";
		$subcategory[18][4] = "Naval Studies";

		$subcategory[19][1] = "Exercise and Sports Science";
		$subcategory[19][2] = "Recreation and Parks Management";
		$subcategory[19][3] = "Sports Management";
		$subcategory[19][4] = "Travel and Tourism";

		$subcategory[20][1] = "Aviation";
		$subcategory[20][2] = "Construction Trades";
		$subcategory[20][3] = "Culinary";
		$subcategory[20][4] = "Industrial Arts";
		$subcategory[20][5] = "Security and Protective Services";
		
		$subcategory[21][1] = "Anthropology";
		$subcategory[21][2] = "Economics";
		$subcategory[21][3] = "International Studies";
		$subcategory[21][4] = "Political Science";
		$subcategory[21][5] = "Psychology";
		$subcategory[21][6] = "Sociology";
		
		$subcategory[22][1] = "Buddhist Studies";
		$subcategory[22][2] = "Christian Studies";
		$subcategory[22][3] = "Islamic Studies";
		$subcategory[22][4] = "Judaic Studies";
		$subcategory[22][5] = "Theology";	


		foreach($category as $key=>$value)
		{
			$majors = new object();
			$majors->name = $value;
			$majors->parent_id = 0;
			$majors->sort_order = 0;
			$majors->added_date = ".time().";
			$new = insert_record('majors',$majors);
			
			foreach($subcategory[$key] as $subcat)
			{
				$sub = new object();
				$sub->name = $subcat;
				$sub->parent_id = $new;
				$sub->sort_order = 0;
				$sub->added_date = ".time().";
				$insert = insert_record('majors',$sub);
			}
		}
		
		if($insert)
		{
			notify("Records inserted in majors table");
		}
		else
		{
			notify("Failed to insert records in majors table");
		}				
	
	}
	if($current_version < 2011072901){		
		execute_sql("ALTER TABLE {$CFG->prefix}fe_users   ADD `image` text  NULL AFTER `user_type` ");
		execute_sql("ALTER TABLE {$CFG->prefix}student   DROP `image` ");
	}

	if($current_version < 2011080301)
	{
		execute_sql("ALTER TABLE {$CFG->prefix}school_degrees_offered CHANGE `degree` `degree` VARCHAR( 255 ) NOT NULL ");
	}

	if($current_version < 2011080401){
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `is_query` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER `isdelete_receiver` ");		
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `topic` text NULL AFTER `isdelete_receiver` ");
	}

	if($current_version < 2011080501){
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `reply` text NULL AFTER `topic` ");		
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `reply_date` text NULL AFTER `reply` ");
	}

	if($current_version < 2011080601){
		execute_sql("ALTER TABLE {$CFG->prefix}school_exam CHANGE `result_status` `result_status` VARCHAR( 250 ) NOT NULL ");		
	}

	if($current_version < 2011080602){
		execute_sql("ALTER TABLE {$CFG->prefix}school_exam ADD `degree_type` VARCHAR( 100 ) NULL AFTER `degree_id`");		
	}

	if($current_version < 2011080801){		
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `isread_reply` ENUM('yes','no') NOT NULL DEFAULT  'no' AFTER `is_query` ");
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `subject` VARCHAR( 255 ) NULL AFTER `receiver_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}messages ADD `parent_id` INT(15) NULL AFTER `receiver_id` ");
		execute_sql("ALTER TABLE {$CFG->prefix}messages 
						DROP `isread_sender`, 
						DROP `topic`, 
						DROP `reply`, 
						DROP `reply_date`, 
						DROP `isread_reply`, 
						DROP `status`
					");			
		execute_sql("ALTER TABLE {$CFG->prefix}`messages` CHANGE `parent_id` `parent_id` INT( 15 ) NOT NULL DEFAULT '0'");
		execute_sql("ALTER TABLE {$CFG->prefix}`messages` ADD `is_replied` BOOLEAN NOT NULL DEFAULT '0' AFTER `is_query`");
		execute_sql("ALTER TABLE {$CFG->prefix}`messages` ADD `is_child` BOOLEAN NOT NULL DEFAULT '0' AFTER `is_replied`");
	}

	if($current_version < 2011080802){		
		execute_sql("ALTER TABLE {$CFG->prefix}`school_staff_user` ADD `name` VARCHAR( 200 ) NOT NULL AFTER `fe_school_id`  ");
	}

	if($current_version < 2011080901){		
		execute_sql("DROP TABLE {$CFG->prefix}`school_news_events`");
	}

	if($current_version < 2011080902){		
		execute_sql("ALTER TABLE {$CFG->prefix}`schools_news` CHANGE `details` `details` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ");
	}
	
	if($current_version < 2011081101){		

		execute_sql("ALTER TABLE `student` CHANGE `goals_in_life` `goals_in_life` TEXT  NULL");
		execute_sql("ALTER TABLE `student` CHANGE `short_description` `short_description` TEXT  NULL");
		execute_sql("ALTER TABLE `student` CHANGE `long_description` `long_description` TEXT  NULL");
		execute_sql("ALTER TABLE `school_admission_details` CHANGE `tuition fees` `tuition_fees` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ");
	}
	

	if($current_version < 2011081102){		
		execute_sql("ALTER TABLE `student` CHANGE `goals_in_life` `goals_in_life` TEXT  NULL");
		execute_sql("ALTER TABLE `student` CHANGE `short_description` `short_description` TEXT  NULL");
		execute_sql("ALTER TABLE `student` CHANGE `long_description` `long_description` TEXT  NULL");
		execute_sql("ALTER TABLE `school_admission_details` CHANGE `tuition fees` `tuition_fees` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ");
		execute_sql("ALTER TABLE {$CFG->prefix}`schools_additional` ADD `school_type` VARCHAR( 250 ) NOT NULL DEFAULT '' AFTER `school_id`");
	}
	if($current_version < 2011081201){
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Alabama','Alabama',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Alaska','Alaska',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Arizona','Arizona',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Arkansas','Arkansas',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'California','California',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Colorado','Colorado',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Connecticut','Connecticut',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Delaware','Delaware',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'District_of_Columbia','District of Columbia',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Florida','Florida',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Georgia','Georgia',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Hawaii','Hawaii',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Idaho','Idaho',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Illinois','Illinois',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Indiana','Indiana',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Iowa','Iowa',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Kansas','Kansas',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Kentucky','Kentucky',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Louisiana','Louisiana',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Maine','Maine',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Maryland','Maryland',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Massachusetts','Massachusetts',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Michigan','Michigan',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Minnesota','Minnesota',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Mississippi','Mississippi',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Missouri','Missouri',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Montana','Montana',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Nebraska','Nebraska',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Nevada','Nevada',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'New_Hampshire','New Hampshire',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'New_Jersey','New Jersey',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'New_Mexico','New Mexico',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'New_York','New York',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'North_Carolina','North Carolina',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'North_Dakota','North Dakota',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Ohio','Ohio',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Oklahoma','Oklahoma',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Oregon','Oregon',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Pennsylvania','Pennsylvania',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Rhode_Island','Rhode Island',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'South_Carolina','South Carolina',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'South_Dakota','South Dakota',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Tennessee','Tennessee',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Texas','Texas',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Utah','Utah',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Vermont','Vermont',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Virginia','Virginia',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Washington','Washington',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'West_Virginia','West Virginia',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Wisconsin','Wisconsin',".time().",'active')");
		execute_sql("INSERT INTO `state` (`id` ,`country_id` ,`code` ,`name` ,`added_date` ,`status`) VALUES (NULL , '1', 'Wyoming','Wyoming',".time().",'active')");
	}
	if($current_version < 2011081601){

		execute_sql("ALTER TABLE `{$CFG->prefix}schools` CHANGE `recommended` `recommended` ENUM( 'null', 'recommend' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL");
	}
	if($current_version < 2011081602)
	{
		execute_sql('ALTER TABLE `{$CFG->prefix}save_search` CHANGE `query` `query` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL'); 
	}	
	if($current_version < 2011081801)
	{
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `campus_area` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `state` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `population` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `college_type` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `affiliation` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `degree` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `majors` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `mfratio` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `culture` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `locals` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `sports` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `clubs` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `greek` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `dorm` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `selectivity` VARCHAR(250) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}save_search` ADD `gpa` VARCHAR(250) NOT NULL DEFAULT ''");		
	}

	if($current_version < 2011082501)
	{
		execute_sql("ALTER TABLE `{$CFG->prefix}event` ADD `added_by` INT( 11 ) NOT NULL AFTER `added_date` ");
	}

	if($current_version < 2011092201)
	{
		execute_sql("ALTER TABLE `{$CFG->prefix}school_staff_user` CHANGE `allowed_sections` `allowed_sections` VARCHAR( 1000 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
	}
	 
	if($current_version < 2011092801)
	{
		execute_sql("ALTER TABLE `{$CFG->prefix}news_letters` CHANGE `title` `subject` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ");
	}

	if($current_version < 2011092802)
	{
		execute_sql("ALTER TABLE `{$CFG->prefix}fe_users` ADD `newsletter` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes';");
	}

	if($current_version < 2011092803)
	{
		execute_sql("INSERT INTO `{$CFG->prefix}members_type` (`id`, `name`) VALUES(1, 'Student'),(2, 'Parent'),(3, 'Counsellor'),(4, 'Teacher'),(5, 'Staff'),(6, 'School');");
	}

    if($current_version < 2011101001){
		execute_sql("ALTER TABLE {$CFG->prefix}schools_additional ADD affiliations_id INT(11) NOT NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}schools_additional ADD institution_type INT(11) NOT NULL");
	}

	if($current_version < 2011101002){
		execute_sql("ALTER TABLE {$CFG->prefix}school_membership ADD `discount` VARCHAR( 255 ) NOT NULL AFTER `renewedon`");
	}

	if($current_version < 2011101501){
		execute_sql("ALTER TABLE {$CFG->prefix}school_membership ADD `history_count` INT( 11 ) NOT NULL AFTER `discount`");
	}

	if($current_version < 2011101901){
		execute_sql("ALTER TABLE  {$CFG->prefix}schools` ADD  `display` INT(11) NOT NULL AFTER  `featured`;");
	}
	if($current_version < 2011110101){
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` CHANGE `name` `title` VARCHAR( 800 ) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `Logo` VARCHAR(800)  NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `email_id` VARCHAR(800) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `short_description` VARCHAR(800) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `contact_no` VARCHAR(800) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `contact_person` VARCHAR(800) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `available_time_from` VARCHAR(500) NOT NULL DEFAULT ''");
		execute_sql("ALTER TABLE `{$CFG->prefix}financial_provider` ADD  `available_time_to` VARCHAR(500) NOT NULL DEFAULT ''");
	}

	if($current_version < 2011110102){
		execute_sql("ALTER TABLE  `{$CFG->prefix}school_contacts` ADD  `contact_email` VARCHAR( 255 ) NOT NULL AFTER  `contact_person` ,
				ADD  `contact_post` VARCHAR( 255 ) NOT NULL AFTER  `contact_email` ;");
	}

	if($current_version < 2011110501){
		execute_sql("ALTER TABLE  `{$CFG->prefix}school_membership` ADD  `total_amount` VARCHAR( 255 ) NOT NULL AFTER  `discount` ;");
	}

	if($current_version < 2011110801){
		execute_sql("ALTER TABLE  `{$CFG->prefix}financial_provider` ADD  `address` TEXT NOT NULL AFTER  `contact_person` ;");
	}

	if($current_version < 2011110901){
		execute_sql("ALTER TABLE  `{$CFG->prefix}school_affiliation_with_banks` CHANGE  `type`  `affilation_id` INT( 11 ) NOT NULL;");
	}

	if($current_version < 2011110902){
		execute_sql("ALTER TABLE  `{$CFG->prefix}school_affiliation_with_banks` ADD  `user_id` INT( 11 ) NOT NULL AFTER  `affilation_id` ;");
	}

	if($current_version < 2011111001){
		execute_sql("ALTER TABLE  `{$CFG->prefix}schools` ADD  `seo_keyword` VARCHAR( 255 ) NOT NULL AFTER  `school_name`;");
	}
	
	
	if($current_version < 2011112301){
		execute_sql("ALTER TABLE `{$CFG->prefix}schools` CHANGE `phone` `phone` VARCHAR( 50 ) NOT NULL ;");
	}
	
	if($current_version < 2011112401){
		execute_sql("ALTER TABLE `schools_additional` CHANGE `institution_type` `institution_type` VARCHAR( 255 ) NOT NULL ;");
	}

	if($current_version < 2011112402){
		execute_sql("ALTER TABLE `{$CFG->prefix}schools_additional` CHANGE `institution_type` `institution_type` VARCHAR( 255 ) NOT NULL ;");
	}
	if($current_version < 2011122101){
		execute_sql("ALTER TABLE  `{$CFG->prefix}fe_users` ADD  `till_now` VARCHAR( 255 ) NOT NULL AFTER  `work_till`;");
	}
	if($current_version < 2011122601){
		execute_sql("ALTER TABLE `schools_enquiry` CHANGE `phone` `phone` VARCHAR( 255 ) NOT NULL ;");
	}
	if($current_version < 2012011001){
		execute_sql("ALTER TABLE  `{$CFG->prefix}save_search` ADD  `keyword` VARCHAR( 255 ) NOT NULL AFTER  `query`;");
		execute_sql("ALTER TABLE  `{$CFG->prefix}save_search` ADD  `zipcode` VARCHAR( 255 ) NOT NULL AFTER  `state`;");
	}
	if($current_version < 2012020102){
		execute_sql("ALTER TABLE  `{$CFG->prefix}financial_provider` ADD  `company_name` VARCHAR( 255 ) NOT NULL AFTER  `title`;");
		execute_sql("ALTER TABLE  `{$CFG->prefix}financial_provider` ADD  `zipcode` VARCHAR( 255 ) NOT NULL AFTER  `address`;");
		execute_sql("ALTER TABLE  `{$CFG->prefix}financial_provider` ADD  `city` INT( 11 ) NOT NULL AFTER  `zipcode`;");
		execute_sql("ALTER TABLE  `{$CFG->prefix}financial_provider` ADD  `state` INT( 11 ) NOT NULL AFTER  `city`;");
	}
	if($current_version < 2012020301){
		execute_sql("ALTER TABLE  `{$CFG->prefix}school_scholarship` ADD  `link` VARCHAR( 500 ) NOT NULL AFTER  `amount`;");
	}
	if($current_version < 2012021001){
		execute_sql("ALTER TABLE  `{$CFG->prefix}schools_enquiry` ADD  `interest` VARCHAR( 500 ) NOT NULL AFTER  `phone`;");
		execute_sql("ALTER TABLE  `{$CFG->prefix}schools_enquiry` ADD  `time_contact` VARCHAR( 500 ) NOT NULL AFTER  `interest`;");
	}
	if($current_version < 2012022102){
		execute_sql("ALTER TABLE  `{$CFG->prefix}schools_news` ADD  `filename` VARCHAR( 800 ) NOT NULL AFTER  `image`;");
		notify("CMI file field added ");
	}
	if($current_version < 2012031301){
		execute_sql("ALTER TABLE `{$CFG->prefix}articles` ADD `user_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `added_date` ,
ADD `approved` INT( 11 ) NOT NULL DEFAULT '0' AFTER `user_id`;");
		notify("articles table field user_id added ");
		notify("articles table field approved added ");
	}
	

	insert_seo();
	insert_page_content();
	update_lib_role();
	
	set_field('config','value',$CFG->cmi_version,'name','cmi_config');
	notify("CMI Admin Upgradation Complete");

	print_continue('index.php');
	print_footer();
	$CFG->debug = $current_debug;
?>