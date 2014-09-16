{* $Id: js_product.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $type == "options"}
<tr {if !$clone}id="{$root_id}_{$delete_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
{if $position_field}<td><input type="text" name="{$input_name}[{$delete_id}]" value="{math equation="a*b" a=$position b=10}" size="3" class="input-text-short" {if $clone}disabled="disabled"{/if} /></td>{/if}
<td>
	<ul>
		<li>{$product}</li>
		{if $options}
		<li>{$options}</li>
		{/if}
	</ul>
	{if $options_array|is_array}
		{foreach from=$options_array item="option" key="option_id"}
		<input type="hidden" name="{$input_name}[product_options][{$option_id}]" value="{$option}"{if $clone} disabled="disabled"{/if} />
		{/foreach}
	{/if}
	{if $product_id}
		<input type="hidden" name="{$input_name}[product_id]" value="{$product_id}"{if $clone} disabled="disabled"{/if} />
	{/if}
	{if $amount_input == "hidden"}
	<input type="hidden" name="{$input_name}[amount]" value="{$amount}"{if $clone} disabled="disabled"{/if} />
	{/if}
</td>
	{if $amount_input == "text"}
<td>
	<input type="text" name="{$input_name}[amount]" value="{$amount}" size="3" class="input-text-short"{if $clone} disabled="disabled"{/if} />
</td>
	{/if}
	
	{hook name="product_picker:table_column_options"}
	{/hook}
	
<td class="nowrap">
	{if !$hide_delete_button}
		{capture name="tools_items"}
		<li><a onclick="jQuery.delete_js_item('{$root_id}', '{$delete_id}', 'p'); return false;">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$category_id tools_list=$smarty.capture.tools_items}
	{else}&nbsp;{/if}
</td>
</tr>

{elseif $type == "product"}
	<tr {if !$clone}id="{$root_id}_{$delete_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
		{if $position_field}<td><input type="text" name="{$input_name}[{$delete_id}]" value="{math equation="a*b" a=$position b=10}" size="3" class="input-text-short" {if $clone}disabled="disabled"{/if} /></td>{/if}
		<td><a href="{"products.update?product_id=`$delete_id`"|fn_url}">{$product|escape}</a></td>
		<td>&nbsp;</td>
		<td class="nowrap">{if !$hide_delete_button}
			{capture name="tools_items"}
			<li><a onclick="jQuery.delete_js_item('{$root_id}', '{$delete_id}', 'p'); return false;">{$lang.delete}</a></li>
			{/capture}
			{include file="common_templates/table_tools_list.tpl" prefix=$category_id tools_list=$smarty.capture.tools_items href="products.update?product_id=`$delete_id`"}
		{else}&nbsp;{/if}</td>
	</tr>
{/if}
