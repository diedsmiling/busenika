{* $Id: google_actions.tpl 9517 2010-05-19 14:02:43Z klerik $*}

<fieldset>
{include file="common_templates/subheader.tpl" title=$lang.information}

{foreach from=$google_info key="name" item="value"}
<div class="form-field">
	<label>{$lang.$name}:</label>{$value}
</div>
{/foreach}

</fieldset>

{if !$google_info.risk_information}

<form action="{""|fn_url}" method="post" name="google_actions_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<fieldset>
<div class="buttons-container">
{if $google_info.fulfillment_state == "NEW" || $google_info.fulfillment_state == "PROCESSING"}
	{include file="buttons/button.tpl" but_text=$lang.deliver but_name="dispatch[orders.google.deliver]"}&nbsp;&nbsp;&nbsp;
{/if}

{if $google_info.fulfillment_state == "DELIVERED" || $google_info.financial_state == "CANCELLED" || $google_info.financial_state == "CANCELLED_BY_GOOGLE"}
	{include file="buttons/button.tpl" but_text=$lang.archive but_name="dispatch[orders.google.archive]"}&nbsp;&nbsp;&nbsp;
{/if}

{include file="buttons/button.tpl" but_text=$lang.add_tracking_data but_name="dispatch[orders.google.add_tracking_data]"}
</div>
</fieldset>

</form>

{if $google_info.charged_amount < $order_info.total}

<form action="{""|fn_url}" method="post" name="google_charge_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<fieldset>
{include file="common_templates/subheader.tpl" title=$lang.charge}

<div class="form-field">
	<label for="elmg_ca_amount" class="cm-required">{$lang.amount}:</label>
	{math equation="a-b" a=$order_info.total b=$google_info.charged_amount|default:0 assign="amount_to_charge"}
	<input type="text" class="input-text" id="elmg_ca_amount" name="google_data[charge_amount]" size="5" value="{$amount_to_charge}" />
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.charge but_name="dispatch[orders.google.charge]"}
</div>

</fieldset>
</form>
{/if}

{if ($google_info.financial_state == "CHARGED" || $google_info.financial_state == "REFUNDED") && $google_info.refunded_amount != $order_info.total}

<form action="{""|fn_url}" method="post" name="google_refund_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<fieldset>
{include file="common_templates/subheader.tpl" title=$lang.refund}

<div class="form-field">
	<label for="elmg_r_amount" class="cm-required">{$lang.amount}:</label>
	{math equation="a-b" a=$google_info.charged_amount|default:0 b=$google_info.refunded_amount|default:0 assign="amount_to_refund"}
	<input type="text" class="input-text" id="elmg_r_amount" name="google_data[refund_amount]" size="5" value="{$amount_to_refund}" />
</div>
<div class="form-field">
	<label for="elmg_r_reason" class="cm-required">{$lang.reason}:</label>
	<input type="text" class="input-text" id="elmg_r_reason" name="google_data[refund_reason]" size="45" value="" />
</div>

<div class="form-field">
	<label for="elmg_r_comment">{$lang.comments}:</label>
	<input type="text" class="input-text" id="elmg_r_comment" name="google_data[refund_comment]" size="45" value="" />
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.refund but_name="dispatch[orders.google.refund]"}
</div>

</fieldset>
</form>
{/if}

{if (($google_info.refunded_amount && $google_info.refunded_amount == $google_info.charged_amount) || ($google_info.chargeback_amount && $google_info.chargeback_amount == $google_info.charged_amount)) && !($google_info.financial_state == "CANCELLED" || $google_info.financial_state == "CANCELLED_BY_GOOGLE")}

<form action="{""|fn_url}" method="post" name="google_cancel_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<fieldset>
{include file="common_templates/subheader.tpl" title=$lang.cancel}

<div class="form-field">
	<label for="elm_g_c_reason" class="cm-required">{$lang.reason}:</label>
	<input type="text" class="input-text" id="elm_g_c_reason" name="google_data[cancel_reason]" size="45" value="" />
</div>

<div class="form-field">
	<label for="elmg_c_comment">{$lang.comments}:</label>
	<input type="text" class="input-text" id="elmg_c_comment" name="google_data[cancel_comment]" size="45" value="" />
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.cancel but_name="dispatch[orders.google.cancel]"}
</div>

</fieldset>
</form>
{/if}

{include file="common_templates/subheader.tpl" title=$lang.send_message}

<form action="{""|fn_url}" method="post" name="google_message_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<fieldset>
<div class="form-field">
	<label class="cm-required" for="elmg_message">{$lang.message}:</label>
	<textarea class="input-text" id="elmg_message" name="google_data[message]" cols="45" rows="4"></textarea>
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.send_message but_name="dispatch[orders.google.send_message]"}
</div>

</fieldset>
</form>

{/if}
