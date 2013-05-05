var mootime = function(timepickerInput) {
	var mootimeID = timepickerInput + "_mootimewrap";
	var mootimeHTML = unescape('%0A%09%09%09%09%3C%64%69%76%20%63%6C%61%73%73%3D%27%74%69%6D%65%70%69%63%6B%65%72%27%3E%0A%09%09%09%09%09%3C%64%69%76%20%63%6C%61%73%73%3D%27%74%69%6D%65%27%3E%0A%09%09%09%09%09%09%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%64%69%73%70%6C%61%79%5F%68%6F%75%72%27%3E%31%32%3C%2F%73%70%61%6E%3E%3A%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%64%69%73%70%6C%61%79%5F%6D%69%6E%27%3E%30%3C%2F%73%70%61%6E%3E%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%64%69%73%70%6C%61%79%5F%6D%69%6E%74%77%6F%27%3E%30%3C%2F%73%70%61%6E%3E%0A%09%09%09%09%09%09%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%64%69%73%70%6C%61%79%5F%61%6D%70%6D%27%3E%61%6D%3C%2F%73%70%61%6E%3E%0A%09%09%09%09%09%3C%2F%64%69%76%3E%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%64%69%76%20%63%6C%61%73%73%3D%27%61%6D%6F%72%70%6D%27%3E%0A%09%09%09%09%09%09%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%61%6D%70%6D%20%61%6D%20%6D%6F%6F%74%69%6D%65%61%6D%70%6D%5F%61%63%74%69%76%65%27%3E%61%6D%3C%2F%73%70%61%6E%3E%0A%09%09%09%09%09%09%3C%73%70%61%6E%20%63%6C%61%73%73%3D%27%61%6D%70%6D%20%70%6D%27%3E%70%6D%3C%2F%73%70%61%6E%3E%0A%09%09%09%09%09%3C%2F%64%69%76%3E%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%64%69%76%20%63%6C%61%73%73%3D%27%63%6F%6E%74%72%6F%6C%73%27%3E%0A%09%09%09%09%09%09%3C%61%20%63%6C%61%73%73%3D%27%63%6C%6F%73%65%5F%74%69%6D%65%70%69%63%6B%65%72%27%3E%63%6C%6F%73%65%3C%2F%61%3E%26%6E%62%73%70%3B%26%6E%62%73%70%3B%0A%09%09%09%09%09%09%3C%61%20%63%6C%61%73%73%3D%27%73%61%76%65%5F%74%69%6D%65%70%69%63%6B%65%72%27%3E%73%61%76%65%3C%2F%61%3E%20%20%0A%09%09%09%09%09%3C%2F%64%69%76%3E%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%64%69%76%20%63%6C%61%73%73%3D%27%73%65%70%27%3E%3C%2F%64%69%76%3E%20%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%75%6C%20%63%6C%61%73%73%3D%27%68%6F%75%72%73%27%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6C%65%67%65%6E%64%27%3E%68%72%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%31%20%6D%6F%6F%74%69%6D%65%5F%61%63%74%69%76%65%27%3E%3C%73%70%61%6E%3E%31%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%32%27%3E%3C%73%70%61%6E%3E%32%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%33%27%3E%3C%73%70%61%6E%3E%33%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%34%27%3E%3C%73%70%61%6E%3E%34%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%35%27%3E%3C%73%70%61%6E%3E%35%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%36%27%3E%3C%73%70%61%6E%3E%36%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%37%27%3E%3C%73%70%61%6E%3E%37%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%38%27%3E%3C%73%70%61%6E%3E%38%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%39%27%3E%3C%73%70%61%6E%3E%39%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%31%30%27%3E%3C%73%70%61%6E%3E%31%30%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%31%31%27%3E%3C%73%70%61%6E%3E%31%31%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%68%72%73%20%68%72%31%32%27%3E%3C%73%70%61%6E%3E%31%32%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%3C%2F%75%6C%3E%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%75%6C%20%63%6C%61%73%73%3D%27%6D%69%6E%5F%66%69%72%73%74%27%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%6C%65%67%65%6E%64%27%3E%6D%6E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%30%20%6D%6F%6F%74%69%6D%65%5F%61%63%74%69%76%65%27%3E%3C%73%70%61%6E%3E%30%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%31%27%3E%3C%73%70%61%6E%3E%31%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%32%27%3E%3C%73%70%61%6E%3E%32%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%33%27%3E%3C%73%70%61%6E%3E%33%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%34%27%3E%3C%73%70%61%6E%3E%34%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%20%6D%69%6E%35%27%3E%3C%73%70%61%6E%3E%35%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%20%0A%09%09%09%09%09%3C%2F%75%6C%3E%0A%09%09%09%09%09%0A%09%09%09%09%09%3C%75%6C%20%63%6C%61%73%73%3D%27%6D%69%6E%5F%73%65%63%6F%6E%64%27%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%30%20%6D%6F%6F%74%69%6D%65%5F%61%63%74%69%76%65%27%3E%30%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%20%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%31%27%3E%3C%73%70%61%6E%3E%31%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%32%27%3E%3C%73%70%61%6E%3E%32%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%33%27%3E%3C%73%70%61%6E%3E%33%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%34%27%3E%3C%73%70%61%6E%3E%34%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%35%27%3E%3C%73%70%61%6E%3E%35%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%36%27%3E%3C%73%70%61%6E%3E%36%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%37%27%3E%3C%73%70%61%6E%3E%37%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%38%27%3E%3C%73%70%61%6E%3E%38%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%09%3C%6C%69%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%20%6D%69%6E%30%39%27%3E%3C%73%70%61%6E%3E%39%3C%2F%73%70%61%6E%3E%3C%2F%6C%69%3E%0A%09%09%09%09%09%3C%2F%75%6C%3E%0A%09%09%09%09%3C%2F%64%69%76%3E%0A%09%09%09%09%0A%09%09%09%09%3C%69%6E%70%75%74%20%74%79%70%65%3D%27%68%69%64%64%65%6E%27%20%63%6C%61%73%73%3D%27%68%6F%75%72%5F%6D%65%6D%6F%72%79%27%20%76%61%6C%75%65%3D%27%31%32%27%3E%0A%09%09%09%09%3C%69%6E%70%75%74%20%74%79%70%65%3D%27%68%69%64%64%65%6E%27%20%63%6C%61%73%73%3D%27%6D%69%6E%5F%6D%65%6D%6F%72%79%27%20%76%61%6C%75%65%3D%27%30%27%3E%0A%09%09%09%09%3C%69%6E%70%75%74%20%74%79%70%65%3D%27%68%69%64%64%65%6E%27%20%63%6C%61%73%73%3D%27%6D%69%6E%74%77%6F%5F%6D%65%6D%6F%72%79%27%20%76%61%6C%75%65%3D%27%30%27%3E%0A%09%09%09%09%3C%69%6E%70%75%74%20%74%79%70%65%3D%27%68%69%64%64%65%6E%27%20%63%6C%61%73%73%3D%27%61%6D%70%6D%5F%6D%65%6D%6F%72%79%27%20%76%61%6C%75%65%3D%27%61%6D%27%3E%0A');
	var newMooTime = new Element('div', { 
		'id': mootimeID,
		'class': 'timepickerwrap', 
		'html': mootimeHTML  
	}); 
		
	newMooTime.inject(timepickerInput, 'after'); 
	var createMootime = $(timepickerInput + "_mootimewrap");
		
	//shows the timepicker when user focuses on timepicker input field
	$(timepickerInput).addEvent('focus', function(){	
		//calculates various positions to decide where to place the timepicker
		var windowSize = $(window).getSize();
		var windowScroll = $(window).getScroll();
		var mootimeInput = $(timepickerInput).getCoordinates();
		var mootimeInputBottom = mootimeInput.bottom;
		var mootimeInputTop = mootimeInput.top;
		var mootimeInputLeft = mootimeInput.left;
		var mootimeInputRight = mootimeInput.right;
		var adjustTopInput = mootimeInputBottom - windowScroll.y;
		var halfWindow = windowSize.y / 2;
		var distanceRight = windowSize.x - mootimeInputRight;
		
		//sets all timepickers back one, so that one can be called to the front on focus
		$$('.timepickerwrap').each(function(item){
			item.setStyle('z-index', '999'); 
		});
		
		//chooses whether to place the timepicker above or below the input field
		$(createMootime).setStyle('display', 'block'); 
		var mootimePickerLeft = mootimeInputLeft + 10; 
		if (adjustTopInput < halfWindow) { 
			var mootimePickerTop = mootimeInputBottom + 10;
			$(createMootime).setStyle('top', mootimePickerTop);
			$(createMootime).setStyle('z-index', '1000');
			
			//chooses wheter to align the timepicker left of right of the input field
			if (mootimeInputLeft < distanceRight) {
				$(createMootime).setStyle('left', mootimePickerLeft); 
			}
			
			else {
				var timepickerSize = $(createMootime).getSize(); 
				var mootimeInputSize = $(timepickerInput).getSize(); 
				var timepickerLeftLeft = (mootimeInputLeft+ mootimeInputSize.x) - timepickerSize.x;
				$(createMootime).setStyle('left', timepickerLeftLeft); 
			};
		}
		
		else {
			var mootimePickerBottom = mootimeInputTop - 10;
			var mootimePickerHeight = $(createMootime).getSize();
			mootimePickerBottom = mootimePickerBottom - mootimePickerHeight.y;
			$(createMootime).setStyle('top', mootimePickerBottom);
			$(createMootime).setStyle('z-index', '1000');
			
			//chooses wheter to align the timepicker left of right of the input field
			if (mootimeInputLeft < distanceRight) {
				$(createMootime).setStyle('left', mootimePickerLeft); 
			}
			
			else {
				var timepickerSize = $(createMootime).getSize(); 
				var mootimeInputSize = $(timepickerInput).getSize();
				var timepickerLeftLeft = (mootimeInputLeft+ mootimeInputSize.x) - timepickerSize.x;
				$(createMootime).setStyle('left', timepickerLeftLeft); 
			};
		}
	});


	//closes timepicker when user clicks "close" 
	$(createMootime).getElement('.close_timepicker').addEvent('click', function(){ 
		$(createMootime).setStyle('display', 'none');
	});

	//removes the current class from current row only
	var removeActive = function(times) {
		$(createMootime).getElements('.' + times).each(function(item, index){
			var currentClass = item.hasClass('mootime_active');
			if (currentClass) {
				item.removeClass('mootime_active');
			}
		});
	};

	//applies to the following 4 functions:
	//adds a click event to each time row that changes the clicked number to an active class, 
	//removes the active class from the previous current element
	//and sets the time in the upper left display 

	//adds click event to hours
	$(createMootime).getElements('.hrs').each(function(item){ 
		var hour = item.get('text');
		item.addEvent('click', function(){
			$(createMootime).getElement('.display_hour').set('text', hour);
			removeActive('hrs');
			item.addClass('mootime_active');
		});
	});

	//adds click event to top minute row
	$(createMootime).getElements('.min').each(function(item){
		var minute = item.get('text');
		item.addEvent('click', function(){
			$(createMootime).getElement('.display_min').set('text', minute);
			removeActive('min');
			item.addClass('mootime_active');
		});
	});

	//adds click event to bottom minute row
	$(createMootime).getElements('.mintwo').each(function(item){
		var minuteTwo = item.get('text');
		item.addEvent('click', function(){
			$(createMootime).getElement('.display_mintwo').set('text', minuteTwo);
			removeActive('mintwo');
			item.addClass('mootime_active');
		});
	});

	//adds click event to am/pm
	$(createMootime).getElements('.ampm').each(function(item){
		var amPm = item.get('text');
		item.addEvent('click', function(){
			$(createMootime).getElement('.display_ampm').set('text', amPm);
			$(createMootime).getElement('.mootimeampm_active').removeClass('mootimeampm_active');
			item.addClass('mootimeampm_active'); 
		});
	});

	//grabs the current selected time and pushes it into the value property of the mootime input field
	var chosenTime = function() {
		//gets time from the timechooser display
		var hour = $(createMootime).getElement('.display_hour').get('text');
		var min =  $(createMootime).getElement('.display_min').get('text');
		var minTwo =  $(createMootime).getElement('.display_mintwo').get('text');
		var amPm =  $(createMootime).getElement('.display_ampm').get('text');
		var newChosenTime = hour + ':'  + min + minTwo + amPm;
		
		//sets the mootime input field to the time on the timechooser
		$(timepickerInput).setProperty('value', newChosenTime);
		
		//sets memory
		$(createMootime).getElement('.hour_memory').setProperty('value', hour);
		$(createMootime).getElement('.min_memory').setProperty('value', min);
		$(createMootime).getElement('.mintwo_memory').setProperty('value', minTwo);
		$(createMootime).getElement('.ampm_memory').setProperty('value', amPm);
	};

	//SAVE
	$(createMootime).getElement('.save_timepicker').addEvent('click', function(){
		chosenTime();
		$(createMootime).setStyle('display', 'none');
	});


	//CLOSE
	$(createMootime).getElement('.close_timepicker').addEvent('click', function(){
		//removes active class from rows
		removeActive('hrs'); 
		removeActive('min');
		removeActive('mintwo');
		$(createMootime).getElement('.mootimeampm_active').removeClass('mootimeampm_active'); 
		
		//grabs saved time from "memory"
		var memoryHour = $(createMootime).getElement('.hour_memory').getProperty('value'); 
		var memoryMin = $(createMootime).getElement('.min_memory').getProperty('value');
		var memoryMinTwo = $(createMootime).getElement('.mintwo_memory').getProperty('value');
		var memoryAmPm = $(createMootime).getElement('.ampm_memory').getProperty('value');
		
		//resets active class from "memory"
		$(createMootime).getElement("'.hr" + memoryHour + "'").addClass('mootime_active');
		$(createMootime).getElement("'.min" + memoryMin + "'").addClass('mootime_active');
		$(createMootime).getElement("'.min0" + memoryMinTwo + "'").addClass('mootime_active'); 
		$(createMootime).getElement('.' + memoryAmPm + "'").addClass('mootimeampm_active'); 

		//resets timepicker time display from "memory"
		$(createMootime).getElement('.display_hour').setProperty('text', memoryHour);
		$(createMootime).getElement('.display_min').setProperty('text', memoryMin);
		$(createMootime).getElement('.display_mintwo').setProperty('text', memoryMinTwo);
		$(createMootime).getElement('.display_ampm').setProperty('text', memoryAmPm); 

		//hides the timepicker
		$(createMootime).setStyle('display', 'none'); 
	});
};
