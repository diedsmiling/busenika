{* $Id: downloads.tpl 6986 2009-03-10 13:35:00Z zeke $ *}

{if $products}

	<p><a href="{"orders.downloads"|fn_url}">{$lang.all_downloads}</a> | <a href="{"orders.details?order_id=`$smarty.request.order_id`"|fn_url}">{$lang.order} #{$smarty.request.order_id}</a></p>

	{foreach from=$products item=dp}
	{include file="views/products/download.tpl" product=$dp no_capture=true hide_order=true}
	{/foreach}

{else}
	<p class="no-items">{$lang.text_downloads_empty}</p>
{/if}

{capture name="mainbox_title"}{$lang.downloads}: {$lang.order|lower} #{$smarty.request.order_id}{/capture}
