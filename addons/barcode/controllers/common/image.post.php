<?php

//
// $Id: image.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'barcode') {
	require(DIR_ADDONS . 'barcode/lib/barcodegenerator/barcode.php');

	$style = BCS_ALIGN_CENTER;
	if (Registry::get('addons.barcode.text') == 'Y') {
		$style = $style + BCS_DRAW_TEXT;
	}
	if (Registry::get('addons.barcode.output') == 'png') {
		$style = $style + BCS_IMAGE_PNG;
	}

	if (Registry::get('addons.barcode.output') == 'jpeg') {
		$style = $style + BCS_IMAGE_JPEG;
	}

	$width = (!empty($_REQUEST['width'])) ? $_REQUEST['width'] : BCD_DEFAULT_WIDTH;
	$height = (!empty($_REQUEST['height'])) ? $_REQUEST['height'] : BCD_DEFAULT_HEIGHT;
	$xres = (!empty($_REQUEST['xres'])) ? $_REQUEST['xres'] : BCD_DEFAULT_XRES;
	$font = (!empty($_REQUEST['font'])) ? $_REQUEST['font'] : BCD_DEFAULT_FONT;
	$id = (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : '';
	$type = (!empty($_REQUEST['type'])) ? $_REQUEST['type'] : '';

	// Define supported barcode types
	$objects = array (
		'I25' => 'I25Object',
		'C39' => 'C39Object',
		'C128A' => 'C128AObject',
		'C128B' => 'C128BObject',
		'C128C' => 'C128CObject',
	);

	// Define barcode types that should have only numeric values
	$numeric_objects = array (
		'I25' => true,
		'C128C' => true,
	);

	if (!empty($objects[$type])) {
		$prefix = Registry::get('addons.barcode.prefix');
		if (!empty($numeric_objects[$type]) && !is_numeric($prefix)) {
			$prefix = '';
		}

		$code = $prefix . $id;
		if (strlen($code) % 2 != 0) {
			$code = $prefix . '0' . $id;
		}

		require(DIR_ADDONS . 'barcode/lib/barcodegenerator/' . strtolower($objects[$type]) . '.php');

		$obj = new $objects[$type]($width, $height, $style, $code);
		if ($obj) {
			$obj->SetFont($font);
			$obj->DrawObject($xres);
			$obj->FlushObject();
			$obj->DestroyObject();
			unset($obj);  /* clean */
		}
	} else {
		__DEBUG__("Need bar code type ex. C39");
	}
	exit;
}
?>