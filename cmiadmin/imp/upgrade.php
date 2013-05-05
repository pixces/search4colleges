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
						  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
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

		execute_sql("DROP TABLE IF EXISTS `{$CFG->prefix}admin_users`;");

		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}admin_users` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `first_name` varchar(100) NOT NULL,
					  `last_name` varchar(100) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `phone` varchar(20) NOT NULL,
					  `username` varchar(50) NOT NULL,
					  `password` varchar(50) NOT NULL,
					  `allowed_sections` varchar(50) NULL,
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created  `admin_users` datatype fields");
				
				$user_update = new object();
				$user_update->first_name	= 'admin';
				$user_update->username		= 'admin';
				$user_update->password		= md5('admin');
				$user_update->last_name		= '';
				$user_update->email		= '';
				$user_update->phone		= '';
				$user_update->allowed_sections	= '';
				$user_update->status		= 'active';
				$user_update->added_date		= time();
  				$user_update->last_modified_date = time();
				$insert = insert_record('admin_users',$user_update);
				
				if($insert){
					notify("Record inserted in admn_users table");
				}
				else{
					notify("Failed to insert record in admn_users table");
				}
		}
		else{
				notify("Failed Modified zip datatype fileds");
		}
		
	}
	/* 	Added By \O/ cmi \o/
		Reading sql files from data and executing them */
	if($current_version < 2010061705){
		$basedir = 'data';
		if ($handle = opendir($basedir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$myFile = $basedir.'/'.$file;
					$fh = file_get_contents($myFile);
					$fh = str_replace('$prefix',$CFG->prefix,$fh);
					if(execute_sql("$fh",false) or die(mysql_error())){
					
					}

				}
			}
			closedir($handle);
		}
	}

	if($current_version < 2010062102){
		execute_sql("RENAME TABLE {$CFG->prefix}reports to {$CFG->prefix}activities");
	}

	if($current_version < 2010062103){
		execute_sql("RENAME TABLE {$CFG->prefix}admin_users to {$CFG->prefix}users");
	}

	if($current_version < 2010062201){
		if(execute_sql("ALTER TABLE `{$CFG->prefix}users` ADD `role_name` VARCHAR( 255 ) NOT NULL COMMENT 'role name' AFTER `status`"))
		{
			notify("altered users table");
		}
	}
	
	if($current_version < 2010062202){
		if(execute_sql("UPDATE `{$CFG->prefix}users` SET `role_name` = 'admin' WHERE `id` =1"))
		{
			notify("altered users table , added role_name = admin");
		}

		$role = new object();
		$role->rolename ="admin";
		$role->added_date = time();
		insert_record('role',$role);

	}

    if($current_version < 2010062401){
		execute_sql("ALTER TABLE {$CFG->prefix}users CHANGE `allowed_sections` `allowed_sections` VARCHAR( 250 ) NULL"); 
    }

	if($current_version < 2010062402){
		execute_sql("ALTER TABLE {$CFG->prefix}product CHANGE `image` `image` TEXT NULL"); 
    }
	if($current_version < 2010062402){
		execute_sql("ALTER TABLE {$CFG->prefix}product ADD `seo_keyword` VARCHAR(255) NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}category ADD `seo_keyword` VARCHAR(255) NULL");  
		execute_sql("ALTER TABLE {$CFG->prefix}articles ADD `seo_keyword` VARCHAR(255) NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}category_articles ADD `seo_keyword` VARCHAR(255) NULL");
		execute_sql("ALTER TABLE {$CFG->prefix}events ADD `seo_keyword` VARCHAR(255) NULL");
	}
	
	if($current_version < 2010080401){
		if(execute_sql("ALTER TABLE `{$CFG->prefix}product` ADD `code` VARCHAR( 255 ) NOT NULL AFTER `category`"))
		{
			notify("added code in product table");
		}
		if(execute_sql("ALTER TABLE `{$CFG->prefix}product` ADD `price` VARCHAR( 255 ) NOT NULL AFTER `code`"))
		{
			notify("added price in product table");
		}
	}

    if($current_version < 2010080403){
		execute_sql("ALTER TABLE {$CFG->prefix}category CHANGE `description` `description` VARCHAR( 250 ) NULL"); 
		{
			notify("description null in product table");
		}
	}

	if($current_version < 2010080404){
		if(execute_sql("ALTER TABLE `{$CFG->prefix}product` ADD `featured` VARCHAR( 255 ) NULL AFTER `price`"))
		{
			notify("added featured in product table");
		}		
	}

    if($current_version < 2010080406){
		
		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}orders` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `customer_name` varchar(250) NOT NULL,
					  `order_status` varchar(250) NOT NULL,
					  `total` varchar(250) NULL,
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created orders table");
	    } 
		
		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}ordered_products` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `customer_name` varchar(250) NOT NULL,
					  `product_name` varchar(250) NOT NULL,
					  `quantity` varchar(250) NOT NULL,
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created ordered_products table");
	    } 
	}
	




if($current_version < 2010080407){
		
		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}distributor` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,					 
					  `name` varchar(250) NOT NULL,
					  `address` varchar(250) NOT NULL,
					  `city` varchar(250) NOT NULL,
					  `state` varchar(250) NOT NULL,
					  `zip` varchar(250) NOT NULL,
					  `phone` varchar(250) NOT NULL,		  
					  `fax` varchar(250) NULL,
					  `map` varchar(250) NULL,					  
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created distributor table");
	    } 
		
		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}retailer` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `name` varchar(250) NOT NULL,
					  `address` varchar(250) NOT NULL,
					  `city` varchar(250) NOT NULL,
					  `state` varchar(250) NOT NULL,
					  `zip` varchar(250) NOT NULL,
					  `phone` varchar(250) NOT NULL,
					  `fax` varchar(250) NOT NULL,
					  `map` varchar(250) NOT NULL,					  
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created ordered_products table");
	    } 
	}



if($current_version < 2010080502){

		if(execute_sql("ALTER TABLE {$CFG->prefix}ordered_products CHANGE `customer_name` `customer` VARCHAR( 250 ) NOT NULL")) 
		{
			notify("change customer_name to customer in ordered_products table");
		}
		if(execute_sql("ALTER TABLE {$CFG->prefix}ordered_products CHANGE `product_name` `product` VARCHAR( 250 ) NOT NULL")) 
		{
			notify("change product_name to product in ordered_products table");
		}
		if(execute_sql("ALTER TABLE {$CFG->prefix}ordered_products ADD `amount` VARCHAR( 255 ) NOT NULL AFTER `quantity`"))
		{
			notify("added amount in ordered_products table");
		}
	}


if($current_version < 2010080701){
		
		if(execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}discount` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,					 
					  `code` varchar(250) NOT NULL,
					  `from` varchar(250) NOT NULL,
					  `to` varchar(250) NOT NULL,
					  `type` varchar(250) NOT NULL,
					  `amount` varchar(250) NOT NULL,
					  `added_date` int(11) NOT NULL,
					  `last_modified_date` int(11) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))
		{
				notify("created discount table");
	    } 
}

