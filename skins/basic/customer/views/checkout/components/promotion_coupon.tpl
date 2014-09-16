{* $Id: promotion_coupon.tpl 10081 2010-07-15 13:25:17Z alexions $ *}

{if $settings.General.one_page_checkout != "Y"}
	{assign var="additional_ids" value=",step_three"}
{/if}

<div class="cm-tools-list right">
<form {if $use_ajax}class="cm-ajax"{/if} name="coupon_code_form" action="{""|fn_url}" method="post">
<input type="hidden" name="redirect_mode" value="{$location}" />
<input type="hidden" name="result_ids" value="checkout_steps,cart_status{$additional_ids}" />

<div class="form-field">
	<strong>{$lang.discount_coupon_code}:</strong><label for="coupon_field" class="hidden cm-required">{$lang.discount_coupon_code}</label>
	<input type="text" class="input-text" id="coupon_field" name="coupon_code" size="40" value="" />
	<input type="submit" class="hidden" name="dispatch[checkout.apply_coupon]" value="" />
	{include file="buttons/button.tpl" but_role="text" but_name="dispatch[checkout.apply_coupon]" but_text=$lang.apply but_rev="coupon_code_form"}
</div>

</form>

{if $cart.coupons|floatval}
	{foreach from=$cart.coupons item="coupon" key="coupon_code"}
	<li>
		<span>{$lang.coupon} "{$coupon_code}"
		{assign var="_redirect_url" value=$config.current_url|escape:url}
		{if $use_ajax}{assign var="_class" value="cm-ajax"}{/if}
		{include file="buttons/button.tpl" but_href="checkout.delete_coupon?coupon_code=`$coupon_code`&redirect_url=`$_redirect_url`" but_role="delete" but_meta=$_class but_rev="checkout_totals,cart_items,cart_status,checkout_steps`$additional_ids`"}</span>
		<strong>&nbsp;</strong>
	</li>
	{/foreach}
{/if}
</div>
