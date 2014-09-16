{* $Id: cart_content.tpl 9794 2010-06-15 10:51:58Z alexions $ *}

{assign var="result_ids" value="cart_items,checkout_totals,checkout_steps,cart_status"}

<form name="checkout_form" class="cm-check-changes" action="{""|fn_url}" method="post" enctype="multipart/form-data">
<input type="hidden" name="redirect_mode" value="cart" />
<input type="hidden" name="result_ids" value="{$result_ids}" />

{include file="views/checkout/components/cart_items.tpl" disable_ids="button_cart"}

<div class="cart-buttons clear">
	<div class="float-left">{include file="buttons/clear_cart.tpl" but_href="checkout.clear" but_role="text" but_meta="cm-confirm"}</div>
	<div class="float-right">{include file="buttons/update_cart.tpl" but_id="button_cart" but_name="dispatch[checkout.update]"}</div>
</div>

</form>

{include file="views/checkout/components/checkout_totals.tpl" location="cart"}

<div class="buttons-container clear">
	<div class="float-left">
		{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script}</div>
	<div class="float-right right">
	{if $payment_methods}
		{assign var="m_name" value="checkout"}
		{assign var="link_href" value="checkout.checkout"}
		{include file="buttons/checkout.tpl" but_href=$link_href}
	{/if}
	{if $checkout_add_buttons}
		{foreach from=$checkout_add_buttons item="checkout_add_button"}
			<p>{$checkout_add_button}</p>
		{/foreach}
	{/if}
	</div>
</div>
