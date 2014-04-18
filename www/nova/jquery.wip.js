jQuery.noConflict()(function($){

$(".style-picker .opentab").click(function() {
	if($('div.style-picker').css('left')=='-182px')
		{	
			$('div.style-picker').animate({'left':'0'});
					
		}
	if($('div.style-picker').css('left')=='0px')
		{	
			$('div.style-picker').animate({'left':'-182'});
					
		}
});

	var dir = $('#themedir').val();
	var skins = $('#skins').val();

$('#skins').live("click",function()
{
	var skins = jQuery(this).val();
	$("#Custom-css").attr("href", "http://themeinprogress.com/demo/nova/wp-content/themes/nova/skins/"+skins+".css");
	$('#customstyle').html('');

	var substr = skins.split('_');
	jQuery('body').removeClass('dark').removeClass('light').addClass(substr[0]);

});

$('#patterns').live("click",function()
{
	var skins = $('#skins').val();
	var patterns = $(this).val();

	$("#Custom-css").attr("href", "http://themeinprogress.com/demo/nova/wp-content/themes/nova/skins/"+skins+".css");
	$('#customstyle').append( 'body { background-image: url('+dir+patterns+') !important; background-repeat:repeat;}  ');

});


/* ===============================================
   Mobile menu with Tinynav Plugin
   =============================================== */

	if ( $('nav#mainmenu ul.menu .current-menu-item').length ) { 
	
		$('nav#mainmenu ul.menu').tinyNav({
			active: 'current-menu-item',
		});

	} else {
	
		$('nav#mainmenu ul.menu').tinyNav({
			header: 'Select an item',
		});

	}

/* ===============================================
   Scroll to Top Plugin
   =============================================== */

	$(window).scroll(function() {
		if( $(window).scrollTop() > 500 ) {
			$('#back-to-top').fadeIn(500);
				} else {
			$('#back-to-top').fadeOut(500);
		}
	});

	$('#back-to-top').click(function(){
		$.scrollTo(0,'slow');
		return false;
	});


/* ===============================================
   Portfolio code
   =============================================== */

	$('.filterable-grid li').live('mouseover',function(){
			
		var imgw = $('.overlay',this).prev().width();
		var imgh = $('.overlay',this).prev().height();
			
		$('.overlay',this).css({'width':imgw,'height':imgh});	
			
		$('.overlay',this).animate({ opacity : 0.6 },{queue:false});
	
	});

	$('.filterable-grid li').live('mouseout',function(){
		
		$('.overlay', this).animate({ opacity: 0}, { queue:false });
		
	});

	$('.skills .views').click(function(){
	
	if($(this).next('ul').css('display')=='none') {	
			$(this).addClass('active');
			$('.skills .views i').addClass('open');
		}
	else {	
			$(this).removeClass('active');
			$('.skills .views i').removeClass('open');
		}
				
			$(this).next('ul').stop(true,false).slideToggle('hight');

	});

/* ===============================================
   Contact form code
   =============================================== */

	$(".buttons").click(function() {
		
			var hasError = false;
	
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			
			if($("input#contact-name").val() == '') {
				hasError = true;
			}
			if($("input#contact-subject").val() == '') {
				hasError = true;
			}
			if($("#contact-message").val() == '') {
				hasError = true;
			}
			if( (!emailReg.test( $("#contact-email").val() )) || ($("#contact-email").val() == '') ) {
				hasError = true;
			}
	
			if(hasError == true)
			{
				$('span.error').css({'display':'block'});
				return false;
	
			}else {
				$('span.error').css({'display':'none'});
				return true;
			}
	
	});

/* ===============================================
   Menu code
   =============================================== */

	$('nav#mainmenu li').hover(
			function () {
				$(this).children('ul').stop(true, true).fadeIn(100);
	 
			}, 
			function () {
				$(this).children('ul').stop(true, true).fadeOut(400);		
			}
			
	
	);

	$('nav#mainmenu ul > li').each(function(){
    	if( $('ul', this).length > 0 )
        $(this).children('a').append('<span class="sf-sub-indicator"> <i class="icon-angle-down"></i> </span>').removeAttr("href");
	}); 

	$('nav#widgetmenu ul > li').each(function(){
    	if( $('ul', this).length > 0 )
        $(this).children('a').append('<span class="sf-sub-indicator"> &raquo;</span>').removeAttr("href");
	}); 

	$('nav#widgetmenu ul > li ul').click(function(e){
		e.stopPropagation();
    })
	
    .filter(':not(:first)')
    .hide();
    
	$('nav#widgetmenu ul > li, nav#widgetmenu ul > li > ul > li').click(function(){

		var selfClick = $(this).find('ul:first').is(':visible');
		if(!selfClick) {
		  $(this).parent().find('> li ul:visible').slideToggle('low');
		  
		
		}
		
		$(this).find('ul:first').stop(true, true).slideToggle();
	
	});

/* ===============================================
   Tabs code
   =============================================== */

	$( ".tabs" ).tabs({ hide: { effect: "fadeOut", duration: 300 } });
	
/* ===============================================
   Toggle code
   =============================================== */
	
	$('.toggle_container h5.element').last().css('border-bottom', 'none' , 'border-top', 'none');
	$('.toggle_container h5.element').click(function(){		
		if($(this).next('.toggle').css('display')=='none') {	
				$(this).addClass('inactive');
				$(this).children('i').addClass('open');
			}
		else {	$(this).removeClass('inactive');
				$(this).children('img').removeClass('inactive');
				$(this).children('i').removeClass('open');
			}
					
				$(this).next('.toggle').stop(true,false).slideToggle('slow');
	
		});

/* ===============================================
   Overlay code
   =============================================== */
	
	$('.overlay-image.shortcode-thumb').hover(function(){
		
		var imgwidth = $(this).children('img').width();
		var imgheight = $(this).children('img').height();
		$(this).children('.zoom').css({'width':imgwidth,'height':imgheight});	
		$(this).children('.link').css({'width':imgwidth,'height':imgheight});		
		$(this).css({'width':imgwidth+10});		
		
		$('.overlay',this).animate({ opacity : 0.6 },{queue:false});
		}, 
		function() {
		$('.overlay',this).animate({ opacity: 0.0 },{queue:false});
	
	});
	
	$('.overlay-image.blog-thumb').hover(function(){
		
		var imgwidth = $(this).children('img').width();
		var imgheight = $(this).children('img').height();
		$(this).children('.link').css({'width':imgwidth,'height':imgheight});		
		$(this).css({'width':imgwidth});		
		
		$('.overlay',this).animate({ opacity : 0.4 },{queue:false});
		}, 
		function() {
		$('.overlay',this).animate({ opacity: 0.0 },{queue:false});
	
	});

	$('.gallery img').hover(function(){
		$(this).animate({ opacity: 0.50 },{queue:false});
	}, 
	function() {
		$(this).animate({ opacity: 1.00 },{queue:false});
	});
	
/* ===============================================
   Prettyphoto code
   =============================================== */

	$("a[data-rel^='prettyPhoto']").prettyPhoto({
	
				animationSpeed:'fast',
				slideshow:5000,
				theme:'pp_default',
				show_title:false,
				overlay_gallery: false,
				social_tools: false
	});

});          