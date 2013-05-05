<?php 
	require_once('cmiadmin/config.php'); 
	$m_type = 'Page';
	$page_name =  str_replace(array(str_replace('cmiadmin','',$CFG->dirroot),'.html'),'',($_SERVER['SCRIPT_FILENAME']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search4Colleges</title>
<link rel="shortcut icon" href="<?php echo $CFG->siteroot;?>/images/favicon.ico"/>
<link href="<?php echo $CFG->siteroot;?>/css/styles.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $CFG->siteroot;?>/css/tipz.css" rel="stylesheet" type="text/css" />

<?php
$mootools_array		= array('student_form', 'parent_form', 'school_form', 'teacher_professor_form', 'counselor_form', 'contact_us', 	
						   'personal_details', 'pictures_and_videos','photo_uploaded', 'video_uploaded', 'about_us',
						   'achievements_awards', 'professional_experience','change_password','friends','blog','counseling','help', 
						   'inbox_messages','message','sent_messages','new_messages','new_messagesp','about_me','forgot_password','friend_request','my_account',
						   'searched_history','blog_topics_&_comments', 'blog_topics_&_comments','colleges', 'colleges_profile', 'colleges_degrees', 'colleges_culture_campus_life', 'colleges_gallery', 'colleges_scholarships', 'colleges_admissions', 'colleges_contact_information', 'colleges_send_enquiry', 'colleges_download_brochures', 'colleges_related_colleges','quick_search_results','articles_details','articles','blog_details','general','college_news_details','counselors_articles','events',
						   'searched_history','counselor_search','ask_question','counselor_query','exam_edit','school_additional_details','scholarships_edit','college_users','college_statistics','newsdetails','ask_fp_question'
						 );

$date_picker_array	= array('personal_details','achievements_awards', 'professional_experience','colleges_admissions','exam_edit','school_additional_details','college_users','college_news_details','counselors_articles','newsdetails','events','counselors_articles');
$media_box_array	= array('personal_details','pictures_and_videos', 'photo_uploaded','video_uploaded','achievements_awards','friends','college_news','brochures_details','user_search', 							                                                                                                 'inbox_messages','sent_messages','my_account','about_me','professional_experience','searched_history',
							'blog_topics_&_comments','change_password','inbox_messages','sent_messages','message','new_messages','new_messagesp','about_me',


							'forgot_password','friend_request','colleges_gallery','colleges_admissions','colleges_download_brochures',	'colleges_related_colleges','quick_search_results','articles_details','articles','blog_details',							'counselor_search','counselor_query','exam_edit','school_additional_details','scholarships_edit','colleges_scholarships','college_users','college_news_details','counselors_articles','college_statistics','colleges','advanced_search','newsdetails','colleges_degrees','my_calendar','financial_provider_search','professionals_search','financial_provider','parent','student','teacher','counselors'

						 );
$form_check_array	= array('student_form', 'parent_form', 'school_form', 'teacher_professor_form', 'counselor_form', 'contact_us', 	
						   'personal_details', 'photo_uploaded', 'video_uploaded', 'about_us', 'achievements_awards',
						   'professional_experience','change_password','blog','counseling','help','new_messages','new_messagesp','about_me',
						   'forgot_password','friends','colleges', 'colleges_profile', 'colleges_degrees', 'colleges_culture_campus_life', 'colleges_gallery', 'colleges_scholarships', 'colleges_admissions', 'colleges_contact_information', 'colleges_send_enquiry', 'colleges_download_brochures', 'colleges_related_colleges','quick_search_results','articles_details','articles','blog_details','general','college_statistics','my_calendar','events','membership_buy',
						   'searched_history','counselor_search','ask_question','exam_edit','school_additional_details','scholarships_edit','college_users','college_users','college_news_details','counselors_articles','newsdetails','ask_question'
						 );

$sexy_alert_array	= array('student_form', 'parent_form', 'school_form', 'teacher_professor_form', 'counselor_form','change_password'
						  ,'friends','inbox_messages','message','sent_messages','general','counselor_search','counselor_query','scholarships_edit','college_news_details','counselors_articles','college_news','college_statistics','searched_history','newsdetails','college_users','college_affiliated_banks'
						 );
$ckeditor_array		= array('about_me', 'colleges_profile','colleges_degrees','scholarships_edit','college_users','college_news_details','counselors_articles','college_statistics','college_news','newsdetails'
						 );
						 
$birth_date_array		= array('student_form', 'parent_form', 'teacher_professor_form', 'counselor_form' ,'college_statistics','newsdetails','counselors_articles',
						 );
?>


<?php //if(in_array($page_name,$mootools_array)) { ?>	
<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/mootools.js" ></script>
<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/mootools_more.js" ></script>
<?php //} ?>
<?php if($page_name=='advanced_search') { ?>
	<link href="<?php echo $CFG->siteroot;?>/nested/styles.css" rel="stylesheet" type="text/css" /> 
	<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/nested/functions.js" ></script>
<?php } ?>
<?php if(in_array($page_name,$date_picker_array)) { ?>
<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/datepicker.js" ></script>
<link href="<?php echo $CFG->siteroot;?>/css/datepicker_vista.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" >
	window.addEvent('domready', function(){
		new DatePicker('.demo_vista1', { 
			pickerClass: 'datepicker_vista',
			allowEmpty: true ,
			format: 'F d, Y',
		});
	});	
</script>
<?php } ?>

<?php if(in_array($page_name,$birth_date_array)) { ?>
<script  type="text/javascript">

	var month_sel	= false;
	var year_sel	= false;
	
	function month_change(month){
		if(month != 0){
			month_sel	= true;
			check_day();
		}else{
			month_sel	= false;
			check_day();
		}
	}
	function year_change(year){
		if(year != 0){
			year_sel	= true;
			check_day();
		}else{
			year_sel	= false;
			check_day();
		}
	}
	function check_day(){
		if(year_sel == true && month_sel == true){
			//document.s4c_reg.date_day.disabled = false;
			show_day();
		}else{
			//document.s4c_reg.date_day.disabled = true;
		}
	}

	function show_day(){
			
		var req = new Request({
			 method: 'post',
			 url: 'ajax_handler.php',
			 data: {'month' : $('date_month').value,	
					'year' : $('date_year').value,	
					'flag' : 'show_day'	
				 },
			 onRequest: function() {},
			 onComplete: function(response) { 
				 $('date_day').innerHTML = response;
				 //$('personal_msg').innerHTML = '';
			}
		}).send();	
	}
	

</script>
<?php } ?>


<?php if(in_array($page_name,$sexy_alert_array)) { ?>
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/sexyalert.js"></script>
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/general.js"></script>
<link href="<?php echo $CFG->siteroot;?>/css/sexyalert.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" >
	var email_flag=false;
	function customEmail(el){
		var movealert = new SexyAlertBox();
		if($('email').value != ''){
			var req = new Request({
				 method: 'post',
				 url: 'ajax_handler.php',
				 data: { 'email' : $('email').value,
						 'flag' : 'check_email'
					 },
				 onRequest: function() { },
				 onComplete: function(response) { 
					if(response=='yes')
					 {
						$('email').value = "";
						movealert.error('<h1>Sorry! Email ID is already registerd.</h1>');
					 }
					 else
					 {
						email_flag = true;
					 }
				}
			}).send();
		}
	}

	function customEmailconfirm(el){
		var movealert = new SexyAlertBox();
		if($('confirm_email').value != ''){
			var req = new Request({
				 method: 'post',
				 url: 'ajax_handler.php',
				 data: { 'email' : $('email').value,
						'confirm_email' : $('confirm_email').value,
						 'flag' : 'check_email_confirm'
					 },
				 onRequest: function() { },
				 onComplete: function(response) { 
					if(response=='yes')
					 {
						$('confirm_email').value = "";
						movealert.error('<h1>Confirm email field is different from Email</h1>');
					 }
				}
			}).send();
		}
	}
	function get_city(id,flag){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'ajax_handler.php',
				 data: { 'id' : id,'flag' : 'city_from' },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(flag).innerHTML = response;
				}
			 
			}).send();	
			}
		}
	
