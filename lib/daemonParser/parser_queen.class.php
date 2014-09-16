<?php
namespace parser{

/**
 * Parser parent class for common parsers logics.
 * 
 * @copyright  	Kozin.net (c) (http://korzin.net)
 * @author		diedsmiling@gmail.com
 */ 
	
	class Queen{
/**
 * Type of parser.
 *
 * @var string
 */	
		public $type = null;	

/**
 * Parser title
 *
 * @var string
 */	
		public $title = null;	
	
/**
 * Constructor.
 *
 * @param
 */		
		public function __construct(){
						
		}	
		
/**
 * Enchances patterns for cs cart
 * 
 * @param array $patterns
 * @return array
 */
		public function enchancePattern($patterns){			
			$patterns[$this->type]['section'] = 'products'; 
		//	$patterns[$this->type]['name'] = $this->title; 			
			return $patterns;
		}	
		
	}
	
/**
 * Uploads file to server
 *  
 * @return string filename
 // */
		// public function upload($patterns){			
			// $patterns[$this->type]['section'] = 'products'; 
			//$patterns[$this->type]['name'] = $this->title; 			
			// return $patterns;
		// }	
		
	

	
		
class Exception extends \Exception {}
	}

