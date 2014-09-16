<?php

//
// $Id: func.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

function fn_age_verification_get_products(&$params, &$fields, &$sortings, &$condition, &$join)
{
	$fields[] = "IF(products.age_verification = 'Y', 'Y', IF(?:categories.age_verification = 'Y', 'Y', ?:categories.parent_age_verification)) as age_verification";
	$fields[] = "IF(products.age_limit > ?:categories.age_limit, IF(products.age_limit > ?:categories.parent_age_limit, products.age_limit, ?:categories.parent_age_limit), IF(?:categories.age_limit > ?:categories.parent_age_limit, ?:categories.age_limit, ?:categories.parent_age_limit)) as age_limit";

	if (AREA == 'C') {
		if (!empty($_SESSION['auth']['age'])) {
			if ($_SESSION['auth']['age'] == -1) {
				$condition .= " AND products.age_verification = 'N' AND ?:categories.age_verification = 'N'";
			} else {
				$condition .= db_quote(" AND (products.age_verification = 'N' OR (products.age_verification = 'Y' AND products.age_limit <= ?i)) AND (?:categories.age_verification = 'N' OR (?:categories.age_verification = 'Y' AND ?:categories.age_limit <= ?i)) AND (?:categories.parent_age_verification = 'N' OR (?:categories.parent_age_verification = 'Y' AND ?:categories.parent_age_limit <= ?i))", $_SESSION['auth']['age'], $_SESSION['auth']['age'], $_SESSION['auth']['age']);
			}
		}
	}
}

function fn_age_verification_get_user_info(&$user_data)
{
	if (!empty($user_data['user_id'])) {
		$user_data['birthday'] = db_get_field('SELECT birthday FROM ?:users WHERE user_id = ?i', $user_data['user_id']);
	}
}

function fn_age_verification_get_categories($params, $join, &$condition, $fields, $group_by, $sortings)
{
	$fields[] = '?:categories.age_verification';
	$fields[] = '?:categories.age_limit';
	$fields[] = '?:category_descriptions.age_warning_message';

	if (AREA == 'C') {
		if (!empty($_SESSION['auth']['age'])) {
			if ($_SESSION['auth']['age'] == -1) {
				$condition .= " AND ?:categories.age_verification = 'N'";
			} else {
				$condition .= db_quote(" AND (?:categories.age_verification = 'N' OR (?:categories.age_verification = 'Y' AND ?:categories.age_limit <= ?i))", $_SESSION['auth']['age']);
			}
		}
	}

	return true;
}

function fn_age_verification_category_check($category_id)
{
	if (!empty($_SESSION['auth']['age'])) {
		$age = $_SESSION['auth']['age'];
	} else {
		$age = 0;
	}

	while ($category_id) {
		$data = db_get_row("SELECT category_id, parent_id, age_verification, age_limit FROM ?:categories WHERE category_id = ?i", $category_id);

		if (empty($data)) {
			return array (false, 0);
		}

		if ($data['age_verification'] == 'Y') {
			if (!$age) {
				return array ('form', $data['category_id']);
			} else {
				if ($age < $data['age_limit']) {
					return array ('deny', $data['category_id']);
				}
			}
		}

		$category_id = $data['parent_id'];
	}

	return array (false, 0);
}

function fn_age_verification_update_profile($action, $user_data)
{
	return fn_age_verification_check_age($user_data);
}

function fn_age_verification_check_age($user_data)
{
	if (!empty($user_data['birthday']) && !empty($user_data['status']) && $user_data['status'] == 'A') {
		$year = date('Y', $user_data['birthday']);
		$month = date('m-d', $user_data['birthday']);

		$_year = date('Y', TIME);
		$_month = date('m-d', TIME);

		$age = $_year - $year;

		if ($month > $_month) {
			$age--;
		}

		$_SESSION['auth']['age'] = $age;

		return true;
	}

	return false;
}

?>