<?php
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
		$this->link = mysql_connect($config['db_host'], $config['db_user'], $config['db_password'])
		or die('Database connection error. ' . mysql_error());
		$this->sourceDB = $sourceDB;
		$this->destinationDB = $destinationDB;
	}
	
	function purgeTable ($tableName){
		$query = "	USE {$destinationDB}
					DELETE FROM {$tableName}";
		return $result = mysql_query($query) or die('Failed to delete table: ' . mysql_error());
	}
	
	function importCategories(){
	$query = "	USE {$sourceDB}
				SELECT * FROM categories";
	$result = mysql_query($query) or die('Failed to select categories: ' . mysql_error());
	while ($category = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$template = array(
					category_id => $category[id],
					category => $category[name],
					parent_id => $category[cat_id],
					description => 'description',
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
				fn_update_category($category_data);
				echo "category {$category[name]} added.";
			}
		}
	}

}

?>