var Site = {
	
			start: function(){
				if($('accordion')) Site.accordion();
			},
			
			accordion: function(){
				var list = $$('#accordion li div.collapse');
				var headings = $$('#accordion li h1');
				var collapsibles = new Array();
				var spans = new Array();
				
				headings.each( function(heading, i) {

					var collapsible = new Fx.Slide(list[i], { 
						duration: 500, 
						transition: Fx.Transitions.quadIn
					});

					collapsibles[i] = collapsible;
					spans[i] = $('myspan');
					
					heading.onclick = function(){
						var span = $('myspan');

						if(span){
							var newHTML = span.innerHTML == '+' ? '-' : '+';
							span.setHTML(newHTML);
						}
						
						for(var j = 0; j < collapsibles.length; j++){
							if(j!=i) {
								collapsibles[j].slideOut();
								if(spans[j]) spans[j].setHTML('+');
							}
						}						
						collapsible.toggle();						
						return false;
					}					
					if(!list[i].hasClass("selected"))
						collapsible.hide();
					
				});
			}
		};
		window.addEvent('domready', Site.start);