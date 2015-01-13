<?php

include_once DIR_ROOT . "/core/fn.catalog.php";
/*
	$config - database connection configuration array
	$config['db_host'] = 'localhost';
	$config['db_name'] = 'korzin_db';
	$config['db_user'] = 'korzin_user';
	$config['db_password'] = 'kslQi0121oPsl';
	$config['db_type'] = 'mysql';
	
	$sourceDB - name of source database
	$destinationDB - name of destination database
*/
class ImportTool {

	private $link;
	private $sourceDB;
	private $destinationDB;
	private $config;
    private $watermark;
	
	function __construct($config, $sourceDB, $destinationDB){
		$this->config = $config;
		$this->link = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'])
		or die('Database connection error. ' . mysqli_error($this->link));
		mysqli_set_charset($this->link, 'utf8');
		$this->sourceDB = $sourceDB;
		$this->destinationDB = $destinationDB;
	}
	
	function purgeTable ($tableName){
		$this->useDatabase($this->destinationDB);
		$query = "DELETE FROM {$tableName}";
		$result = mysqli_query($this->link, $query) or die("Failed to delete table: {$tableName}" . mysql_error());
		if ($result){
			echo "Table {$tableName} deleted;";
		}
	}
	
	function importCategories(){
		$this->useDatabase($this->sourceDB);
		$query = "SELECT * FROM `categories` ORDER BY `categories`.`cat_id` ASC";
		$result = mysqli_query($this->link, $query) or die('Failed to select categories: ' . mysqli_error($this->link));
		while ($category = mysqli_fetch_array($this->link, $result, MYSQL_ASSOC)) {
				$template = array(
					'category_id' => $category['id'],
					'category' => $category['name'],
					'parent_id' => $category['cat_id'],
					'description' => '',
					'status' => 'A',
					'page_title' => '',
					'meta_description' => '',
					'meta_keywords' => '',
					'usergroup_ids' => '',
					'position' => $category['position'],
					'timestamp' => date('j/m/Y'),   //'14/09/2014'
					'product_details_layout' => 'default',
					'use_cusom_templates' => 'N',
					'iscussion_type' => 'D'
					);
				fn_update_category($template);
				echo "category {$category['name']} {$category['cat_id']} {$category['position']} added.<br>";
			}
	}

	function deleteAllProducts(){
		$this->useDatabase($this->destinationDB);	
		$query = "SELECT product_id FROM cscart_products";
		$result = mysqli_query($this->link, $query) or die("Failed select from: cscart_products" . mysqli_error($this->link));
		while ($item = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			fn_delete_product($item['product_id']);
			echo "Item {$item['product_id']} deleted<br>";
		}
	}
	
	function importProducts(){
		$this->useDatabase($this->sourceDB);
		$query = "SELECT * FROM shop_items";
		$result = mysqli_query($this->link, $query) or die('Failed to select items: ' . mysqli_error($this->link));
		while ($item = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				$imageList = unserialize($item['file']);
				$mainImg = array_shift($imageList);
				$idx = 0;
				$product_add_additional_image_data = array();
				$file_product_add_additional_image_icon = array();
				$type_product_add_additional_image_icon = array();
				$file_product_add_additional_image_detailed = array();
                $type_product_add_additional_image_detailed = array();
				foreach($imageList as $image)
				{
					$product_add_additional_image_data[$idx =($idx!=2)?$idx:3] = array (
								'pair_id' => '',
								'type' => 'A',
								'object_id' => '0',
								'image_alt' => '',
								'detailed_alt' => ''
							);
					$file_product_add_additional_image_icon[$idx =($idx!=2)?$idx:3] = ($idx==0) ? "product_add_additional" : '';
					$type_product_add_additional_image_icon[$idx =($idx!=2)?$idx:3] = ($idx==0) ? "local" : '';
					$file_product_add_additional_image_detailed[$idx =($idx!=2)?$idx:3] = "images/import/{$image}";
					$type_product_add_additional_image_detailed[$idx =($idx!=2)?$idx:3] = "server";
					$idx++;
				}
				$_REQUEST = array(
						'fake' => '1',
						'selected_section' => 'images',
						//product_id => $item['id'] ,
						'product_data' => array(
							'product_id' => $item['id'] ,
							'product' => $item['name'],
							'main_category' => $item['cat_id'],
							'price' => $item['price'],
							'full_description' => $item['description'],
							'status' => 'A',
							'amount' => $item['quantity'],
							'timestamp' => strtotime($item['date']),
							'relative' => $item['relative'],
							'product_features' => array(
								20 => $item['color'] //color
								)
							),
						'product_main_image_data' => array(
							0 => array (
								'pair_id' => '',
								'type' => 'M',
								'object_id' => '0',
								'image_alt' => '',
								'detailed_alt' => ''
							)),
						'file_product_main_image_icon' => array(
							0 => 'product_main'
							),
						'type_product_main_image_icon' => array(
							0 => 'local'
							),
						'file_product_main_image_detailed' => array(
							0 => "images/import/{$mainImg}"
							),
						'type_product_main_image_detailed' => array(
							0 => 'server'
							)
						);
				$_REQUEST['product_add_additional_image_data'] = $product_add_additional_image_data;
				$_REQUEST['file_product_add_additional_image_icon'] = $file_product_add_additional_image_icon;
				$_REQUEST['type_product_add_additional_image_icon'] = $type_product_add_additional_image_icon;
				$_REQUEST['file_product_add_additional_image_detailed'] = $file_product_add_additional_image_detailed;
				$_REQUEST['type_product_add_additional_image_detailed'] = $type_product_add_additional_image_detailed;
				$_SERVER['REQUEST_METHOD'] = 'POST';
				$mode = 'add';

				include DIR_ROOT . "/controllers/admin/products.php";
				
				//die();//import one item
				echo "Item {$item[name]} added.<br>";
				}		
		}
		
