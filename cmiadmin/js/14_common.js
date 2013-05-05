function confirm_delete(val,link,page_var,page){
	call_alert_confirm("Are you sure you want to delete this record?",link+'?delete='+val+'&'+page_var+'&page='+page);
}

function check_all_checkbox()
{
	if($('check'))
	{ 
		$('check').addEvent('click', function() 
		{
			if($('check').get('rel') == 'yes' || $('check').get('rel') == 'null')
			{
				do_check = false;
				$('check').set('checked','').set('rel','no');
			}
			else
			{
				do_check = true;
				$('check').set('checked','true').set('rel','yes');
			}
				$$('.check-me').set('checked',do_check) 
		});
	}
}

function get_datepicker()
{
	new DatePicker('.demo_vista', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					format: 'd F, Y',
					toggleElements: '.date_toggler'
			});
}
function get_datepicker_without_toggler()
{
	new DatePicker('.demo_vista_without_toggler', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					format: 'd F, Y'
			});
}
