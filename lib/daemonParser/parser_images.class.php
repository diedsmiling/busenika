<?php
/** 
 * Image parser class for managing application images 
 *
 * @copyright  	Kozin.net (c) (http://korzin.net)
 * @author		diedsmiling@gmail.com
 */
namespace parser\images;
	use \parser;	
	class Exception extends \Exception {}
	class Handle extends parser\Queen{
		
/**
 * Constructor.
 *
 */		
		public function __construct(){
			$this->type = 'daemon_images';				
			$this->title = 'Изображения NEW';				
		}	
		
/**
 * Proceeds upload and parse
 * 
 * @throws Exception
 * @return void
 */ 		
		public function proceed(){
				throw new Exception('try no 1'); 
			
		}	
		
		
	}	



