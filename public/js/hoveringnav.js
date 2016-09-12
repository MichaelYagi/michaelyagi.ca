/*
* Michael Yagi
* Fixed position nav bar when scrolling webpage
*/

!function ($) {
	$(function(){
  	
		$(window).scroll(function () {
  			if ($(window).scrollTop() >= 32) 
  			{
    			$('#nav').addClass('nav-fixed');
  			} 
  			else if ($(window).scrollTop() <= 0)
  			{
    			$('#nav').removeClass('nav-fixed');
  			}
		});
	
	})
}(window.jQuery)