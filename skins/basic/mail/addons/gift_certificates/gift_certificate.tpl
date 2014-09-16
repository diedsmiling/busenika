{* $Id: gift_certificate.tpl 8243 2009-11-17 08:24:17Z zeke $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$gift_cert_data.recipient},<br /><br />

{$certificate_status.email_header|unescape}<br /><br />

{include file="addons/gift_certificates/templates/`$gift_cert_data.template`"}
	
{include file="letter_footer.tpl"}