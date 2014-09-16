<?php
// ---------------------------------------------------- Related functions --------------------------------
//
// Add or update product
//
function fn_update_product($product_data, $product_id = 0, $lang_code = CART_LANGUAGE)
{
	$_data = $product_data;

	if (!empty($product_data['timestamp'])) {
		$_data['timestamp'] = fn_parse_date($product_data['timestamp']); // Minimal data for product record
	}

	if (!empty($product_data['avail_since'])) {
		$_data['avail_since'] = fn_parse_date($product_data['avail_since']);
	}

	if (isset($product_data['tax_ids'])) {
		$_data['tax_ids'] = empty($product_data['tax_ids']) ? '' : fn_create_set($product_data['tax_ids']);
	}

	if (isset($product_data['localization'])) {
		$_data['localization'] = empty($product_data['localization']) ? '' : fn_implode_localizations($_data['localization']);
	}
	
	if (isset($product_data['usergroup_ids'])) {
		$_data['usergroup_ids'] = empty($product_data['usergroup_ids']) ? '' : implode(',', $_data['usergroup_ids']);
	}

	if (Registry::get('settings.General.allow_negative_amount') == 'N' && isset($_data['amount'])) {
		$_data['amount'] = abs($_data['amount']);
	}

	// add new product
	if (empty($product_id)) {
		$create = true;
		// product title can't be empty
		if(empty($product_data['product'])) {
			return false;
		}
		echo("data");
		var_dump($data);
		$product_id = db_query("INSERT INTO ?:products ?e", $_data);

		if (empty($product_id)) {
			return false;
		}

		//
		// Adding same product descriptions for all cart languages
		//
		$_data = $product_data;
		$_data['product_id'] =	$product_id;
		$_data['product'] = trim($_data['product'], " -");

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:product_descriptions ?e", $_data);
		}

	// update product
	} else {
		if (isset($product_data['product']) && empty($product_data['product'])) {
			unset($product_data['product']);
		}

		db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", $_data, $product_id);

		$_data = $product_data;
		if (!empty($_data['product'])){
			$_data['product'] = trim($_data['product'], " -");
		}
		db_query("UPDATE ?:product_descriptions SET ?u WHERE product_id = ?i AND lang_code = ?s", $_data, $product_id, $lang_code);
	}

	// Log product add/update
	fn_log_event('products', !empty($create) ? 'create' : 'update', array(
		'product_id' => $product_id
	));

	if (!empty($product_data['product_features'])) {
		$i_data = array(
			'product_id' => $product_id,
			'lang_code' => $lang_code
		);


		foreach ($product_data['product_features'] as $feature_id => $value) {

			// Check if feature is applicable for this product
			$id_paths = db_get_fields("SELECT ?:categories.id_path FROM ?:products_categories LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id WHERE product_id = ?i", $product_id);

			$_params = array(
				'category_ids' => array_unique(explode('/', implode('/', $id_paths))),
				'feature_id' => $feature_id
			);
			list($_feature) = fn_get_product_features($_params);

			if (empty($_feature)) {
				$_feature = db_get_field("SELECT description FROM ?:product_features_descriptions WHERE feature_id = ?i AND lang_code = ?s", $feature_id, CART_LANGUAGE);
				$_product = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, CART_LANGUAGE);
				fn_set_notification('E', fn_get_lang_var('error'), str_replace(array('[feature_name]', '[product_name]'), array($_feature, $_product), fn_get_lang_var('product_feature_cannot_assigned')));
				continue;
			}

			$i_data['feature_id'] = $feature_id;
			unset($i_data['value']);
			unset($i_data['variant_id']);
			unset($i_data['value_int']);
			$feature_type = db_get_field("SELECT feature_type FROM ?:product_features WHERE feature_id = ?i", $feature_id);

			// Delete variants in current language
			if ($feature_type == 'T') {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i AND lang_code = ?s", $feature_id, $product_id, $lang_code);
			} else {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i", $feature_id, $product_id);
			}

			if ($feature_type == 'D') {
				$i_data['value_int'] = fn_parse_date($value);
			} elseif ($feature_type == 'M') {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$value = empty($value) ? array() : $value;
					$value[] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				}
				if (!empty($value)) {
					foreach ($value as $variant_id) {
						foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) { // insert for all languages
							$i_data['variant_id'] = $variant_id;
							db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
						}
					}
				}
				continue;
			} elseif (in_array($feature_type, array('S', 'N', 'E'))) {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$i_data['variant_id'] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				
				} elseif (!empty($value) && $value != 'disable_select') {
					if ($feature_type == 'N') {
						$i_data['value_int'] = db_get_field("SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s", $value, CART_LANGUAGE);
					}
					$i_data['variant_id'] = $value;
				} else {
					continue;
				}
			} else {
				if ($value == '') {
					continue;
				}
				if ($feature_type == 'O') {
					$i_data['value_int'] = $value;
				} else {
					$i_data['value'] = $value;
				}
			}

			if ($feature_type != 'T') { // feature values are common for all languages, except text (T)
				foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) {
					db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
				}
			} else { // for text feature, update current language only
				$i_data['lang_code'] = $lang_code;
				db_query("INSERT INTO ?:product_features_values ?e", $i_data);
			}
		}
	}

	// Update product prices
	if (isset($product_data['price'])) {
		if (!isset($product_data['prices'])) {
			$product_data['prices'] = array();
			$skip_price_delete = true;
		}
		$_price = array (
			'price' => abs($product_data['price']),
			'lower_limit' => 1,
		);

		array_unshift($product_data['prices'], $_price);
	}

	if (!empty($product_data['prices'])) {
		if (empty($skip_price_delete)) {
			db_query("DELETE FROM ?:product_prices WHERE product_id = ?i", $product_id);
		}

		foreach ($product_data['prices'] as $v) {
			if (!empty($v['lower_limit'])) {
				$v['product_id'] = $product_id;
				db_query("REPLACE INTO ?:product_prices ?e", $v);
			}
		}
	}

	if (!empty($product_data['popularity'])) {
		$_data = array (
			'product_id' => $product_id,
			'total' => intval($product_data['popularity'])
		);
		
		db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE total = ?i", $_data, $product_data['popularity']);
	}

	fn_set_hook('update_product', $product_data, $product_id, $lang_code);

	return $product_id;
}

