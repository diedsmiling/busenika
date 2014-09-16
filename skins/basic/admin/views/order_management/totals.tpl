{* $Id: totals.tpl 10362 2010-08-05 13:10:15Z 2tl $ *}

{* Cart content table*}

{capture name="mainbox"}

{include file="views/order_management/components/orders_header.tpl"}

{notes}
{$lang.text_om_checkbox_notice}
{/notes}

<form action="{""|fn_url}" method="post" name="om_totals">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="50%">{$lang.product}</th>
	<th width="10%">{$lang.price}</th>
	<th width="5%">{$lang.quantity}</th>
	{if $cart.use_discount}
	<th width="10%">{$lang.discount}</th>
	{/if}
	{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
	<th width="10%">{$lang.tax}</th>
	{/if}
	<th class="right" width="10%">{$lang.subtotal}</th>
</tr>
{if $cart_products}
{foreach from=$cart_products item="cp" key="key"}
{if  !$cart.products.$key.extra.parent}
{cycle values=",class=\"table-row\"" name="class_cycle" assign="_class"}
<tr {$_class} valign="top">
	<td>
		<a href="{"products.update?product_id=`$cp.product_id`"|fn_url}">{$cp.product}</a>
		{hook name="order_management:product_info"}
		{if $cp.product_code}
		<p>{$lang.sku}:&nbsp;{$cp.product_code}</p>
		{/if}
		{/hook}
		{if $cp.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$cp.product_options}</div>{/if}
	</td>
	<td class="nowrap">
		{if $cp.exclude_from_calculate}{$lang.free}
		{else} {assign var="price_with_options" value=$cp.base_price}
			{if $cp.modifiers_price > 0}
				{math equation="x+y" x=$price_with_options y=$cp.modifiers_price assign="price_with_options"}	
			{/if}
			{include file="common_templates/price.tpl" value=$price_with_options}
		{/if}</td>
	<td class="nowrap">
		{$cp.amount}</td>
	{if $cart.use_discount}
	<td class="nowrap">
		{if $cp.discount|floatval}{include file="common_templates/price.tpl" value=$cp.discount}{else}-{/if}</td>
	{/if}
	{if $cart.taxes && $settings.General.tax_calculation != "subtotal"}
	<td class="nowrap">
		{include file="common_templates/price.tpl" value=$cp.tax_summary.total}</td>
	{/if}
	<td class="right nowrap">
		{if $cp.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$cp.display_subtotal}{/if}</td>
</tr>
{/if}
{/foreach}
{/if}
{hook name="order_management:extra_list"}
{/hook}
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<td class="total-details">
	{if !$cart.order_id}
	<div class="form-field">
		<label for="coupon_code">{$lang.discount_coupon_code}:</label>
		<input type="text" name="coupon_code" id="coupon_code" class="input-text-large" size="30" value="" />
	</div>
	{/if}
	
	{hook name="order_management:totals_extra"}
		{hook name="order_management:totals_shipping"}
		{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR" || ($settings.Suppliers.enable_suppliers == "Y" && $settings.Suppliers.display_shipping_methods_separately == "Y")}

		<div class="form-field">
			<label>{$lang.shipping_method}:</label>
			<table cellpadding="1" cellspacing="1" border="0" width="100%">
			{foreach from=$suppliers key=supplier_id item=supplier}
				{cycle values="class=\"table-row\", " assign="_class"}
				<tr {$_class}>
					<td class="nowrap">&nbsp;<strong>{$supplier.company|default:$lang.none}</strong></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					{if $supplier.rates}
					<td width="100%">
						<select name="shipping_ids[{$supplier_id}]">
						{foreach from=$supplier.rates key=shipping_id item=rate}
						<option value="{$shipping_id}" {if $cart.shipping.$shipping_id.rates.$supplier_id}selected="selected"{/if}>{$rate.name} ({$rate.delivery_time}) - {include file="common_templates/price.tpl" value=$rate.rate}</option>
						{/foreach}
						</select></td>
					{else}
					<td class="error-text" align="center"  width="100%">{$lang.text_no_shipping_methods}</td>
					{assign var="is_empty_rates" value="Y"}
					{/if}
				</tr>
			{/foreach}
			</table>
		</div>

		{else}{* not MVE or Suppliers disabled *}

		{if $cart.shipping_required}
		<div class="form-field">
			<label for="shipping_ids">{$lang.shipping_method}:</label>
			{if $shipping_rates}
				<select name="shipping_ids[]" id="shipping_ids">
					{foreach from=$shipping_rates key=shipping_id item=s_rate}
						{assign var="rate" value="0"}
						{foreach from=$s_rate.rates key=key_id item=r}
							{math equation="x + y" x=$rate y=$r assign="rate"}
						{/foreach}
					<option value="{$shipping_id}" {if $cart.shipping.$shipping_id}selected="selected"{/if}>{$s_rate.name} ({$s_rate.delivery_time}) - {include file="common_templates/price.tpl" value=$rate}</option>
					{/foreach}
				</select>
			{else}
				<span class="error-text">{$lang.text_no_shipping_methods}</span>
			{/if}
		</div>
		{/if}

		{/if}
		{/hook}
	
		<div class="form-field">
		{if $settings.General.min_order_amount <= $cart.total}
			{if $cart.total != 0}
			<label for="payment_methods">{$lang.payment_method}:</label>
			<select name="payment_id" id="payment_methods" onchange="fn_set_payment_surcharges('{$cart.total}', this.value)">
				{foreach from=$payment_methods item="pm" name="pay"}
				<option value="{$pm.payment_id}" {if $cart.payment_id == $pm.payment_id || (!$cart.payment_id && $smarty.foreach.pay.first)}{assign var="selected_payment_id" value=$pm.payment_id}selected="selected"{/if}>{$pm.payment}</option>
				{/foreach}
			</select>
			{/if}
	
		{elseif $settings.General.min_order_amount > $cart.total}
			<label class="error-text">
				{$lang.text_min_order_amount_required}&nbsp;<strong>{include file="common_templates/price.tpl" value=$settings.General.min_order_amount}</strong></label>
		{/if}
		</div>
	{/hook}
	</td><td class="right">
	<ul class="statistic-list">
		<li>
			<em>{$lang.subtotal}:</em>
			<strong>{include file="common_templates/price.tpl" value=$cart.display_subtotal}</strong>
		</li>
		
		{if ($cart.discount|floatval)}
		<li>
			<em>{$lang.including_discount}:</em>
			<strong>{include file="common_templates/price.tpl" value=$cart.discount}</strong>
		</li>
		{/if}

		{if $cart.subtotal_discount|floatval}
		<li class="toggle-elm">
			<em>{$lang.order_discount}:</em>
			<strong>
			<input type="hidden" name="stored_subtotal_discount" value="N" />
			<input type="checkbox" class="valign" name="stored_subtotal_discount" value="Y" {if $cart.stored_subtotal_discount == "Y" && $cart.order_id}checked="checked"{/if} {if !$cart.order_id}disabled="disabled"{/if} onclick="$('span[@id^=db_subtotal_]').toggle(); $('span[@id^=manual_subtotal_]').toggle();" />
			<span {if $cart.stored_subtotal_discount == "Y"}style="display: none;"{/if} id="db_subtotal_discount">{include file="common_templates/price.tpl" value=$cart.original_subtotal_discount|default:$cart.subtotal_discount}</span>
			<span {if $cart.stored_subtotal_discount != "Y"}style="display: none;"{/if} id="manual_subtotal_discount">{$currencies.$primary_currency.symbol}&nbsp;<input type="text" class="input-text" size="5" name="subtotal_discount" value="{$cart.subtotal_discount}" /></span></strong>
		</li>
		{/if}

		{if $cart.taxes}
		<li>
			<em>{$lang.manually_set_tax_rates}:</em>
			<strong>
				<input type="hidden" name="stored_taxes" value="N" />
				<input type="checkbox" name="stored_taxes" value="Y" {if $cart.stored_taxes == "Y"}checked="checked"{/if} onclick="$('span[@id^=db_taxes_]').toggle(); $('span[@id^=manual_taxes_]').toggle();" {if !$cart.order_id}disabled="disabled"{/if} /></strong>
		</li>
		
		{foreach from=$cart.taxes item="tax" key=key name="fet"}
		<li class="toggle-elm">
			<em>&nbsp;<strong>&middot;</strong>&nbsp;{$tax.description}{if $tax.price_includes_tax == "Y" && $settings.Appearance.cart_prices_w_taxes != "Y"}&nbsp;{$lang.included}{/if}
			{strip}
			(
			<span {if $cart.stored_taxes == "Y"}class="hidden"{/if} id="db_taxes_{$key}">{include file="common_templates/modifier.tpl" mod_value=$tax.rate_value mod_type=$tax.rate_type}</span>
			<span {if $cart.stored_taxes != "Y"}class="hidden"{/if} id="manual_taxes_{$key}"><input type="text" class="input-text" size="5" name="taxes[{$key}]" value="{$tax.rate_value}" /></span>
			){/strip}:</em>
			<strong>{include file="common_templates/price.tpl" value=$tax.tax_subtotal}</strong>
		</li>
		{/foreach}
		{/if}
		
		{if $cart.shipping}
		{foreach from=$cart.shipping item="sh" key=key name="f_shipping"}
		<li class="toggle-elm">
			<em>{$sh.shipping}:</em>
			<strong>
			{if $cart.stored_shipping.$key || $cart.stored_shipping.$key === "0"}{assign var="custom_ship_exists" value=true}{else}{assign var="custom_ship_exists" value=false}{/if}
			<input type="hidden" name="stored_shipping[{$key}]" value="N" />
			<input type="checkbox" class="valign" name="stored_shipping[{$key}]" value="Y" {if $custom_ship_exists}checked="checked"{/if} onclick="$('span[@id^=db_shipping_]').toggle(); $('span[@id^=manual_shipping_]').toggle();" />
			<span {if $custom_ship_exists}style="display: none;"{/if} id="db_shipping_{$key}">{if $cart.shipping.$key.rates}{include file="common_templates/price.tpl" value=$cart.shipping.$key.rates|@array_sum}{else}{include file="common_templates/price.tpl" value=0}{/if}</span>
			<span {if !$custom_ship_exists}style="display: none;"{/if} id="manual_shipping_{$key}">{$currencies.$primary_currency.symbol}&nbsp;<input type="text" class="input-text" size="5" name="stored_shipping_cost[{$key}]" value="{$cart.shipping.$key.rates|@array_sum}" /></span></strong>
		</li>
		{/foreach}
		{/if}
		
		{if $cart.coupons}
		<input type="hidden" name="c_id" value="0" id="c_id" />
		{foreach from=$cart.coupons item="coupon" key="key"}

		<li>
			<em>{$lang.coupon} {$key}{if !$cart.order_id}&nbsp;{include file="buttons/button.tpl" but_href="order_management.delete_coupon?c_id=`$key`" but_role="delete_item"}{/if}</em>
			<strong>&nbsp;</strong>
		</li>
		
		{/foreach}
		{/if}
		
		<li class="hidden" id="payment_surcharge_line">
			<em>{$lang.payment_surcharge}:</em>
			<strong>{include file="common_templates/price.tpl" value="0.00" span_id="payment_surcharge_value" class="list_price"}</strong>
		</li>

		{hook name="order_management:totals"}
		{/hook}
		
		<li class="total">
			<em>{$lang.total_cost}:</em>
			<strong>{include file="common_templates/price.tpl" value=$cart.total span_id="cart_total"}</strong>
		</li>
	</ul>
	</td>
</tr>
</table>


<div class="buttons-container buttons-bg center">
	<div class="float-left">
		{include file="buttons/save.tpl" but_name="dispatch[order_management.update_totals]" but_role="button_main"}
	</div>
	{include file="buttons/button.tpl" but_text=$lang.proceed_to_the_next_step but_name="dispatch[order_management.update_totals/continue]" but_role="big"}
</div>

</form>

{if $selected_payment_id}
<script type="text/javascript">
	//<![CDATA[
	surcharges = new Array();
	{foreach from=$payment_methods item="pm" name="pay"}
	surcharges[{$pm.payment_id}] = '{$pm.surcharge_value|default:"0"}';
	{/foreach}
	{literal}
	function fn_set_payment_surcharges(total, payment_id)
	{
		var surcharge = parseFloat(surcharges[payment_id]);
		var total = parseFloat(total);

		$('#payment_surcharge_value').html(jQuery.formatNum(surcharge, 2, true));
		$('#sec_payment_surcharge_value').html(jQuery.formatNum(surcharge / currencies.secondary.coefficient, 2, false));
		$('#cart_total').html(jQuery.formatNum(surcharge + total, 2, true));
		$('#sec_cart_total').html(jQuery.formatNum((surcharge + total) / currencies.secondary.coefficient, 2, false));
		$('#payment_surcharge_line').toggleBy(surcharge == '0');
	}
	{/literal}
	fn_set_payment_surcharges('{$cart.total}', {$selected_payment_id});
	//]]>
</script>
{/if}

{/capture}
{if $cart.order_id == ""}
	{assign var="_title" value=$lang.create_new_order}
{else}
	{assign var="_title" value="`$lang.editing_order`:&nbsp;#`$cart.order_id`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}
