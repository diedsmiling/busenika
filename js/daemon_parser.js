/*
 * template uploader
 * @author lazarev
 * @email diedsmiling@gmail.com
 * @uri http://smartresponder.ru
 */ 

(function($) {	
    $.fn.demonParser = function(options) {

/**
 * Form DOM object
 *
 * @var object
 */		
        var dom_form = this;		
	
/**
 * Default uploader settings 
 *
 * @var array
 */
		var settings = $.extend({
						
		}, options);		
      

/**
 * Filesize
 * 
 * @var int
 */
		var filesize = 0;
	
/**
 * Filename
 * 
 * @var string
 */
		var filename = '';

/**
 * Ajax-submit options
 * 
 * @var array
 */ 
		var ajaxSettings = {
			dataType: 'json',
			uploadProgress: uploadProgress,
			beforeSend: prepareUpload,
			success: result,
			url: 'zenon.php?dispatch=exim.import_daemon'				
		}	
	
/**
 * Increment value
 *
 * @var int
 */ 
		var i = 1;
		
/**
 * Initialize form submit event
 *
 * @return false
 */		
		function init(){												
			if($("#daemon_images").hasClass("cm-active")){
				console.log('aa');
				$(".submit-button").click(function(){
					//$(this).parent().parent().parent().hide();
					$(this).parent().parent().parent().ajaxSubmit(ajaxSettings);
					return false;
				});
			}
		}	

/**
 * Perform file upload
 *
 * @return void
 */		
		function performUpload(obj){			
			
		}
		
/**
 * On progress function
 *
 * @param obj event
 * @param int position current bytes updated
 * @param int total total file size
 * @param int percentComplete
 * @return void
 */ 		
		function uploadProgress(event, position, total, percentComplete){
			
		}
		
/**
 * Preparations must be made
 * 
 * @return void
 */
		function prepareUpload(){
					
		}
		
/**
 * Inserts code into editor
 * 
 * @params responseText
 * @params statusText
 * @params xhr
 * @params $form
 * @return 
 */
	function result(responseText, statusText, xhr, $form) { 		
		
		
	} 
		
/* inits process */		
		init();    		
    };
 
}(jQuery));

$(function() {
	
	$("#demon").demonParser();
	
});