	function deleteAllUsers($deleteAdmins = false){
		$this->useDatabase($this->destinationDB);
		$query = "SELECT * FROM cscart_users";
		if (!$deleteAdmins)
			$query = $query . " WHERE user_type != 'A'";
		$result = mysqli_query($this->link, $query) or die("Failed select from: cscart_users" . mysqli_error($this->link));
		while ($user = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			if (fn_delete_user($user['user_id']))
				echo "User {$user['user_id']} deleted<br>";
		}	
	}
	
	function importUsers(){
		$this->useDatabase($this->sourceDB);
		$query = "SELECT * FROM members";
		$result = mysqli_query($this->link, $query) or die("Failed to select from: cscart_users" . mysqli_error($this->link, $this->link));
		
		$destLink = mysqli_connect($this->config['db_host'], $this->config['db_user'], $this->config['db_password'])
		or die('Database connection error. ' . mysqli_error($this->link));
		$destQuery = "UPDATE cscart_users SET user_id=0 WHERE user_id=1";	
		$this->useDatabase($this->destinationDB, $destLink);
		mysqli_query($destLink, $destQuery);
		while ($user = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$timestamp = strtotime($user['date']); 
			$user_data = array(
				'user_id' => $user['id'],
				'user_login' => $user['login'],
				'email' => $user['email'],
				'password1' => 'tempPass',
				'password2' => 'tempPass',
				'firstname' => $user['name'],
				'lastname' => $user['surname'],
				'fields' => array(
					35 => $user['phone'],
					61 => $user['from'],
					36 => $user['comment'],
					38 => $user['usercomment'],
					59 => $user['country']
					),
				'b_state' => '',
				'b_zipcode' => $user['index'],
				'b_address' => $user['adress'],
				'b_city' => $user['city'],
				'b_phone' => $user['phone'],
				'b_country' => $user['country'] == 'Россия' ? 'RU' : ''
			);
			$auth = array(
				'ip' => $user['ip']
			);
			fn_update_user('', $user_data, $auth, false, false);
			$destQuery = "UPDATE cscart_users SET timestamp={$timestamp}, password='{$user['pass']}' WHERE user_id={$user['id']}";
			mysqli_query($destLink, $destQuery);
		}
		//Add one admin
		unset($user_data['user_id']);
		$user_data['email'] = 'admin@admin.admin';
		$user_data['user_login'] = 'admin@admin.admin';
		$user_data['password1'] = 'admin';
		$user_data['password2'] = 'admin';
		$user_data['user_type'] = 'A';
		fn_update_user('', $user_data, $auth, false, false);

	}
	
	function deleteAllOrders(){
		$this->useDatabase($this->destinationDB);
		$query = "SELECT * FROM cscart_orders";
		$result = mysqli_query($this->link, $query) or die("Failed select from: cscart_orders" . mysqli_error($this->link));
		while ($user = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			if (fn_delete_order($user['order_id']))
				echo "Order {$user['user_id']} deleted<br>";
		}	
	}
	
