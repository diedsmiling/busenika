<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: products.php 10430 2010-08-17 08:24:19Z andyye $
//
global $god;
if ( !defined('AREA') ) { die('Access denied'); }

//
// Search products
//
if ($mode == 'search') {
	

	$params = $_REQUEST;

	if (!empty($params['search_performed']) || !empty($params['features_hash'])) {

		fn_add_breadcrumb(fn_get_lang_var('advanced_search'), "products.search" . (!empty($_REQUEST['advanced_filter']) ? '?advanced_filter=Y' : ''));
		fn_add_breadcrumb(fn_get_lang_var('search_results'));
		$params = $_REQUEST;
		$params['type'] = 'extended';
		list($products, $search, $products_count) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));

		if (!empty($products)) {
			foreach ($products as $k => $v) {
				fn_gather_additional_product_data($products[$k], true, true);
			}
		}

		if (!empty($products)) {
			$_SESSION['continue_url'] = Registry::get('config.current_url');
		}

		$selected_layout = fn_get_products_layout($params);

		$view->assign('products', $products);
		$view->assign('search', $search);
		$view->assign('product_count', $products_count);
		$view->assign('selected_layout', $selected_layout);
	} else {
		fn_add_breadcrumb(fn_get_lang_var('advanced_search'));
	}
	if (!empty($params['advanced_filter'])) {

		$params['get_all'] = 'Y';

		list($filters, $view_all_filter) = fn_get_filters_products_count($params);
		$view->assign('filter_features', $filters);
		$view->assign('view_all_filter', $view_all_filter);
	}
