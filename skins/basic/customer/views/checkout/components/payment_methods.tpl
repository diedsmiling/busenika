{* $Id: payment_methods.tpl 10284 2010-07-30 09:08:26Z angel $ *}

{if !$no_mainbox}
	{include file="common_templates/subheader.tpl" title=$lang.select_payment_method anchor="payment_methods"}
{/if}

<div id="payments_summary">
	<table cellpadding="0" cellspacing="0" border="0" id="list_payment_methods" width="100%">
	{hook name="checkout:payment_methods"}
		{foreach from=$payment_methods item="pm" name="pay"}
		<tr>
			<td>
				<input type="radio" id="payment_method_{$pm.payment_id}" {if $pm.disabled}disabled="disabled"{/if} class="radio" onclick="jQuery.ajaxRequest('{"checkout.order_info?payment_id=`$pm.payment_id`"|fn_url:'C':'rel':'&'}', {literal}{method: 'POST', cache: false, result_ids: 'payments_summary'}{/literal});" name="payment_id" value="{$pm.payment_id}" {if $cart.payment_id == $pm.payment_id}{assign var="selected_payment_id" value=$pm.payment_id}{assign var="selected_payment_surcharge_value" value=$pm.surcharge_value|default:"0"}checked="checked"{/if} />
			</td>
			<td><label for="payment_method_{$pm.payment_id}"{if $cart.payment_id == $pm.payment_id} class="strong"{/if}>{$pm.payment}</label></td>
			<td>&nbsp;</td>
			<td>{$pm.description}</td>
		</tr>
		
		{if $selected_payment_id == $pm.payment_id && $payment_method.template}
			<tr><td colspan="4" class="payment-details">
				{assign var="pm_options" value="pm_options_$payment_method.payment_id"}
				{capture name=$pm_options}
					{include file="views/orders/components/payments/`$payment_method.template`" payment_id=$payment_method.payment_id}
				{/capture}
				{if $smarty.capture.$pm_options|trim}
					<div class="payment-details-container">
						{$smarty.capture.$pm_options}
					</div>
				{/if}
			</td></tr>
		{/if}
		{/foreach}
	{/hook}
	</table>
<!--payments_summary--></div>
