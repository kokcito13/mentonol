$(document).ready(function() {
   $('#mainmenu .menu-item').mouseover(function(){
		$(this).children('.sub-menu').addClass('hoverMenu').animate({opacity:"1"},{ queue:false, duration: 1000 ,easing:"swing"});
		$(this).addClass('hoverItem');
	}).mouseout(function(){
		$(this).children('.sub-menu').removeClass('hoverMenu').animate({opacity:"0"},{ queue:false, duration: 1000 ,easing:"swing"});
		$(this).removeClass('hoverItem');
	});
});