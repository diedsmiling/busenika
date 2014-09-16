{* $Id: checkout_totals_info.tpl 10081 2010-07-15 13:25:17Z alexions $ *}

{if $applied_promotions}
	{include file="views/checkout/components/applied_promotions.tpl" location=$location}
{/if}

{if $location == "cart" && $cart.shipping_required == true && $settings.General.estimate_shipping_cost == "Y"}
	{include file="views/checkout/components/shipping_estimation.tpl"}
{/if}

<ul class="statistic-list">
	<li class="subtotal">
		<span>{$lang.subtotal}:</span>
		<strong>{include file="common_templates/price.tpl" value=$cart.display_subtotal}</strong>
	</li>
	
	{hook name="checkout:checkout_totals"}
		{if $cart.shipping_required == true && $shipping_rates && ($location != "cart" || $settings.General.estimate_shipping_cost == "Y")}
		<li>
			<span>{$lang.shipping_cost}:</span>
			<strong>{include file="common_templates/price.tpl" value=$cart.display_shipping_cost}</strong>
		</li>
		{/if}
	{/hook}
	
	{if ($cart.discount|floatval)}
	<li>
		<span>{$lang.including_discount}:</span>
		<strong>{include file="common_templates/price.tpl" value=$cart.discount}</strong>
	</li>
	{/if}

	{if ($cart.subtotal_discount|floatval)}
	<li>
		<span>{$lang.order_discount}:</span>
		<strong>{include file="common_templates/price.tpl" value=$cart.subtotal_discount}</strong>
	</li>
	{/if}
	
	{if $cart.coupons|floatval}
	{foreach from=$cart.coupons item="coupon" key="coupon_code"}
	<li>
		<span>{$lang.coupon} "{$coupon_code}"
		{assign var="_redirect_url" value=$config.current_url|escape:url}
		{if $settings.General.one_page_checkout == "Y"}{if $use_ajax}{assign var="_class" value="cm-ajax"}{/if}
		{include file="buttons/button.tpl" but_href="checkout.delete_coupon?coupon_code=`$coupon_code`&redirect_url=`$_redirect_url`" but_role="delete" but_meta=$_class but_rev="checkout_totals,cart_items,cart_status,checkout_steps"}{/if}</span>
		<strong>&nbsp;</strong>
	</li>
	{/foreach}
	{/if}

	{if $cart.taxes}
	<li>
		<span>{$lang.taxes}:</span>
		<strong>&nbsp;</strong>
	</li>
	{foreach from=$cart.taxes item="tax"}
	<li>
		<span><em>{$tax.description}&nbsp;({include file="common_templates/modifier.tpl" mod_value=$tax.rate_value mod_type=$tax.rate_type}{if $tax.price_includes_tax == "Y" && ($settings.Appearance.cart_prices_w_taxes != "Y" || $settings.General.tax_calculation == "subtotal")}&nbsp;{$lang.included}{/if}):</em></span>
		<strong>{include file="common_templates/price.tpl" value=$tax.tax_subtotal}</strong>
	</li>
	{/foreach}
	{/if}

	{if $cart.payment_surcharge}
	<li id="payment_surcharge_line">
		<span>{$lang.payment_surcharge}:</span>
		<strong>{include file="common_templates/price.tpl" value=$cart.payment_surcharge span_id="payment_surcharge_value"}</strong>
	</li>
	{math equation="x+y" x=$cart.total y=$cart.payment_surcharge assign="_total"}
	{/if}
	
	<li class="total">
		<strong>{$lang.total_cost}:</strong>{include file="common_templates/price.tpl" value=$_total|default:$cart.total span_id="cart_total" class="price valign"}
	</li>
</ul>