function fn_clone_product($product_id)
{
	// Clone main data
	$data = db_get_row("SELECT * FROM ?:products WHERE product_id = ?i", $product_id);
	unset($data['product_id']);
	$data['status'] = 'D';
	$pid = db_query("INSERT INTO ?:products ?e", $data);

	// Clone descriptions
	$data = db_get_array("SELECT * FROM ?:product_descriptions WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		if ($v['lang_code'] == CART_LANGUAGE) {
			$orig_name = $v['product'];
			$new_name = $v['product'].' [CLONE]';
		}

		$v['product'] .= ' [CLONE]';
		db_query("INSERT INTO ?:product_descriptions ?e", $v);
	}

	// Clone prices
	$data = db_get_array("SELECT * FROM ?:product_prices WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		unset($v['price_id']);
		db_query("INSERT INTO ?:product_prices ?e", $v);
	}

	// Clone categories links
	$data = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i", $product_id);
	$_cids = array();
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		db_query("INSERT INTO ?:products_categories ?e", $v);
		$_cids[] = $v['category_id'];
	}
	fn_update_product_count($_cids);

	// Clone product options
	fn_clone_product_options($product_id, $pid);

	// Clone global linked options
	$gl_options = db_get_fields("SELECT option_id FROM ?:product_global_option_links WHERE product_id = ?i", $product_id);
	if (!empty($gl_options)) {
		foreach ($gl_options as $v) {
			db_query("INSERT INTO ?:product_global_option_links (option_id, product_id) VALUES (?i, ?i)", $v, $pid);
		}
	}

	// Clone product features
	$data = db_get_array("SELECT * FROM ?:product_features_values WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		db_query("INSERT INTO ?:product_features_values ?e", $v);
	}

	// Clone blocks
	fn_clone_block_links('products', $product_id, $pid);

	// Clone addons
	fn_set_hook('clone_product', $product_id, $pid);

	// Clone images
	fn_clone_image_pairs($pid, $product_id, 'product');

	// Clone product files
	if (is_dir(DIR_DOWNLOADS . $product_id)) {
		$data = db_get_array("SELECT * FROM ?:product_files WHERE product_id = ?i", $product_id);
		foreach ($data as $v) {
			$v['product_id'] = $pid;
			$old_file_id = $v['file_id'];
			unset($v['file_id']);
			
			$file_id = db_query("INSERT INTO ?:product_files ?e", $v);
			
			$file_descr = db_get_row("SELECT * FROM ?:product_file_descriptions WHERE file_id = ?i", $old_file_id);
			$file_descr['file_id'] = $file_id;
			
			db_query("INSERT INTO ?:product_file_descriptions ?e", $file_descr);
		}
		
		fn_copy(DIR_DOWNLOADS . $product_id, DIR_DOWNLOADS . $pid);
	}

	fn_build_products_cache(array($pid));
	return array('product_id'=>$pid, 'orig_name'=>$orig_name, 'product'=>$new_name);
}

