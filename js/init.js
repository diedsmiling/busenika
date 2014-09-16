$(document).ready(function () {
	var projects = [
		{
			value: "jquery",
			label: "jQuery",
			desc: "the write less, do more, JavaScript library",
			icon: "jquery_32x32.png"
		},			
		{
			value: "jquery-ui",
			label: "jQuery UI",
			desc: "the official user interface library for jQuery",
			icon: "jqueryui_32x32.png"
		},
		{
			value: "sizzlejs",
			label: "Sizzle JS",
			desc: "a pure-JavaScript CSS selector engine",
			icon: "sizzlejs_32x32.png"
		}
	];
		
	var cities = ["Aberdeen", "Ada", "Adamsville", "Addyston", "Adelphi", "Adena", "Adrian", "Akron"];

	$( ".search-input" ).autocomplete('index.php?dispatch=products.ajax_search', {
		minChars: 0,
		max: 8,
		scrollHeight: 820,
		width: 380,
		matchContains: false,
		selectFirst: false,
		redirect: true,	
		autoFill: false,	
		redirectUrl: 'http://lindero.ru/index.php?dispatch=products.view&product_id=',
		formatItem: function(data, i, n, value) {			
			return '<div class="redirect_div"><div class="wrap_img"><span class="vline"></span><img src="'+ value.split("@")[3] + '"></div><span class="imitl">'+ value.split("@")[0] + '</span><div class="wrap_price"><div class="'+value.split("@")[1]+'"></div><div class="price_light"><strong>'+ value.split("@")[2] + '<span> руб.</span></strong></div></div></div>';
			
		},
		formatResult: function(data, value) {
				return value.split("@")[0];
		}
	}); 
	
	$(".search-input").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			document.forms["search_form"].submit();    
		}
	});
				
	$(".no_paginate_link").attr('href', document.URL+'&no_pagination=1');
	
	/* mask imput */
	$("#elm_35").mask("7(999) 999-9999");
	
	if($('#slides').length > 1){	
		$('#slides').slides({
			play: 5000,
			pause: 2500,
			hoverPause: true,		
		});	
	}
	
	/* email kostil` */
	$("#no-email").click(function(){		
		$("#soacf_elm_email").val('zakaz@korzin.net');
		$(this).parent().siblings('.fright').find('input').click();
	});
	
	/* image kostil` */
	$(".feature-description").find('p').eq(0).prepend($(".feature-description").find('img').eq(0).css('float', 'left').css('margin', '3px 10px 5px 0px'));

	$('.button-submit-action').click(function(){
	yaCounter21813562.reachGoal('korzin');
	});	

});
  
	