	function importOrders(){
		$this->useDatabase($this->sourceDB);
		//main link used for selecting orders
		$query = "SELECT * FROM shop_orders ORDER BY id DESC";
		$result = mysqli_query($this->link, $query) or die('Failed to select items: ' . mysqli_error($this->link));
		
		//lineLink for selecting order lines (ordered products) 
		$lineLink = mysqli_connect($this->config['db_host'], $this->config['db_user'], $this->config['db_password'])
		or die('Database connection error. ' . mysqli_error($this->link));
		$this->useDatabase($this->sourceDB, $lineLink);
		
		//destLink used to update timestamp for migrated orders
		$destLink = mysqli_connect($this->config['db_host'], $this->config['db_user'], $this->config['db_password'])
		or die('Database connection error. ' . mysqli_error($this->link));
		$this->useDatabase($this->destinationDB, $destLink);
		
		while ($order = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
				$products = array();
				$lineResult = mysqli_query($lineLink, "SELECT * FROM shop_cart WHERE order_id = {$order['id']}");
				var_dump(mysqli_error($lineLink));
				while ($line = mysqli_fetch_array($lineResult, MYSQL_ASSOC)){
					echo $line['item_id']."<br>";
					$products[$line['id']] = array(
						'product_id' => $line['item_id'],
						'amount' => $line['quantity'],
						'price' => $line['price_in_order']
					);
				}
				$timestamp = strtotime($order['date']); 
				$cart = array(
				'products' => $products,
				'recalculate' => false,
				'user_data' => array(
					'user_id' => $order['member_id'],
					'firstname' => $order['name'],
					'lastname' => $order['surname'],
					'b_country' => $order['country'] == 'Россия' ? 'RU' : '',
					's_country' => $order['country'] == 'Россия' ? 'RU' : '',
					's_address' => $order['adress'],
					'b_address' => $order['adress'],
					's_zipcode' => $order['index'],
					'b_zipcode' => $order['index'],
					's_city' => $order['city'],
					'b_city' => $order['city'],
					's_phone' => $order['phone'],
					'b_phone' => $order['phone'],
					'email' => $order['email'],
					'fields' => array(
						35 => $order['phone'],
						39 => $order['usercomment'],
						64 => $order['comment']
					),
				),
				'total' => $order['total'],
				'shipping_cost' => $order['delivery_cost'],
				'display_shipping_cost' => $order['delivery_cost'],
				'timestamp' => $timestamp,
				'order_id' => $order['id'],
				'status' => 'C'
				);
				$auth = array(
					'user_id' => $order['member_id']
				);
				if (fn_place_order($cart, $auth)){
					$lineResult = mysqli_query($destLink, "UPDATE cscart_orders SET timestamp={$timestamp}, status = 'C' WHERE order_id = {$order['id']}");
				}
		}
	}

    function setWatermark($watermark){
        if (getimagesize($watermark)){
            $this->watermark = imagecreatefrompng($watermark);
        }

    }

    function addAllWatermarks($folder){
        $files = scandir($folder);
        foreach($files as $fileName) {
            $file = $folder . "/" . $fileName;
            if (is_dir($file) && ($fileName != '.') && ($fileName != '..')){
                $this->addAllWatermarks($file);
                echo $file . "<br>";
            } else if (($fileName != '.') && ($fileName != '..')){
                $this->placeWatermark($file, $this->watermark);
                echo $file . " file <br>";
            }

        }
    }


    function placeWatermark($image, $waterMark){

        $imageType = getimagesize($image);

        if ($imageType){

            $im = imagecreatefromjpeg($image);
            $marge_right = 10;
            $marge_top = 15;

            imagecopy($im, $waterMark, $marge_right, $marge_top, 0, 0, imagesx($waterMark), imagesy($waterMark));

            switch ($imageType[2]){
                case IMG_GIF:
                    imagegif($im, $image);
                    break;
                case IMG_JPG:
                    imagejpeg($im, $image);
                    break;
                case IMG_PNG:
                    imagepng($im, $image);
                    break;
                default:
                    break;
            }
            imagedestroy($im);
        }
    }

	private function useDatabase($dbname, $link = null){
		if (!$link) $link = $this->link;
		$query = "USE {$dbname}";
		$result = mysqli_query($link, $query) or die("Failed use database: {$dbname}" . mysqli_error($this->link, $this->link));
		
		
	}
}
/*,
						product_add_additional_image_data => array(
							0 => array (
								pair_id => '',
								type => 'A',
								object_id => '0',
								image_alt => '',
								detailed_alt => ''
							),
							1 => array (
								pair_id => '',
								type => 'A',
								object_id => '0',
								image_alt => '',
								detailed_alt => ''
							),
							3 => array (
								pair_id => '',
								type => 'A',
								object_id => '0',
								image_alt => '',
								detailed_alt => ''
							)),
						file_product_add_additional_image_icon => array(
							0 => 'product_add_additional',
							1 => '',
							3 => ''
							),
						type_product_add_additional_image_icon => array(
							0 => 'local',
							1 => '',
							3 => ''
							),
						file_product_add_additional_image_detailed => array(
							0 => 'images/import/3_2.jpg',
							1 => 'images/import/3_3.jpg',
							3 => 'images/import/3_4.png'
							),
						type_product_add_additional_image_detailed => array(
							0 => 'server',
							1 => 'server',
							3 => 'server'
							),*/
?>