{* $Id *}

{* NOTE: This template doesn\'t used for direct display
   It will store in the session and then display into notification box
   ---------------------------------------------------------------
   So, it is STRONGLY recommended to use strip tags in such templates
*}

{strip}
<div class="notification-body">
	{if $added_products}
	<ul>
		{foreach from=$added_products item=product key="key"}
			{hook name="products:notification_product"}
				<li>
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product_id|fn_get_product_name|escape}</a>
				</li>
				<li>
					<strong class="valign">{$product.amount}</strong>&nbsp;x&nbsp;{include file="common_templates/price.tpl" value=$product.display_price span_id="price_`$key`" class="none"}
				</li>
				{if $product.product_option_data}
					<li>{include file="common_templates/options_info.tpl" product_options=$product.product_option_data}</li>
				{/if}
			{/hook}
		{/foreach}
	</ul>
	{else}
	{$empty_text}
	{/if}
</div>
<div class="clear{if $n_type} center{/if}">
	{if !$n_type}
		<div class="float-left">
			{if $settings.DHTML.ajax_add_to_cart != "Y" && $settings.General.redirect_to_cart == 'Y'}
				{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script but_role="action"}
			{else}
				{include file="buttons/continue_shopping.tpl" but_meta="cm-notification-close"}
			{/if}
		</div>
		<div class="float-right">
			{include file="buttons/checkout.tpl" but_href="checkout.checkout"}
		</div>
	{else}
		{hook name="products:notification_control"}
		{if $n_type == "C"}
			{include file="buttons/button.tpl" but_href="product_features.compare" but_text=$lang.view_compare_list}
		{/if}
		{/hook}
	{/if}
</div>
{/strip}
