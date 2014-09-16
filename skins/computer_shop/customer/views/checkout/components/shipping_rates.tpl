{* $Id: shipping_rates.tpl 10362 2010-08-05 13:10:15Z 2tl $ *}

{if $cart.shipping_required == true}

	{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
		{assign var="lang_vendor_supplier" value=$lang.vendor}
	{else}
		{assign var="lang_vendor_supplier" value=$lang.supplier}
	{/if}

	{if $show_header == true}
		{include file="common_templates/subheader.tpl" title=$lang.select_shipping_method}
	{/if}

	{if !$no_form}
	<form {if $use_ajax}class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="shippings_form">
	<input type="hidden" name="redirect_mode" value="checkout" />
	{if $use_ajax}<input type="hidden" name="result_ids" value="checkout_totals,checkout_steps" />{/if}
	{/if}

	{hook name="checkout:shipping_rates"}

	{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR" || ($settings.Suppliers.enable_suppliers == "Y" && $settings.Suppliers.display_shipping_methods_separately == "Y")}
	
		{if $display == "show"}
		<div class="step-complete-wrapper">
		{/if}

		{foreach from=$suppliers key=supplier_id item=supplier name="s"}
		<p>
		<strong>{$lang_vendor_supplier}:&nbsp;</strong>{$s_companies.$supplier_id.company}
		</p>
		<ul class="bullets-list">
		{foreach from=$supplier.products item="cart_id"}
			{if $supplier_id != 0 || ($supplier_id == 0 && ($supplier.all_edp_no_shipping == true || !($cart_products.$cart_id.is_edp == "Y" && $cart_products.$cart_id.edp_shipping == "N")))}<li>{if $cart_products.$cart_id}{$cart_products.$cart_id.product|unescape}{else}{$cart.products.$cart_id.product_id|fn_get_product_name:$smarty.const.CART_LANGUAGE}{/if}</li>{/if}
		{/foreach}
		</ul>
		{if $supplier.rates && !$supplier.all_edp_no_shipping}

			{if $display == "radio"}

			{foreach from=$supplier.rates key="shipping_id" item="rate"}
			<p>
				<input type="radio" class="valign" id="sh_{$supplier_id}_{$shipping_id}" name="shipping_ids[{$supplier_id}]" value="{$shipping_id}" {if isset($cart.shipping.$shipping_id.rates.$supplier_id)}checked="checked"{/if} /><label for="sh_{$supplier_id}_{$shipping_id}" class="valign">{$rate.name} {if $rate.delivery_time}({$rate.delivery_time}){/if}{if $rate.rate} &mdash; {include file="common_templates/price.tpl" value=$rate.rate}{else}{/if}</label>
			</p>
			{/foreach}

			{elseif $display == "select"}

			<p>
			<select id="ssr_{$supplier_id}" name="shipping_ids[{$supplier_id}]" {if $onchange}onchange="{$onchange}"{/if}>
			{foreach from=$supplier.rates key=shipping_id item=rate}
			<option value="{$shipping_id}" {if isset($cart.shipping.$shipping_id.rates.$supplier_id)}selected="selected"{/if}>{$rate.name} {if $rate.delivery_time}({$rate.delivery_time}){/if}{if $rate.rate} &mdash; {include file="common_templates/price.tpl" value=$rate.rate}{else}{/if}</option>
			{/foreach}
			</select>
			</p>

			{elseif $display == "show"}

			{foreach from=$supplier.rates key=shipping_id item=rate}
			{if isset($cart.shipping.$shipping_id.rates.$supplier_id)}<p><strong>{$rate.name} {if $rate.delivery_time}({$rate.delivery_time}){/if}{if $rate.rate} &mdash; {include file="common_templates/price.tpl" value=$rate.rate}{else}{/if}</strong></p>{/if}
			{/foreach}

			{/if}
		{else}

		<p>{if $display == "show"}<strong>{/if}{$lang.no_shipping_required}{if $display == "show"}</strong>{/if}</p>
		{/if}
		{/foreach}
		<p class="right"><strong>{$lang.total}:</strong>&nbsp;{include file="common_templates/price.tpl" value=$cart.shipping_cost class="price"}</p>


		{if $display == "show"}
		</div>
		{/if}

	{else}{* $settings.Suppliers.display_shipping_methods_separately != "Y"  OR Suppliers disabled*}

		{if $shipping_rates}
	
		{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR" || ($settings.Suppliers.enable_suppliers == "Y" && $settings.Suppliers.display_shipping_methods_separately == "Y")}
			{assign var="_suppliers_ids" value=","|implode:$supplier_ids}
		{else}
			{assign var="_suppliers_ids" value=""}
		{/if}

		<div class="overflow-hidden {if $display == "select"}form-field shipping-rates{/if}" id="shipping_rates_list">
		{if $display == "radio"}

			{foreach from=$shipping_rates key="shipping_id" item="s_rate"}
			<p>
				<input type="radio" class="valign" name="shipping_ids[{$_suppliers_ids}]" value="{$shipping_id}" id="sh_{$shipping_id}" {if $cart.shipping.$shipping_id}checked="checked"{/if} />&nbsp;<label for="sh_{$shipping_id}" class="valign">{$s_rate.name} {if $s_rate.delivery_time}({$s_rate.delivery_time}){/if}{if $s_rate.rates|@array_sum} &mdash; {include file="common_templates/price.tpl" value=$s_rate.rates|@array_sum}{else}{/if}</label>
			</p>
			{/foreach}
			
		{elseif $display == "select"}

			<label for="ssr">{$lang.shipping_method}:</label>
	
			<select id="ssr" name="shipping_ids[{$_suppliers_ids}]">
			{foreach from=$shipping_rates key="shipping_id" item="s_rate"}
				<option value="{$shipping_id}" {if $cart.shipping.$shipping_id}selected="selected"{/if}>{$s_rate.name} {if $s_rate.delivery_time}({$s_rate.delivery_time}){/if}{if $s_rate.rates|@array_sum} &mdash; {include file="common_templates/price.tpl" value=$s_rate.rates|@array_sum}{else}{/if}</option>
			{/foreach}
			</select>

		{elseif $display == "show"}

			{foreach from=$shipping_rates key="shipping_id" item="s_rate"}
				{if $cart.shipping.$shipping_id}
					{capture name="selected_shipping"}
						{$s_rate.name} {if $s_rate.delivery_time}({$s_rate.delivery_time}){/if}{if $s_rate.rates|@array_sum} &mdash; {include file="common_templates/price.tpl" value=$s_rate.rates|@array_sum}{else}{/if}
					{/capture}
				{/if}
			{/foreach}
			{$smarty.capture.selected_shipping}
		{/if}

		<!--shipping_rates_list--></div>

		{/if}{* $shipping_rates *}
	
	{/if}{* $settings.Suppliers.display_shipping_methods_separately === "Y" *}

	{/hook}

	{if !$no_form}
	<div class="cm-noscript buttons-container center">{include file="buttons/button.tpl" but_name="dispatch[checkout.update_shipping]" but_text=$lang.select}</div>

	</form>
	{/if}

{/if}