//
// View product details
//
} elseif ($mode == 'view') {

	$_REQUEST['product_id'] = empty($_REQUEST['product_id']) ? 0 : $_REQUEST['product_id'];
	$product = fn_get_product_data($_REQUEST['product_id'], $auth, CART_LANGUAGE, '', true, true, true, true, ($auth['area'] == 'A' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'preview'));
	if (empty($product)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	if (empty($_SESSION['current_category_id']) || empty($product['category_ids'][$_SESSION['current_category_id']])) {
		$_SESSION['current_category_id'] = $product['main_category'];
	}

	if (!empty($product['meta_description']) || !empty($product['meta_keywords'])) {
		$view->assign('meta_description', $product['meta_description']);
		$view->assign('meta_keywords', $product['meta_keywords']);

	} else {
		$view->assign('meta_keywords',  $product['page_title'].' - '.$product['price'].' руб.');
		$view->assign('meta_description', fn_generateMetaDesc($product));
		// $meta_tags = db_get_row("SELECT meta_description, meta_keywords FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $_SESSION['current_category_id'], CART_LANGUAGE);
		// if (!empty($meta_tags)) {
			// $view->assign('meta_description', $meta_tags['meta_description']);
			// $view->assign('meta_keywords', $meta_tags['meta_keywords']);
		// }
		
	}

	$_SESSION['continue_url'] = "categories.view?category_id=$_SESSION[current_category_id]";

	$parent_ids = explode('/', db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $_SESSION['current_category_id']));

	if (!empty($parent_ids)) {
		$cats = fn_get_category_name($parent_ids);
		foreach($parent_ids as $c_id) {
			fn_add_breadcrumb($cats[$c_id], "categories.view?category_id=$c_id");
		}
	}

	fn_add_breadcrumb($product['product']);

	if (!empty($_REQUEST['combination'])) {
		$product['combination'] = $_REQUEST['combination'];
	}
	
	fn_gather_additional_product_data($product, true, true);
	//var_dump($product);
	//die();
	$view->assign('product', $product);
	$amount = db_get_field("SELECT count(*) FROM `cscart_discussion_messages` cm LEFT OUTER JOIN `cscart_discussion` cd ON cm.thread_id = cd.thread_id 
							WHERE cd.object_id = '".$product['product_id']."' AND cd.object_type = 'P'");
	$view->assign('postCount', $amount);
	// If page title for this product is exist than assign it to template
	if (!empty($product['page_title'])) {
		$view->assign('page_title', $product['page_title'].' - '.$product['price'].' руб.');
		//var_dump($product);
	}

	$files = fn_get_product_files($_REQUEST['product_id'], true);

	if (!empty($files)) {
		$view->assign('files', $files);
	}

	/* [Block manager tabs] */
	$_blocks = $view->get_var('blocks');
	foreach ($_blocks as $block) {
		if (!empty($block['text_id']) && $block['text_id'] == 'product_details') {
			$tabs_group_id = $block['block_id'];
			break;
		}
	}
	if (!empty($tabs_group_id)) {
		$view->assign('tabs_block_id', $tabs_group_id);
		foreach ($_blocks as $block) {
			if (!empty($block['group_id']) && $block['group_id'] == $tabs_group_id) {
				Registry::set('navigation.tabs.block_' . $block['block_id'], array (
					'title' => $block['description'],
					'js' => true
				));
			}
		}
	}
	/* [/Block manager tabs] */

	// Set recently viewed products history
	if (!empty($_SESSION['recently_viewed_products'])) {
		$recently_viewed_product_id = array_search($_REQUEST['product_id'], $_SESSION['recently_viewed_products']);
		// Existing product will be moved on the top of the list
		if ($recently_viewed_product_id !== FALSE) {
			// Remove the existing product to put it on the top later
			unset($_SESSION['recently_viewed_products'][$recently_viewed_product_id]);
			// Re-sort the array
			$_SESSION['recently_viewed_products'] = array_values($_SESSION['recently_viewed_products']);
		}
		array_unshift($_SESSION['recently_viewed_products'], $_REQUEST['product_id']);
	} elseif (empty($_SESSION['recently_viewed_products'])) {
		$_SESSION['recently_viewed_products'] = array($_REQUEST['product_id']);
	}

	if (count($_SESSION['recently_viewed_products']) > MAX_RECENTLY_VIEWED) {
		array_pop($_SESSION['recently_viewed_products']);
	}

	// Increase product popularity
	if (empty($_SESSION['products_popularity']['viewed'][$_REQUEST['product_id']])) {
		$_data = array (
			'product_id' => $_REQUEST['product_id'],
			'viewed' => 1,
			'total' => POPULARITY_VIEW
		);
		
		db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE viewed = viewed + 1, total = total + ?i", $_data, POPULARITY_VIEW);
		
		$_SESSION['products_popularity']['viewed'][$_REQUEST['product_id']] = true;
	}
} elseif ($mode == 'disc') {
	$idRow = db_get_fields("SELECT product_id FROM cscart_products");
	
	foreach($idRow as $id){		
		$q = db_get_fields("SELECT * FROM  `cscart_discussion` WHERE object_id = $id");				
		if(empty($q)) 
			db_query("INSERT INTO `cscart_discussion` (`thread_id`, `object_id`, `object_type`, `type`) VALUES (NULL, '$id', 'P', 'B');");			
		unset($q);	
	}
	
	
	die('aa');
} elseif ($mode == 'autocomplete') {
	ini_set('display_errors', 2);
	
	
	$mail             = new PHPMailer1();

$body             =  '<b>ss</b>';

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "lindero.ru"; // SMTP server
$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->Host       = "lindero.ru"; // sets the SMTP server

$mail->Username   = "developer@lindero.ru"; // SMTP account username
$mail->Password   = "100mhz";        // SMTP account password

$mail->SetFrom('name@yourdomain.com', 'First Last');

$mail->AddReplyTo("name@yourdomain.com","First Last");

$mail->Subject    = "PHPMailer Test Subject via smtp, basic with authentication";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$address = "diedsmiling@gmail.com";
$mail->AddAddress($address, "John Doe");

$mail->AddAttachment("images/phpmailer.gif");      // attachment
$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}
         
	
	$view->display('/views/products/autocomplete.tpl');
	
}elseif ($mode == 'ajax_search') {
	//ini_set('display_errors', 2);
	//var_dump($_GET['q']);
	$key = mysql_real_escape_string($_GET['q']);
	$result = array();
	$product_id_row = db_get_fields("SELECT product_id, MATCH(product) AGAINST ('$key' IN BOOLEAN MODE) AS relevance FROM  `cscart_product_descriptions` WHERE MATCH(product) AGAINST ('$key' IN BOOLEAN MODE) AND `lang_code` = 'RU' ORDER BY relevance DESC LIMIT 8");
		
	
	foreach($product_id_row as $product){
	
		$product_name = fn_get_product_name($product);
		$product_amount = db_get_fields("SELECT amount, list_price FROM cscart_products WHERE `product_id` = $product");
		if($product_amount[0] > 0) $am = 'availability_ok'; else $am = 'availability_no';
		$product_price = db_get_fields("SELECT price FROM cscart_product_prices WHERE `product_id` = $product");
		
		$product_img= fn_get_image_pairs($product, 'product', 'M', true, true, CART_LANGUAGE);
		
		$result[] = $product_name.'@'.$am.'@'.$product_price[0].'@'.$product_img['icon']['image_path'].'@'.$product;	

	}	

	foreach($result as $a)
	{
		echo $a. "\n";	
	}
	
	die();
	
}elseif ($mode == 'parseup'){
	$result = db_get_fields("SELECT product_code FROM cscart_products WHERE `product_id` IN (31439,31466,31419,32685,31554,36226,33359,32025,34705,34703,31122,32796,32797,32800,30770,32359,32357,32908,32895,33011,32792,34166,32284,35041,35136,35127,33124,33157,34484,34352,33782,33799,34344,33232,33241,33150,34523,34482,35176,35175,35179,35181,35184,35183,35186,35185,35318,35386)");
	foreach($result as $r){
	echo $r; echo ';'; echo '1';echo '<br/>';	
		
	}	
	die('a');
	
}		
/**
 * Generates meta description based on product info;
 * 
 * @param array $product product data
 * @return string;
 */
function fn_generateMetaDesc($product){
	global $god;
	
	/* schema */
	$schema = array(
		'title' => array(
			'key' => 'product',
			'prefix' => '',
			'postfix' => ' - ',
		),
		'price' => array(
			'key' => 'price',
			'prefix' => '',
			'postfix' => ' руб.,',
		),
		
	);
	
	/* generate string */
	$string = '';
	
	foreach($schema as $item){
		if(!empty($product[$item['key']]))
			$string .= $item['prefix'].$product[$item['key']].$item['postfix'];	
	}
 
	return $string;

}


?>
