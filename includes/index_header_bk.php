<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search4Colleges</title>
<link rel="shortcut icon" href="<?php echo $CFG->siteroot;?>/images/favicon.ico"/>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/mootools.js" ></script>
<script language="javascript" type="text/javascript" src="js/mootools_more.js" ></script>
<!--<script language="javascript" type="text/javascript" src="js/en.js" ></script>-->
<script language="javascript" type="text/javascript" src="js/formcheck.js" ></script>
<link rel="stylesheet" type="text/css" href="css/formcheck.css">
<link href="css/calendar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" >
	window.addEvent('domready', function(){
		new FormCheck('login_user',{display:{showErrors:1}});
		new FormCheck('form_school_colleges',{display:{showErrors:1},
            alerts : {
                select : 'Please choose a state'
            }});
	});	

</script>
<script type="text/javascript" >
function checkstate(el) {
		var stateid = $('state').value;
		if (el.value == '') 
			{
				el.errors.push("Please choose a state"); 
				return false;
			} else {
				return true;
			}

	}
</script>
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="css/ie7.css" />
<![endif]-->
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="css/ie6.css" />
<![endif]-->
</head>
<body>
<div id="main">
  <!-- header section >> -->
  <div id="header">
    <div class="logo"> <a href="index.html"><img src="images/logo.gif" alt="Logo" border="0" /></a> </div>
    <div class="top_nav_sec">
      <div class="top_right"> join us on <a href="https://twitter.com/#!/search4college1" target="_blank" title="Twitter"><img src="images/twitter.gif" alt="Twitter" /></a> <a href="http://www.linkedin.com/pub/alison-baker/46/262/3b3" target="_blank" title="Linkedin"><img src="images/link.png" alt="LinkedIn" target="_blank" /></a> <a href="http://www.facebook.com" target="_blank" title="facebook"><img src="images/facebook.gif" alt="Facebook" /></a> </div>
      <div class="top_nav">
        <ul>
          <li class="selected"><a href="<?php echo $CFG->siteroot;?>/index.html" title="Home">home</a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/about_us.html" title="About Search4colleges">About Search4colleges</a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/colleges.html" title="Colleges">Colleges</a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/blog.html" title="Blog">Blog </a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/counselor_search.html" title="Counselors">Counselors</a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/help.html" title="Help">Help</a></li>
          <li><a href="<?php echo $CFG->siteroot;?>/contact_us.html" title="Contact us">Contact us</a></li>
        </ul>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <!-- << header section -->