//
// Product glodal update
//
function fn_global_update($update_data)
{
	$table = $field = $value = $type = array();
	$msg = '';
	$currencies = Registry::get('currencies');

	if (!empty($update_data['product_ids'])) {
		$update_data['product_ids'] = explode(',', $update_data['product_ids']);
	}

	// Update prices
	if (!empty($update_data['price'])) {
		$table[] = '?:product_prices';
		$field[] = 'price';
		$value[] = $update_data['price'];
		$type[] = $update_data['price_type'];

		$msg .= ($update_data['price'] > 0 ? fn_get_lang_var('price_increased') : fn_get_lang_var('price_decreased')) . ' ' . abs($update_data['price']) . ($update_data['price_type'] == 'A' ? $currencies[CART_PRIMARY_CURRENCY]['symbol'] : '%') . '.<br />';
	}

	// Update list prices
	if (!empty($update_data['list_price'])) {
		$table[] = '?:products';
		$field[] = 'list_price';
		$value[] = $update_data['list_price'];
		$type[] = $update_data['list_price_type'];

		$msg .= ($update_data['list_price'] > 0 ? fn_get_lang_var('list_price_increased') : fn_get_lang_var('list_price_decreased')) . ' ' . abs($update_data['list_price']) . ($update_data['list_price_type'] == 'A' ? $currencies[CART_PRIMARY_CURRENCY]['symbol'] : '%') . '.<br />';
	}

	// Update amount
	if (!empty($update_data['amount'])) {

		$table[] = '?:products';
		$field[] = 'amount';
		$value[] = $update_data['amount'];
		$type[] = 'A';

		$table[] = '?:product_options_inventory';
		$field[] = 'amount';
		$value[] = $update_data['amount'];
		$type[] = 'A';

		$msg .= ($update_data['amount'] > 0 ? fn_get_lang_var('amount_increased') : fn_get_lang_var('amount_decreased')) .' ' . abs($update_data['amount']) . '.<br />';
	}

	fn_set_hook('global_update', $table, $field, $value, $type, $msg, $update_data);

	$where = !empty($update_data['product_ids']) ? db_quote(" WHERE product_id IN (?n)", $update_data['product_ids']) : '';

	foreach ($table as $k => $v) {
		$_value = db_quote("?d", $value[$k]);
		$sql_expression = $type[$k] == 'A' ? ($field[$k] . ' + ' . $_value) : ($field[$k] . ' * (1 + ' . $_value . '/ 100)');

		db_query("UPDATE $v SET " . $field[$k] . " = IF($sql_expression < 0, 0, $sql_expression) $where");
	}

	if (empty($update_data['product_ids'])) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('all_products_have_been_updated') . '<br />' . $msg);
	} else {
		$products = fn_get_product_name($update_data['product_ids']);
		$msg = fn_get_lang_var('text_products_updated') . '<br />' . implode('<br />', $products) . '<br /><br />' . $msg;
		fn_set_notification('N', fn_get_lang_var('notice'), $msg);
	}

	return true;
}

function fn_copy_product_files($file_id, $file, $product_id, $var_prefix = 'file')
{
	$revisions = Registry::get('revisions');

	if (!empty($revisions['objects']['product']['tables'])) {
		$revision = true;
	} else {
		$revision = false;
	}

	if ($revision) {
		$filename = $file['name'];

		$i = 1;
		while (is_file(substr(DIR_DOWNLOADS, 0, -1) . ($revision ? '_rev' : '') . '/' . $product_id . '/' . $filename)) {
			$filename = substr_replace($file['name'], sprintf('%03d', $i) . '.', strrpos($file['name'], '.'), 1);
			$i++;
		}
	} else {
		$filename = $file['name'];
	}

	$_data = array();
	$_data[$var_prefix . '_path'] = $filename;
	$_data[$var_prefix . '_size'] = $file['size'];

	list($new_file, $_data[$var_prefix . '_path']) = fn_generate_file_name(substr(DIR_DOWNLOADS, 0, -1) . ($revision ? '_rev' : '') . '/' . $product_id . '/', $_data[$var_prefix . '_path']);
	
	if (fn_copy($file['path'], $new_file) == false) {
		$_msg = fn_get_lang_var('cannot_write_file');
		$_msg = str_replace('[file]', $new_file, $_msg);
		fn_set_notification('E', fn_get_lang_var('error'), $_msg);
		return false;
	}

	db_query('UPDATE ?:product_files SET ?u WHERE file_id = ?i', $_data, $file_id);

	return true;
}

// Add feature variants
function fn_add_feature_variant($feature_id, $variant)
{
	if (empty($variant['variant'])) {
		return false;
	}

	$variant['feature_id'] = $feature_id;
	$variant['variant_id'] = db_query("INSERT INTO ?:product_feature_variants ?e", $variant);

	foreach (Registry::get('languages') as $variant['lang_code'] => $_d) {
		db_query("INSERT INTO ?:product_feature_variant_descriptions ?e", $variant);
	}

	return $variant['variant_id'];
}
?>