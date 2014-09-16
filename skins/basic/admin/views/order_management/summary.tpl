{* $Id: summary.tpl 10581 2010-09-02 12:46:41Z 2tl $ *}

{capture name="mainbox"}

{include file="views/order_management/components/orders_header.tpl"}
{include file="views/profiles/components/profiles_info.tpl" user_data=$cart.user_data location="I"}

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
	<td width="50%">
		{if $payment_method.payment}
			{include file="common_templates/subheader.tpl" title=$lang.payment_method}
			<div class="details-block">
				{$payment_method.payment}&nbsp;<a href="{"order_management.totals"|fn_url}">[{$lang.change}]</a>
			</div>
		{else}
			&nbsp;
		{/if}
	</td>
	<td class="details-block-container" width="50%">
		{if $cart.shipping}
			{include file="common_templates/subheader.tpl" title=$lang.shipping_method}
			<div class="details-block">
				{foreach from=$cart.shipping item="m" name="sh"}{$m.shipping}{if !$smarty.foreach.sh.last},&nbsp;{/if}{/foreach}&nbsp;<a href="{"order_management.totals"|fn_url}">[{$lang.change}]</a>
			</div>
		{else}
			&nbsp;
		{/if}
	</td>
</tr>
</table>


{* Payment methods form *}
<form action="{""|fn_url}" method="post" name="om_summary" class="cm-form-highlight">

{if $payment_method.template}
	{include file="common_templates/subheader.tpl" title=$lang.payment_details}

	{script src="js/cc_validator.js"}
	{include file="views/orders/components/payments/`$payment_method.template`" payment_id=$payment_method.payment_id skin_area="customer"}
{/if}

<div class="form-field">
	<label for="customer_notes">{$lang.text_customer_notes}:</label>
	<textarea class="input-textarea-long" name="customer_notes" id="customer_notes" cols="45" rows="8">{$cart.notes}</textarea>
</div>

<div class="select-field notify-customer">
	<input type="checkbox" name="notify_user" id="notify_user" value="Y" class="checkbox" />
	<label for="notify_user">{$lang.notify_customer}</label>
</div>

<div class="select-field notify-department">
	<input type="checkbox" name="notify_department" id="notify_department" value="Y" class="checkbox" />
	<label for="notify_department">{$lang.notify_orders_department}</label>
</div>
{if $cart.have_suppliers == "Y"}
<div class="select-field notify-department">
	<input type="checkbox" name="notify_supplier" id="notify_supplier" value="Y" class="checkbox" />
	<label for="notify_supplier">{$lang.notify_supplier}</label>
</div>
{/if}
<div class="buttons-container buttons-bg center">
	{if $cart.order_id == ""}
		{assign var="_but_text" value=$lang.create}
		{assign var="but_text_" value=$lang.create_process_payment}
		{assign var="_title" value=$lang.create_new_order}
	{else}
		{assign var="_but_text" value=$lang.save}
		{assign var="but_text_" value=$lang.save_process_payment}
		{assign var="_title" value="`$lang.editing_order`:&nbsp;#`$cart.order_id`"}
	{/if}
	
	{include file="buttons/button.tpl" but_text=$_but_text but_name="dispatch[order_management.place_order.save]" but_role="big" but_meta="no-margin cm-skip-validation"}
	{include file="buttons/button.tpl" but_text=$but_text_ but_name="dispatch[order_management.place_order]" but_rev="om_summary" but_role="big" but_meta="button-left-margin"}
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}
