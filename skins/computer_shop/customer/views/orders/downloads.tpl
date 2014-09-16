{* $Id: downloads.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $products}

	{include file="common_templates/pagination.tpl"}

	{foreach from=$products item=dp}
	<a name="{$dp.order_id}_{$dp.product_id}"></a>
	{include file="views/products/download.tpl" product=$dp no_capture=true}
	{/foreach}

	{include file="common_templates/pagination.tpl"}

{else}
	<p class="no-items">{$lang.text_downloads_empty}</p>
{/if}

{capture name="mainbox_title"}{$lang.downloads}{/capture}
