{* $Id: products_list_update.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	{if !$no_pos}
	<th>{$lang.position_short}</th>
	{/if}
	<th width="100%">{$lang.product_name}</th>
</tr>
{foreach from=$products item=product}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%"><input type="checkbox" name="delete[{$product.product_id}]" id="delete_checkbox" value="Y" class="checkbox cm-item" /></td>
	{if !$no_pos}
	<td class="center"><input type="text" name="position[{$product.product_id}]" value="{$product.position}" size="2" class="input-text-short" /></td>
	{/if}
	<td>
		<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a></td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="{if !$no_pos}3{else}2{/if}"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>
