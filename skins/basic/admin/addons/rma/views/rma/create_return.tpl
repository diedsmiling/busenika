{* $Id: create_return.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

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

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.sku}</th>
	<th width="100%">{$lang.product}</th>
	<th>{$lang.price}</th>
	<th>{$lang.amount}</th>
	<th>{$lang.reason}</th>
</tr>
{foreach from=$order_info.items item="oi" key="key"}
<tr {cycle values="class=\"table-row\", "}>
	<td width="1%" class="center">
		<input type="checkbox" name="returns[{$oi.cart_id}][chosen]" value="Y" class="checkbox cm-item" />
		<input type="hidden" name="returns[{$oi.cart_id}][product_id]" value="{$oi.product_id}" /></td>
	<td>{$oi.product_code}</td>
	<td><a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{$oi.product}</a>
	{if $oi.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}</td>
	<td class="nowrap">
		{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.price}{/if}</td>
	<td>
		<input type="hidden" name="returns[{$oi.cart_id}][available_amount]" value="{$oi.amount}" />
		<select name="returns[{$oi.cart_id}][amount]">
		{section name=$key loop=$oi.amount+1 start="1" step="1"}
				<option value="{$smarty.section.$key.index}">{$smarty.section.$key.index}</option>
		{/section}
		</select></td>
	<td>
		{if $reasons}
			<select name="returns[{$oi.cart_id}][reason]">
			{foreach from=$reasons item="reason" key="reason_id"}
				<option value="{$reason_id}">{$reason.property}</option>
			{/foreach}
			</select>
		{/if}</td>
</tr>
{/foreach}
</table>

{include file="common_templates/subheader2.tpl" title=$lang.comments}
<textarea name="comment" cols="55" rows="4" class="input-textarea-long"></textarea>

<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_text=$lang.rma_return but_name="dispatch[rma.add_return]" but_meta="cm-process-items" but_role="button_main"}
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.return_registration content=$smarty.capture.mainbox}
