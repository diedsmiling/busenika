<?php
/**
 * You face a god 
 * Developer tools lib
 * @author diedsmiling@gmail.com 
 * The path of the righteous man is beset on all sides by the iniquities of the selfish and the tyranny of evil men. 
 * Blessed is he, who in the name of charity and good will, shepherds the weak through the valley of darkness, 
 * for he is truly his brother’s keeper and the finder of lost children. And I will strike down upon thee with great 
 * vengeance and furious anger those who would attempt to poison and destroy my brothers.
 */ 
define('AUTH_BY_SESSION', true);

class God{
	

	
	private $ids = array(
		'178.168.54.138' => 'Lazarev', /* dcs office */
		'212.56.216.30' => 'Lazarev', /* lazarev home */	
		'178.168.64.253' => 'Lazarev', /* lazarev home */	
		'89.208.105.2' => 'Kirill', /* Kir home*/
	);
	
	private $innerIds = array(
		'373' => 'Lazarev',	
	);
	
	private $gods = array(
		'Lazarev' => array(
			'debug' => 2,			
		),
		'Kirill' => array(
			'debug' => 0
		), 
		'Bash' => array(
			'debug'=> 0
		)
	);
	
	private $sinners = array(
		'6.6.6.0' => 'Satana', /* Satana home */
		'6.6.6.1' => 'Satana', /* Satana office */
	);
	
/**
 * constructor
 *
 * @param string name optional, god`s name
 * @return void
 */
	function god($name = false){
		/* set god mode base on session */
		if(AUTH_BY_SESSION){
			define('GOD', (in_array($_SESSION['auth']['user_id'], array_keys($this->innerIds))) ?  true : false);
			$name = $this->innerIds[$_SESSION['auth']['user_id']];			
		}	
		else{
			/* set god mode based on ip*/
			define('GOD', (in_array($_SERVER['REMOTE_ADDR'], array_keys($this->ids))) ?  true : false);
			/* sinners not allowed */		
			if(in_array($_SERVER['REMOTE_ADDR'], array_keys($this->sinners)))
				die('Not allowed');
			/* init debug level */			
			if($name == null || !$name || $name == false)
				$name = $this->ids[$_SERVER['REMOTE_ADDR']];	
			
		}
		
		ini_set('display_errors', $this->gods[$name]['debug']);		
	}
	
/**
 * devineDump method, dumps a variable if god is initiated
 *
 * @param any $var
 * @return void
 */ 		
	public function devineDump($var){
		if(GOD){
			echo '<pre>';
			var_dump($var);	
			echo '</pre>';	
		}
	}	
	
/**
 * sin method, stops executing code if god is initiated
 *
 * @return void
 */
	public function sin(){
		if(GOD)
			die('I am the alpha and the omega.');	
	}	
	
/** 
 * apocalypse method, does something bad
 *
 * @return void
 */ 
	private function apocalypse(){
		die('Boom!');		
	}	
}	

?>
