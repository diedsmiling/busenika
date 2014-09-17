<?php

include_once DIR_ROOT . "/core/fn.catalog.php";
/*
	$config - database connection configuration array
	$config['db_host'] = 'localhost';
	$config['db_name'] = 'korzin_db';
	$config['db_user'] = 'korzin_user';
	$config['db_password'] = 'kslQi0121oPsl';
	$config['db_type'] = 'mysql';
	
	$sourseDB - name of soure database
	$destinationDB - name of destination database
*/
class ImportTool {

	private $link;
	private $sourceDB;
	private $destinationDB;
	
	function __construct($config, $sourceDB, $destinationDB){
		$this->link = mysql_connect($config['db_host'], 'root', 'arsenal82', true, 65536)
		or die('Database connection error. ' . mysql_error());
		mysql_set_charset( 'utf8' );
		$this->sourceDB = $sourceDB;
		$this->destinationDB = $destinationDB;
	}
	
	function purgeTable ($tableName){
		$query = "USE {$this->destinationDB}";
		$result = mysql_query($query) or die("Failed use database: {$this->destinationDB}" . mysql_error());
		
		$query = "DELETE FROM {$tableName}";
		$result = mysql_query($query) or die("Failed to delete table: {$tableName}" . mysql_error());
		if ($result){
			echo "Table {$tableName} deleted;";
		}
	}
	
	function importCategories(){
	$query = "USE {$this->sourceDB}";
	$result = mysql_query($query) or die('Failed to select categories: ' . mysql_error());
	$query = "SELECT * FROM `categories` ORDER BY `categories`.`cat_id` ASC";
	$result = mysql_query($query) or die('Failed to select categories: ' . mysql_error());
	while ($category = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$template = array(
					category_id => $category[id],
					category => $category[name],
					parent_id => $category[cat_id],
					description => '',
					status => 'A',
					page_title => '',
					meta_description => '',
					meta_keywords => '',
					usergroup_ids => '',
					position => $category[position],
					timestamp => date('j/m/Y'),   //'14/09/2014'
					product_details_layout => 'default',
					use_cusom_templates => 'N',
					discussion_type => 'D'		
					);
				fn_update_category($template);
				echo "category {$category[name]} {$category[cat_id]} {$category[position]} added.<br>";
			}
	}

	function deleteAllProducts(){
		$query = "USE {$this->destinationDB}";
		$result = mysql_query($query) or die("Failed use database: {$this->destinationDB}" . mysql_error());
		
		$query = "SELECT product_id FROM cscart_products";
		$result = mysql_query($query) or die("Failed to delete table: {$tableName}" . mysql_error());
		while ($item = mysql_fetch_array($result, MYSQL_ASSOC)) {
			fn_delete_product($item['product_id']);
			echo "Item {$item['product_id']} deleted<br>";
		}
	}
	
	function importProducts(){
		$query = "USE {$this->sourceDB}";
		$result = mysql_query($query) or die('Failed open database: ' . mysql_error());
		$query = "SELECT * FROM shop_items";
		$result = mysql_query($query) or die('Failed to select items: ' . mysql_error());
		while ($item = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$imageList = unserialize($item['file']);
				$_REQUEST = array(
						fake => '1',
						selected_section => 'images',
						//product_id => $item['id'] ,
						product_data => array(
							product_id => $item['id'] ,
							product => $item['name'],
							main_category => $item['cat_id'],
							price => $item['price'],
							full_description => $item['description'],
							status => 'A',
							amount => $item['quantity'],
							timestamp => $item['date'],
							product_features => array(
								20 => $item['color']
								)
							),
						product_main_image_data => array(
							0 => array (
								pair_id => '',
								type => 'A',
								object_id => '',
								image_alt => '',
								detailed_alt => ''
							)),
						file_product_main_image_icon => array(
							0 => 'product_main'
							),
						type_product_main_image_icon => array(
							0 => 'local'
							),
						file_product_main_image_detailed => array(
							0 => 'images/import/import111.jpg'
							),
						type_product_main_image_detailed => array(
							0 => 'server'
							),
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
							0 => 'images/import/import111.jpg',
							1 => 'images/import/import112.jpg',
							3 => 'images/import/import113.jpg'
							),
						type_product_add_additional_image_detailed => array(
							0 => 'server',
							1 => 'server',
							3 => 'server'
							),
						);
				$_SERVER['REQUEST_METHOD'] = 'POST';
				$mode = 'add';
				var_dump($_REQUEST);
				include DIR_ROOT . "/controllers/admin/products.php";

				die();//import one item
				echo "Item {$item[name]} added.<br>";
				}
		die();
		
		}
}
?>