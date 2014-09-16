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
		$this->link = mysql_connect($config['db_host'], 'root', 'arsenal82', true, 65536)
		or die('Database connection error. ' . mysql_error());
		mysql_set_charset( 'utf8' );
		$this->sourceDB = $sourceDB;
		$this->destinationDB = $destinationDB;
	}
	
	function purgeTable ($tableName){
		$query = "USE {$this->destinationDB}";
		$result = mysql_query($query) or die("Failed to delete table: {$tableName}" . mysql_error());
		
		$query = "DELETE FROM {$tableName}";
		$result = mysql_query($query) or die("Failed to delete table: {$tableName}" . mysql_error());
		if ($result){
			echo "Table {$tableName} deleted;";
		}
	}
	
	function importCategories(){
	$query = "USE {$this->sourceDB}";
	$result = mysql_query($query) or die('Failed to select categories: ' . mysql_error());
	$query = "SELECT * FROM categories";
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
				fn_update_category($template);
				echo "category {$category[name]} added.";
			}
		}
}
?>