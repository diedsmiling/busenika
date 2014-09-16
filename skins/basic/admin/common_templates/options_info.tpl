{* $Id: options_info.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{if $product_options}
<strong>{$lang.options}: </strong>
{strip}
{foreach from=$product_options item=po name=po_opt}
	&nbsp;{$po.option_name}:&nbsp;{$po.variant_name}
	
	{if $oi.extra.custom_files[$po.option_id] || $cp.extra.custom_files[$po.option_id]}
		{foreach from=$oi.extra.custom_files[$po.option_id] item="file" name="po_files"}
			{assign var="filename" value=$file.name|rawurlencode}
			<a href="{"orders.get_custom_file?order_id=`$order_info.order_id`&amp;file=`$file.file`&amp;filename=`$filename`"|fn_url}">{$file.name}</a>
			{if !$smarty.foreach.po_files.last},&nbsp;{/if}
		{foreachelse}
			{foreach from=$cp.extra.custom_files[$po.option_id] item="file" name="po_files"}
				{$file.name}
				{if !$smarty.foreach.po_files.last},&nbsp;{/if}
			{/foreach}
		{/foreach}
	{/if}
	
	{if $settings.General.display_options_modifiers == "Y"}
		{if $po.modifier|floatval}
			&nbsp;({include file="common_templates/modifier.tpl" mod_type=$po.modifier_type mod_value=$po.modifier display_sign=true})
		{/if}
	{/if}
	{if !$smarty.foreach.po_opt.last},{/if}
{/foreach}
{/strip}
{else}
	&nbsp;
{/if}
