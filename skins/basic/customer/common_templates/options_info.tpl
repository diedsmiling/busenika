{* $Id: options_info.tpl 10414 2010-08-13 05:54:32Z alexions $ *}

{if $product_options}
	{if !$no_block}
	<div class="product-list-field">
		<label>{$lang.options}:</label>
	{/if}
		{strip}
		{foreach from=$product_options item=po name=po_opt}
			{if $po.variants}
				{assign var="var" value=$po.variants[$po.value]}
			{else}
				{assign var="var" value=$po}
			{/if}
			
			{$po.option_name}: {if !$product.extra.custom_files[$po.option_id]}{$var.variant_name|default:$var.value}{/if}
			
			{if $product.extra.custom_files[$po.option_id]}
				{foreach from=$product.extra.custom_files[$po.option_id] item="file" name="po_files"}
					<a href="{"orders.get_custom_file?order_id=`$order_info.order_id`"|fn_url}&amp;file={$file.file}&amp;filename={$file.name|rawurlencode}" title="{$file.name}">{$file.name|truncate:"40"}</a>
					{if !$smarty.foreach.po_files.last}, {/if}
				{/foreach}
			{/if}
			
			{if $settings.General.display_options_modifiers == "Y"}
				{if $var.modifier|floatval}
					&nbsp;({include file="common_templates/modifier.tpl" mod_type=$var.modifier_type mod_value=$var.modifier display_sign=true})
				{/if}
			{/if}
			{if !$smarty.foreach.po_opt.last}, {/if}
			{if $fields_prefix}<input type="hidden" name="{$fields_prefix}[{$po.option_id}]" value="{$po.value}" />{/if}
		{/foreach}
		{/strip}
	{if !$no_block}
	</div>
	{/if}
{/if}