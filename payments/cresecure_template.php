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
// $Id: cresecure_template.php 10229 2010-07-27 14:21:39Z 2tl $
//

define('AREA', 'C');
define('AREA_NAME' ,'customer');
define('SKIP_SESSION_VALIDATION', true);

require './../prepare.php';

$_REQUEST['dispatch'] = 'checkout.cresecure_template';

require './../init.php';

define('INDEX_SCRIPT', Registry::get('config.customer_index'));

fn_run_controller(DIR_ROOT . '/controllers/customer/init.php');

$view = & Registry::get('view');

$view->assign('display_base_href', true);

fn_add_breadcrumb(fn_get_lang_var('payment_information'));

$view->assign('content_tpl', 'views/orders/processors/cresecure.tpl');

$view->display(Registry::get('root_template'));
?>