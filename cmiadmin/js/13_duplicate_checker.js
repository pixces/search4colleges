/*
	Class:    	duplicate_checker
	Author:   	David Walsh
	Website:    http://davidwalsh.name
	Version:  	1.0
	Date:     	09/23/2008
	Built For:  MooTools 1.2.0
	
	SAMPLE USAGE AT BOTTOM OF THIS FILE
	
*/
var duplicate_checker = new Class({
	
	//implements
	Implements: [Options],

	//options
	options: {
		trigger: 'keyup',
		offset: { x:0, y:0 },
		element: '',
		table: '',
		availableClass: 'available',
		takenClass: 'taken',
		availableImage: '',
		takenImage: '',
		url: 'ajax_handler.php'
	},
	
	//initialization
	initialize: function(options) {
		//set options
		this.setOptions(options);
		
		//validate it
		this.validate();
	},
	
	//a method that does whatever you want
	validate: function() {
		this.options.element.addEvent(this.options.trigger,function() {

				var othis = this;
				var request = new Request({
					url: othis.options.url,
					method: 'post',
					data: {
						value: othis.options.element.value,
						table: othis.options.table,
						field: othis.options.field,
						check_exist: othis.options.check_exist,
						current_id: othis.options.current_id,
						ajax: 1
					},
					onRequest: function() {
						//remove existing classes
						othis.options.element.removeClass(othis.options.availableClass).removeClass(othis.options.takenClass);
					},
					onComplete: function(response) {
						//add class
						othis.options.element.addClass(response == 1 ? othis.options.availableClass : othis.options.takenClass);
						othis.injectImage(response == 1 ? othis.options.availableImage : othis.options.takenImage);
				
						if(response == 1)
						{
							$('toggler').value = 1 ;
						}
						else
						{
							$('toggler').value = 0 ;							
						}
					}
				}).send();
		}.bind(this));
	},
	
	//adds the image
	injectImage: function(image) {
		
		//figure out its position
		var pos = this.options.element.getCoordinates();
		
		var img = new Element('img',{
			src: image,
			styles: {
				'z-index': 5,
				'position': 'absolute',
				'top': pos.top + this.options.offset.y,
				'left': pos.left + pos.width + this.options.offset.x
			}
		}).inject(document.body);
		
		
	}
	
});