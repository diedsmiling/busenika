{* $Id: export_items.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{foreach from=$order_certificates item="certificate"}
INVITEM{$_d}{$certificate.gift_cert_code}{$_d}INVENTORY{$_d}GIFT CERTIFICATE {$certificate.gift_cert_code}{$_d}GIFT CERTIFICATE{$_d}{$addons.quickbooks.accnt_product}{$_d}{$addons.quickbooks.accnt_asset}{$_d}{$addons.quickbooks.accnt_cogs}{$_d}{$certificate.amount}{$_d}0{$_d}N
{/foreach}