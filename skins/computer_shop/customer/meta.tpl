{* $Id: meta.tpl 10042 2010-07-12 14:05:42Z klerik $ *}

{hook name="index:meta"}
{if $display_base_href}
<base href="{$config.current_location}/" />
{/if}
<meta name='yandex-verification' content='798ca436c6ebed88' />
<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.CHARSET}" />
<meta http-equiv="Content-Language" content="{$smarty.const.CART_LANGUAGE|lower}" />
<meta name="description" content="{$meta_description|html_entity_decode|default:$location_data.meta_description}" />
<meta name="keywords" content="{$meta_keywords|default:$location_data.meta_keywords}" />
<link rel='canonical' href='{$config.current_location}{php}$mo=preg_replace("/&page=\d*/","",$_SERVER["REQUEST_URI"]); echo($mo);{/php}' />
{/hook}

