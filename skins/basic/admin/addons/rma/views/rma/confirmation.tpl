{* $Id: confirmation.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<p>{$lang.text_confirmation_page_header}</p>

{if $change_return_status}
<form action="{""|fn_url}" method="post" name="change_return_status">
<input type="hidden" name="confirmed" value="Y" />
{foreach from=$change_return_status item="value" key="field"}
<input type="hidden" name="change_return_status[{$field}]" value="{$value}" />
{/foreach}

<div>
	{assign var="status_to" value=$change_return_status.status_to}
	{assign var="status_from" value=$change_return_status.status_from}
	{$lang.text_return_change_warning|replace:"[return_id]":$change_return_status.return_id}&nbsp;<strong>{$status_descr.$status_from}&nbsp;&#8212;&#8250;&nbsp;{$status_descr.$status_to}</strong>.
</div>
{if $change_return_status.recalculate_order == "M"}
<div class="form-field">
	<label for="total" class="cm-required">{$lang.order_total_will_changed}:</label>
	<input id="total" type="text" name="change_return_status[total]" value="{$change_return_status.total}" size="5" class="input-text" />
</div>
{elseif $change_return_status.recalculate_order == "R"}

{if $shipping_info}
<div>
	{$lang.shipping_costs_will_changed}:
</div>
{foreach from=$shipping_info item="shipping" key="shipping_id"}
<div class="form-field">
	<label for="sh_{$shipping_id}" class="cm-required">{$shipping.shipping}:</label>
	<input id="sh_{$shipping_id}" type="text" name="change_return_status[shipping_costs][{$shipping_id}]" value="{$shipping.cost}" size="5" class="input-text" />
</div>
{/foreach}
{/if}

{/if}
<p>{$lang.text_are_you_sure_to_proceed}</p>

<div class="buttons-container">	
	{include file="buttons/button.tpl" but_text=$lang.yes but_name="dispatch[rma.update_details]"}
	{include file="buttons/button.tpl" but_text=$lang.no but_onclick="history.go(-1);"}
</div>

</form>
{/if}
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.confirmation_dialog content=$smarty.capture.mainbox}
