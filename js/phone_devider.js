$(document).ready(function() {
	$("#phone_code, #phone_number").change(function(event) {
		var code = $("#phone_code").attr('value');
		var number = $("#phone_number").attr('value');
		
		var fullNumber  = "+7"+code+number;
		
		
		$("#elm_35").val(fullNumber);
		if(fullNumber == "+7"){

				$("#elm_35").removeAttr('value');
			}

	});

//email duplicate automaticly
$("#email").change(function(event){
	var emailGrabbed = $(this).attr('value');
	$("#elm_33").val(emailGrabbed);
	
});	
	
});
