{* $Id: create_return.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<form action="{""|fn_url}" method="post" name="return_registration_form">
<input name="order_id" type="hidden" value="{$smarty.request.order_id}" />
<input name="user_id" type="hidden" value="{$order_info.user_id}" />

{if $actions}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="nowrap"><strong>{$lang.what_you_would_like_to_do}</strong>:</td>
	<td>&nbsp;&nbsp;</td>
	<td width="100%">
		<select name="action">
		{foreach from=$actions item="action" key="action_id"}
			<option value="{$action_id}">{$action.property}</option>
		{/foreach}
		</select></td>
</tr>
</table>
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table{if $actions} margin-top{/if}">
<tr>
	<th><input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.product}</th>
	<th>{$lang.price}</th>
	<th>{$lang.quantity}</th>
	<th>{$lang.reason}</th>
</tr>
{foreach from=$order_info.items item="oi" key="key"}
<tr {cycle values=",class=\"table-row\""}>
	<td width="1%">
		<input type="checkbox" name="returns[{$oi.cart_id}][chosen]" id="delete_checkbox" value="Y" class="checkbox cm-item" />
		<input type="hidden" name="returns[{$oi.cart_id}][product_id]" value="{$oi.product_id}" /></td>
	<td>&nbsp;<a href="{"products.view?product_id=`$oi.product_id`"|fn_url}">{$oi.product}</a>
		{if $oi.product_options}
			{include file="common_templates/options_info.tpl" product_options=$oi.product_options}
		{/if}</td>
	<td class="right nowrap">
		{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.price}{/if}</td>
	<td class="center">
		<input type="hidden" name="returns[{$oi.cart_id}][available_amount]" value="{$oi.amount}" />
		<select name="returns[{$oi.cart_id}][amount]">
		{section name=$key loop=$oi.amount+1 start="1" step="1"}
				<option value="{$smarty.section.$key.index}">{$smarty.section.$key.index}</option>
		{/section}
		</select></td>
	<td class="center">
		{if $reasons}
			<select name="returns[{$oi.cart_id}][reason]">
			{foreach from=$reasons item="reason" key="reason_id"}
				<option value="{$reason_id}">{$reason.property}</option>
			{/foreach}
			</select>
		{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="6"><p class="no-items">{$lang.no_items}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="6">&nbsp;</td>
</tr>
</table>

{include file="common_templates/subheader2.tpl" title=$lang.comments}
<textarea name="comment" cols="55" rows="4" class="input-textarea-long"></textarea>
<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.rma_return but_name="dispatch[rma.add_return]" but_meta="cm-process-items"}
</div>
</form>

{capture name="mainbox_title"}{$lang.return_registration}{/capture}
