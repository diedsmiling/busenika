{* $Id: options_info.tpl 8145 2009-10-28 15:12:09Z alexions $ *}
{if $product_options}
<strong>{$lang.options}:</strong>&nbsp;
{foreach from=$product_options item=po name=po_opt}
	{$po.option_name}:&nbsp;{$po.variant_name}
	
	{if $oi.extra.custom_files[$po.option_id]}
		{foreach from=$oi.extra.custom_files[$po.option_id] item="file" name="po_files"}
			{$file.name}
			{if !$smarty.foreach.po_files.last},&nbsp;{/if}
		{/foreach}
	{/if}
	
	{if $settings.General.display_options_modifiers == "Y"}
		{if !$skip_modifiers && $po.modifier|floatval}
			&nbsp;({include file="common_templates/modifier.tpl" mod_type=$po.modifier_type mod_value=$po.modifier display_sign=true})
		{/if}
	{/if}
	{if !$smarty.foreach.po_opt.last},&nbsp;{/if}
{/foreach}
{else}
	&nbsp;
{/if}