if($current_version < 2010082701){

		if(execute_sql("ALTER TABLE {$CFG->prefix}discount CHANGE `from` `from_date` VARCHAR( 250 ) NOT NULL")) 
		{
			notify("from_date in discount table");
		}
		if(execute_sql("ALTER TABLE {$CFG->prefix}discount CHANGE `to` `to_date` VARCHAR( 250 ) NOT NULL")) 
		{
			notify("to_date in discount table");
		}
		if(execute_sql("ALTER TABLE {$CFG->prefix}discount CHANGE `type` `discount_type` VARCHAR( 255 ) NOT NULL"))
		{
			notify("discount_type in discount table");
		}
	}

	if($current_version < 2010090701){
		execute_sql("ALTER TABLE  {$CFG->prefix}discount CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}distributor CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}events CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}faq CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}gallery CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}links CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}ordered_products CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}orders CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}page_contents CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}retailer CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}role CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}section CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}services CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}status CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}status CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}sub_services CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}testimonial CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
		execute_sql("ALTER TABLE  {$CFG->prefix}users CHANGE  `status`  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active'");
	}
	
	if($current_version < 2010111201)
	{
		execute_sql("ALTER TABLE {$CFG->prefix}events CHANGE `pdf` `pdf` TEXT NULL"); 
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