<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['gift_certificates.update'] = array(
	'func' => array('fn_get_gift_certificate_name', '@gift_cert_id'),
	'text' => 'certificate'
);

?>
