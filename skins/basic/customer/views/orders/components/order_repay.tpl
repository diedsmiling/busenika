{* $Id: order_repay.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{if $order_info.total|floatval}

{capture name="group"}
	{include file="common_templates/subheader.tpl" title=$lang.repay_order}

	{script src="js/cc_validator.js"}

	<form action="{""|fn_url}" method="post" name="order_repay_form">
	<input type="hidden" name="order_id" value="{$order_info.order_id}" />

	<table cellpadding="0" cellspacing="3" border="0">
	{foreach from=$payment_methods item="pm" name="pay"}
	<tr>
		<td>
			<input type="radio" class="radio" id="payment_method_{$pm.payment_id}" name="payment_id" value="{$pm.payment_id}" {if $order_payment_id == $pm.payment_id || (!$order_payment_id && $smarty.foreach.pay.first)}checked="checked"{/if} onclick="jQuery.redirect('{"orders.details?order_id=`$order_info.order_id`&payment_id=`$pm.payment_id`"|fn_url}');" /></td>
		<td><strong>{$pm.payment}</strong></td>
		<td>&nbsp;</td>
		<td>{$pm.description}</td>
	</tr>
	{/foreach}
	</table>

	{if $payment_method.template}
		<hr />{include file="views/orders/components/payments/`$payment_method.template`" payment_id=$payment_method.payment_id}
	{/if}
	<div class="right{if !$payment_method.surcharge_value} hidden{/if}"><strong>{$lang.payment_surcharge}:</strong>&nbsp;<span>{include file="common_templates/price.tpl" value=$payment_method.surcharge_value class="list_price"}</span></div>

	{include file="views/checkout/components/customer_notes.tpl"}

	<p>
	{if $payment_method.params.button}
		{$payment_method.params.button}
	{else}
		{include file="buttons/button.tpl" but_text=$lang.repay_order but_name="dispatch[orders.repay]" but_role="action"}
	{/if}
	</p>
	</form>
{/capture}
{include file="common_templates/group.tpl"  content=$smarty.capture.group}

{/if}