</script>

<?php } ?>

<?php if(in_array($page_name,$media_box_array)) { ?>
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/mediabox.js"></script>
<link href="<?php echo $CFG->siteroot;?>/css/mediabox.css" rel="stylesheet" type="text/css"/>
<?php } ?>

<?php if(in_array($page_name,$ckeditor_array)) { ?>
<?php } ?>


<?php //if(in_array($page_name,$form_check_array)) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->siteroot;?>/css/formcheck.css">
<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/en.js" ></script>
<script language="javascript" type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/formcheck.js" ></script>

<script type="text/javascript" >

	var form_checker;
	window.addEvent('domready', function(){
		new FormCheck('s4c_reg',{display:{showErrors:1}});
		new FormCheck('college_form',{display:{showErrors:1}});
		new FormCheck('contactForm',{display:{showErrors:1}});
		new FormCheck('gallery',{display:{showErrors:1}});
		new FormCheck('profile_form',{display:{showErrors:1}});
		new FormCheck('login_user',{display:{showErrors:1}});
		new FormCheck('college_form',{display:{showErrors:1}});
		new FormCheck('form_school_colleges',{display:{showErrors:1}});
		new FormCheck('counselor_form',{display:{showErrors:1}});


		form_checker = new FormCheck('form_school_colleges',{display:{showErrors:1},
					alerts : {
						radio : 'Please choose a Membership'
					}
				});


		
				
	    friendcheck = new FormCheck('friend_form', { 
                display : { 
                       
                        showErrors : 1 
                }, 
				submitByAjax: true 
        }) 
		 friendcheck.addEvent('onAjaxRequest', function(){ 
                // Add the Ajax event so the form validator knows what function(s) 
 
               search_friends();
        }); 
		
	});	

