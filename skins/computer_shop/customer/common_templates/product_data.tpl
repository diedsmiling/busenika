{* $Id $ *}

{if ($product.price|floatval || $product.zero_price_action == "P" || $product.zero_price_action == "A" || (!$product.price|floatval && $product.zero_price_action == "R")) && !($settings.General.allow_anonymous_shopping == "P" && !$auth.user_id)}
	{assign var="show_price_values" value=true}
{else}
	{assign var="show_price_values" value=false}
{/if}
{capture name="show_price_values"}{$show_price_values}{/capture}

{assign var="cart_button_exists" value=false}
{assign var="obj_id" value=$obj_id|default:$product.product_id}
{assign var="product_amount" value=$product.inventory_amount|default:$product.amount}

{capture name="form_open_`$obj_id`"}
{if !$hide_form}
<form {if $settings.DHTML.ajax_add_to_cart == "Y" && !$no_ajax}class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="product_form_{$obj_prefix}{$obj_id}" enctype="multipart/form-data" class="cm-disable-empty-files">
<input type="hidden" name="result_ids" value="cart_status,wish_list" />
{if !$stay_in_cart}
<input type="hidden" name="redirect_url" value="{$config.current_url}" />
{/if}
<input type="hidden" name="product_data[{$obj_id}][product_id]" value="{$product.product_id}" />
{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="form_open_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="name_`$obj_id`"}
	{if $show_name}
		{if $hide_links}<strong>{else}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{/if}{$product.product|unescape}{if $hide_links}</strong>{else}</a>{/if}
	{elseif $show_trunc_name}
		{if $hide_links}<strong>{else}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title" title="{$product.product|strip_tags}">{/if}{$product.product|unescape|truncate:75:"...":true}{if $hide_links}</strong>{else}</a>{/if}
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="name_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="sku_`$obj_id`"}
	{if $show_sku}
		<p class="sku{if !$product.product_code} hidden{/if}">
			<span class="cm-reload-{$obj_prefix}{$obj_id}" id="sku_update_{$obj_prefix}{$obj_id}">
				<input type="hidden" name="appearance[show_sku]" value="{$show_sku}" />
				<span id="sku_{$obj_prefix}{$obj_id}">{$lang.sku}: <span id="product_code_{$obj_prefix}{$obj_id}">{$product.product_code}</span></span>
			<!--sku_update_{$obj_prefix}{$obj_id}--></span>
		</p>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="sku_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="rating_`$obj_id`"}
	{hook name="products:data_block"}
	{/hook}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="rating_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="add_to_cart_`$obj_id`"}
{if $show_add_to_cart}
<div class="cm-reload-{$obj_prefix}{$obj_id}" id="add_to_cart_update_{$obj_prefix}{$obj_id}">
<input type="hidden" name="appearance[show_add_to_cart]" value="{$show_add_to_cart}" />
<input type="hidden" name="appearance[separate_buttons]" value="{$separate_buttons}" />
<input type="hidden" name="appearance[show_list_buttons]" value="{$show_list_buttons}" />
<input type="hidden" name="appearance[but_role]" value="{$but_role}" />
{hook name="products:buttons_block"}
	{if !($product.zero_price_action == "R" && $product.price == 0) && !($settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y" && ($product_amount <= 0 || $product_amount < $product.min_qty) && $product.is_edp != "Y") || ($product.has_options && !$show_product_options)}
		<{if $separate_buttons}div class="buttons-container"{else}span{/if} id="cart_add_block_{$obj_prefix}{$obj_id}">
			{if $product.avail_since <= $smarty.const.TIME || ($product.avail_since > $smarty.const.TIME && $product.buy_in_advance == "Y")}
				{hook name="products:add_to_cart"}
				{if $product.has_options && !$show_product_options && !$details_page}
					{include file="buttons/button.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_text=$lang.select_options but_href="products.view?product_id=`$product.product_id`" but_role="text" but_name=""}
				{else}
					{if $extra_button}{$extra_button}&nbsp;{/if}
					{include file="buttons/add_to_cart.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_name="dispatch[checkout.add..`$obj_id`]" but_role=$but_role block_width=$block_width}
					{assign var="cart_button_exists" value=true}
				{/if}
				{/hook}
			{/if}
			{if $product.avail_since > $smarty.const.TIME}
				{include file="common_templates/coming_soon_notice.tpl" avail_date=$product.avail_since add_to_cart=$product.buy_in_advance}
			{/if}
		</{if $separate_buttons}div{else}span{/if}>
	{elseif !$details_page && ($settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y" && ($product_amount <= 0 || $product_amount < $product.min_qty) && $product.is_edp != "Y")}
		<span class="strong out-of-stock" id="out_of_stock_info_{$obj_prefix}{$obj_id}">{$lang.text_out_of_stock}</span>
	{/if}

	{if $show_list_buttons}
		<{if $separate_buttons}div class="buttons-container"{else}span{/if} id="cart_buttons_block_{$obj_prefix}{$obj_id}">
			{hook name="products:buy_now"}
			{if $product.feature_comparison == "Y"}
				{if $separate_buttons}</div><div class="buttons-container">{/if}
				{include file="buttons/add_to_compare_list.tpl" product_id=$product.product_id}
			{/if}
			{/hook}
		</{if $separate_buttons}div{else}span{/if}>
	{/if}
{/hook}
<!--add_to_cart_update_{$obj_prefix}{$obj_id}--></div>
{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="add_to_cart_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="product_features_`$obj_id`"}
	{if $show_features}
		<div class="cm-reload-{$obj_prefix}{$obj_id}" id="product_features_update_{$obj_prefix}{$obj_id}">
			<input type="hidden" name="appearance[show_features]" value="{$show_features}" />
			{include file="views/products/components/product_features_short_list.tpl" features=$product.product_id|fn_get_product_features_list|escape no_container=true}
		<!--product_features_update_{$obj_prefix}{$obj_id}--></div>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="product_features_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="prod_descr_`$obj_id`"}
	{if $show_descr}
		{if $product.short_description}
			{$product.short_description|unescape}
		{else}
			{$product.full_description|unescape|strip_tags|truncate:160}{if !$hide_links && $product.full_description|strlen > 180} <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="lowercase">{$lang.more}</a>{/if}
		{/if}
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="prod_descr_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{********************** Old Price *****************}
{capture name="old_price_`$obj_id`"}
	{if $show_price_values && $show_old_price}
		{if $product.discount || $product.list_discount}
		<span class="cm-reload-{$obj_prefix}{$obj_id}" id="old_price_update_{$obj_prefix}{$obj_id}">
			<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
			<input type="hidden" name="appearance[show_old_price]" value="{$show_old_price}" />
			{if $product.discount}
				<span class="list-price nowrap" id="line_old_price_{$obj_prefix}{$obj_id}">{if $details_page}{$lang.old_price}: {/if}<strike>{include file="common_templates/price.tpl" value=$product.original_price|default:$product.base_price span_id="old_price_`$obj_prefix``$obj_id`" class="list-price nowrap"}</strike></span>
			{elseif $product.list_discount}
				<span class="list-price nowrap" id="line_list_price_{$obj_prefix}{$obj_id}">{if $details_page}{$lang.list_price}: {/if}<strike>{include file="common_templates/price.tpl" value=$product.list_price span_id="list_price_`$obj_prefix``$obj_id`" class="list-price nowrap"}</strike></span>
			{/if}
		<!--old_price_update_{$obj_prefix}{$obj_id}--></span>
		{/if}
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="old_price_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{********************** Price *********************}
{capture name="price_`$obj_id`"}
	<span class="cm-reload-{$obj_prefix}{$obj_id}" id="price_update_{$obj_prefix}{$obj_id}">
		<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
		<input type="hidden" name="appearance[show_price]" value="{$show_price}" />
		{if $show_price_values}
			{if $show_price}
			{hook name="products:prices_block"}
				{if $product.price|floatval || $product.zero_price_action == "P" || ($hide_add_to_cart_button == "Y" && $product.zero_price_action == "A")}
					<span class="price{if !$product.price|floatval} hidden{/if}" id="line_discounted_price_{$obj_prefix}{$obj_id}">{if $details_page}{$lang.price}: {/if}{include file="common_templates/price.tpl" value=$product.price span_id="discounted_price_`$obj_prefix``$obj_id`" class="price"}</span>
				{elseif $product.zero_price_action == "A"}
					{assign var="base_currency" value=$currencies[$smarty.const.CART_PRIMARY_CURRENCY]}
					<span class="price">{$lang.enter_your_price}: {if $base_currency.after != "Y"}{$base_currency.symbol}{/if}<input class="input-text-short" type="text" size="3" name="product_data[{$obj_id}][price]" value="" />{if $base_currency.after == "Y"}&nbsp;{$base_currency.symbol}{/if}</span>
				{elseif $product.zero_price_action == "R"}
					<span class="price">{$lang.contact_us_for_price}</span>
				{/if}
			{/hook}
			{/if}
		{elseif $settings.General.allow_anonymous_shopping == "P" && !$auth.user_id}
			<p class="price">{$lang.sign_in_to_view_price}</p>
		{/if}
	<!--price_update_{$obj_prefix}{$obj_id}--></span>
{/capture}
{if $no_capture}
	{assign var="capture_name" value="price_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{******************* Clean Price ******************}
{capture name="clean_price_`$obj_id`"}
	{if $show_price_values && $show_clean_price && $settings.Appearance.show_prices_taxed_clean == "Y" && $product.taxed_price}
		<span class="cm-reload-{$obj_prefix}{$obj_id}" id="clean_price_update_{$obj_prefix}{$obj_id}">
			<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
			<input type="hidden" name="appearance[show_clean_price]" value="{$show_clean_price}" />
			{if $product.clean_price != $product.taxed_price && $product.included_tax}
				<span class="list-price nowrap" id="line_product_price_{$obj_prefix}{$obj_id}">({include file="common_templates/price.tpl" value=$product.taxed_price span_id="product_price_`$obj_prefix``$obj_id`" class="list-price nowrap"} {$lang.inc_tax})</span>
			{elseif $product.clean_price != $product.taxed_price && !$product.included_tax}
				<span class="list-price nowrap">({$lang.including_tax})</span>
			{/if}
		<!--clean_price_update_{$obj_prefix}{$obj_id}--></span>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="clean_price_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{********************** You Save ******************}
{capture name="list_discount_`$obj_id`"}
	{if $show_price_values && $show_list_discount && $details_page}
		{if $product.discount || $product.list_discount}
			<span class="cm-reload-{$obj_prefix}{$obj_id}" id="line_discount_update_{$obj_prefix}{$obj_id}">
				<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
				<input type="hidden" name="appearance[show_list_discount]" value="{$show_list_discount}" />
				{if $product.discount}
					<span class="list-price nowrap" id="line_discount_value_{$obj_prefix}{$obj_id}">{$lang.you_save}: {include file="common_templates/price.tpl" value=$product.discount span_id="discount_value_`$obj_prefix``$obj_id`" class="list-price nowrap"}&nbsp;(<span id="prc_discount_value_{$obj_prefix}{$obj_id}" class="list-price nowrap">{$product.discount_prc}</span>%)</span>
				{elseif $product.list_discount}
					<span class="list-price nowrap" id="line_discount_value_{$obj_prefix}{$obj_id}">{$lang.you_save}: {include file="common_templates/price.tpl" value=$product.list_discount span_id="discount_value_`$obj_prefix``$obj_id`" class="list-price nowrap"}&nbsp;(<span id="prc_discount_value_{$obj_prefix}{$obj_id}" class="list-price nowrap">{$product.list_discount_prc}</span>%)</span>
				{/if}
			<!--line_discount_update_{$obj_prefix}{$obj_id}--></span>
		{/if}
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="list_discount_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{************************************ Discount label ****************************}
{capture name="discount_label_`$obj_prefix``$obj_id`"}
	{if $show_discount_label && ($product.discount_prc || $product.list_discount_prc) && $show_price_values}
		<div class="discount-label cm-reload-{$obj_prefix}{$obj_id}" id="discount_label_update_{$obj_prefix}{$obj_id}">
			<input type="hidden" name="appearance[show_discount_label]" value="{$show_discount_label}" />
			<input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
			<div id="line_prc_discount_value_{$obj_prefix}{$obj_id}">
				<em><strong>-</strong><span id="prc_discount_value_label_{$obj_prefix}{$obj_id}">{if $product.discount}{$product.discount_prc}{else}{$product.list_discount_prc}{/if}</span>%</em>
			</div>
		<!--discount_label_update_{$obj_prefix}{$obj_id}--></div>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="discount_label_`$obj_prefix``$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="product_amount_`$obj_id`"}
{if $show_product_amount && $product.is_edp != "Y" && $settings.General.inventory_tracking == "Y" && $product.tracking != "D"}
	<span class="cm-reload-{$obj_prefix}{$obj_id}" id="product_amount_update_{$obj_prefix}{$obj_id}">
		<input type="hidden" name="appearance[show_product_amount]" value="{$show_product_amount}" />
		{if $settings.Appearance.in_stock_field == "Y"}
			{if ($product_amount > 0 && $product_amount >= $product.min_qty) && $settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y" || $details_page}
				{if ($product_amount > 0 && $product_amount >= $product.min_qty) && $settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y"}
					<div class="form-field product-list-field">
						<label>{$lang.in_stock}:</label>
						<span id="qty_in_stock_{$obj_prefix}{$obj_id}" class="qty-in-stock">
							{$product_amount}&nbsp;{$lang.items}
						</span>	
					</div>
				{else}
					<p class="strong out-of-stock">{$lang.text_out_of_stock}</p>
				{/if}
			{/if}
		{else}
			{if ($product_amount > 0 && $product_amount >= $product.min_qty) && $settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y"}
				<span class="strong in-stock" id="in_stock_info_{$obj_prefix}{$obj_id}">{$lang.in_stock}</span>
			{elseif $details_page}
				<span class="strong out-of-stock" id="out_of_stock_info_{$obj_prefix}{$obj_id}">{$lang.text_out_of_stock}</span>
			{/if}
		{/if}
	<!--product_amount_update_{$obj_prefix}{$obj_id}--></span>
{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="product_amount_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="product_options_`$obj_id`"}
	{if $show_product_options}
	<div class="cm-reload-{$obj_prefix}{$obj_id}" id="product_options_update_{$obj_prefix}{$obj_id}">
		<input type="hidden" name="appearance[show_product_options]" value="{$show_product_options}" />
		{hook name="products:product_option_content"}
			{if $disable_ids}
				{assign var="_disable_ids" value="`$disable_ids``$obj_id`"}
			{else}
				{assign var="_disable_ids" value=""}
			{/if}
			{include file="views/products/components/product_options.tpl" id=$obj_id product_options=$product.product_options name="product_data" capture_options_vs_qty=$capture_options_vs_qty disable_ids=$_disable_ids}
		{/hook}
	<!--product_options_update_{$obj_prefix}{$obj_id}--></div>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="product_options_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="advanced_options_`$obj_id`"}
	{if $show_product_options}
		<div class="cm-reload-{$obj_prefix}{$obj_id}" id="advanced_options_update_{$obj_prefix}{$obj_id}">
			{include file="views/companies/components/product_company_data.tpl" company_id=$product.company_id}
			{hook name="products:options_advanced"}
			{/hook}
		<!--advanced_options_update_{$obj_prefix}{$obj_id}--></div>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="advanced_options_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="qty_`$obj_id`"}
	{if $show_qty}
		<div class="cm-reload-{$obj_prefix}{$obj_id}" id="qty_update_{$obj_prefix}{$obj_id}">
		<input type="hidden" name="appearance[show_qty]" value="{$show_qty}" />
		<input type="hidden" name="appearance[capture_options_vs_qty]" value="{$capture_options_vs_qty}" />
		{if !empty($product.selected_amount)}
			{assign var="default_amount" value=$product.selected_amount}
		{elseif !empty($product.min_qty)}
			{assign var="default_amount" value=$product.min_qty}
		{else}
			{assign var="default_amount" value="1"}
		{/if}
		
		{if ($product.qty_content || $show_qty) && $product.is_edp !== "Y" && $cart_button_exists == true && ($settings.General.allow_anonymous_shopping == "Y" || $auth.user_id)}
			<div class="form-field{if !$capture_options_vs_qty} product-list-field{/if}{if $settings.Appearance.quantity_changer == "Y"} changer{/if}" id="qty_{$obj_prefix}{$obj_id}">
				<label for="qty_count_{$obj_prefix}{$obj_id}">{$lang.quantity}:</label>
				{if $product.qty_content}
				<select name="product_data[{$obj_id}][amount]" id="qty_count_{$obj_prefix}{$obj_id}">
				{assign var="a_name" value="product_amount_`$obj_prefix``$obj_id`"}
				{assign var="selected_amount" value=false}
				{foreach name="`$a_name`" from=$product.qty_content item="var"}
					<option value="{$var}" {if $product.selected_amount && ($product.selected_amount == $var || ($smarty.foreach.$a_name.last && !$selected_amount))}{assign var="selected_amount" value=true}selected="selected"{/if}>{$var}</option>
				{/foreach}
				</select>
				{else}
				{if $settings.Appearance.quantity_changer == "Y"}
				<div class="center valign cm-value-changer">
					<a class="cm-increase"><img src="{$images_dir}/icons/up_arrow.gif" width="21" height="9" border="0" /></a>
					{/if}
<input type="text" size="5" class="input  pr-input input-text-short cm-amount" id="qty_count_{$obj_prefix}{$obj_id}" name="product_data[{$obj_id}][amount]" onfocus="if(this.value=='{$default_amount}') this.value='';" onblur="if(this.value=='') this.value='{$default_amount}';" value="{$default_amount}" />
					{if $settings.Appearance.quantity_changer == "Y"}
					<a class="cm-decrease"><img src="{$images_dir}/icons/down_arrow.gif" width="21" height="9" border="0" /></a>
				</div>
				{/if}
				{/if}
			</div>
			{if $product.prices}
				{include file="views/products/components/products_qty_discounts.tpl"}
			{/if}
		{elseif !$bulk_add}
			<input type="hidden" name="product_data[{$obj_id}][amount]" value="{$default_amount}" />
		{/if}
		<!--qty_update_{$obj_prefix}{$obj_id}--></div>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="qty_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="min_qty_`$obj_id`"}
	{if $min_qty && $product.min_qty}
		<p class="description">{$lang.text_cart_min_qty|replace:"[product]":$product.product|replace:"[quantity]":$product.min_qty}.</p>
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="min_qty_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="product_edp_`$obj_id`"}
	{if $show_edp && $product.is_edp == "Y"}
		<p class="description">{$lang.text_edp_product}.</p>
		<input type="hidden" name="product_data[{$obj_id}][is_edp]" value="Y" />
	{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="product_edp_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{capture name="form_close_`$obj_id`"}
{if !$hide_form}
</form>
{/if}
{/capture}
{if $no_capture}
	{assign var="capture_name" value="form_close_`$obj_id`"}
	{$smarty.capture.$capture_name}
{/if}

{foreach from=$images key="object_id" item="image"}
	<div class="cm-reload-{$image.obj_id}" id="{$object_id}">
		<input type="hidden" value="{$image.obj_id},{$image.width},{$image.height},{$image.type}" name="image[{$object_id}]" />
		{include file="common_templates/image.tpl" image_width=$image.width image_height=$image.height show_thumbnail="Y" obj_id=$object_id images=$product.main_pair object_type="product"}
	<!--{$object_id}--></div>
{/foreach}

{hook name="products:product_data"}{/hook}
