{* $Id: products_update_qty_discounts.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{script src="js/picker.js"}
{assign var="usergroups" value="C"|fn_get_usergroups}
<div id="content_qty_discounts" class="hidden">
	<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
	<tbody class="cm-first-sibling">
	<tr>
		<th>{$lang.quantity}</th>
		<th>{$lang.price}&nbsp;({$currencies.$primary_currency.symbol})</th>
				<th width="100%">{$lang.usergroup}</th>
				<th width="1%">&nbsp;</th>
	</tr>
	</tbody>
	<tbody>
	{foreach from=$product_data.prices item="price" key="_key" name="prod_prices"}
	<tr class="cm-row-item">
		<td>
			{if $price.lower_limit == "1" && $price.usergroup_id == "0"}
				&nbsp;{$price.lower_limit}
			{else}
			<input type="text" name="product_data[prices][{$_key}][lower_limit]" value="{$price.lower_limit}" class="input-text-short" />
			{/if}</td>
		<td>
			{if $price.lower_limit == "1" && $price.usergroup_id == "0"}
				&nbsp;{$price.price|default:"0.00"}
			{else}
			<input type="text" name="product_data[prices][{$_key}][price]" value="{$price.price|default:"0.00"}" size="10" class="input-text-medium" />
			{/if}</td>
				<td>
			{if $price.lower_limit == "1" && $price.usergroup_id == "0"}
				&nbsp;{$lang.all}
			{else}
			<select id="usergroup_id" name="product_data[prices][{$_key}][usergroup_id]">
				{foreach from=""|fn_get_default_usergroups item="usergroup"}
					<option {if $price.usergroup_id == $usergroup.usergroup_id}selected="selected"{/if} value="{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</option>
				{/foreach}
				{foreach from=$usergroups item="usergroup"}
					<option {if $price.usergroup_id == $usergroup.usergroup_id}selected="selected"{/if} value="{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</option>
				{/foreach}
			</select>
			{/if}</td>
				<td class="nowrap">
			{if $price.lower_limit == "1" && $price.usergroup_id == "0"}
			&nbsp;{else}
			{include file="buttons/clone_delete.tpl" microformats="cm-delete-row" no_confirm=true}
			{/if}
		</td>
	</tr>
	{/foreach}
	{math equation="x+1" x=$_key|default:0 assign="new_key"}
	<tr {cycle values="class=\"table-row\", " reset=1} id="box_add_qty_discount">
		<td>
			<input type="text" name="product_data[prices][{$new_key}][lower_limit]" value="" class="input-text-short" /></td>
		<td>
			<input type="text" name="product_data[prices][{$new_key}][price]" value="0.00" size="10" class="input-text-medium" /></td>
				<td>
			<select id="usergroup_id" name="product_data[prices][{$new_key}][usergroup_id]">
				{foreach from=""|fn_get_default_usergroups item="usergroup"}
					<option value="{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</option>
				{/foreach}
				{foreach from=$usergroups item="usergroup"}
					<option value="{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</option>
				{/foreach}
			</select>
		</td>
				<td class="right">
			{include file="buttons/multiple_buttons.tpl" item_id="add_qty_discount"}
		</td>
	</tr>
	</tbody>
	</table>

</div>