</script>

<?php //} 

?>

<?php if(in_array($page_name,$ckeditor_array)) { ?>
<link rel="stylesheet" href="<?php echo $CFG->siteroot;?>/editor/ckeditor/samples/sample.css" type="text/css">
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/samples/sample.js"></script>
<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/ckfinderinit.js"></script>
<?php } ?>

<?php if($page_name =='personal_details'){ ?>
		<!-- Verschachteltes Accordion -->	
		<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/functions.js"></script>
</script>
<?php } ?>



<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="css/ie7.css" />
<![endif]-->
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="css/ie6.css" />
<![endif]-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29343353-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<?php
	$colleges_array = array('colleges', 'colleges_profile', 'colleges_degrees', 'colleges_culture_campus_life', 'colleges_gallery', 'colleges_scholarships', 'colleges_admissions', 'colleges_contact_information', 'colleges_send_enquiry', 'colleges_download_brochures', 'colleges_related_colleges');

	$index_selected =($page_name == 'index')?'class="selected"':'';
	$about_us_selected =($page_name == 'about_us')?'class="selected"':'';
	$colleges_selected =(in_array($page_name ,$colleges_array))?'class="selected"':'';
	$blog_selected =($page_name == 'blog')?'class="selected"':'';
	$counseling_selected =($page_name == 'counseling')?'class="selected"':'';
	$help_selected =($page_name == 'help')?'class="selected"':'';
	$contact_us_selected =($page_name == 'contact_us')?'class="selected"':'';
?>

<?php 
	if($page_name =='new_messages' || $page_name =='new_messagesp' || $page_name == 'friend_request'  || $page_name == 'message'||$page_name == 'ask_question' || $page_name == 'ask_fp_question' || $page_name == 'ask_pf_question' || $page_name == 'events_calender' || $page_name == 'counselor_query_detail' || $page_name == 'counselor_query_reply' || $page_name == 'exam_edit' || $page_name == 'school_additional_details' || $page_name == 'scholarships_edit' || $page_name == 'counselor_query_reply' || $page_name == 'send_search'){

	}else{
		require_once('includes/header_common.php');
	}
?>
<?php if($page_name =='events_calender' ){ ?>
	<link href="<?php echo $CFG->siteroot;?>/css/calendar.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/menu.js"></script>
	<script type="text/javascript" src="<?php echo $CFG->siteroot;?>/js/custom-form-elements.js"></script>
<?php } ?>

