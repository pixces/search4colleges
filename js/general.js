/* General.js includes common function */

/* 
	Sexy alert function 
*/
function call_alert_confirm(message,link){
	Sexy.confirm(message, {
		onComplete:
			function(returnvalue) {
			if (returnvalue) {
				//$("frm_search").submit();
				if(link != '')
					window.location = link;
			}
			else {
				return false;
			}
		}
	});
}
/*
	Motools tipz initialization
*/
function show_tipz(){
	$$('a.tipz').each(function(element,index) {
		var content = element.get('title').split('::');
		element.store('tip:title', content[0]);
		element.store('tip:text', content[1]);
	});
	
	//create the tooltips
		var tipz = new Tips('.tipz',{
			className: 'tipz',
			fixed: true,
			hideDelay: 50,
			showDelay: 50
	});

}

function call_alert_delete_msg(message,id){
	Sexy.confirm(message, {
		onComplete:
		function(returnvalue) {
			if (returnvalue) {
				check_delete_msg(id);
			}
			else {
				return false;
			}
		}
	});
}

function call_alert_featured_confirm(message,val){
	Sexy.confirm(message, {
		onComplete:
		function(returnvalue) 
		{
			if (returnvalue) 
			{	
				if($('isapproved['+val+']').checked==true)
				{
					$('isapproved['+val+']').checked=false;				
				}
				else if($('isapproved['+val+']').checked==false)
				{
					$('isapproved['+val+']').checked=true;			
				}
				
				check_isapproved(val);				
			}
			else 
			{				
				return false;
			}
		}
	});
}
