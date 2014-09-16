{* $Id: configuration_product.tpl 9323 2010-04-26 10:45:29Z angel $ *}

<div id="content_description_{$group_id}_{$product.product_id}">
{script src="js/exceptions.js"}
<div class="object-container">
<div class="clear">
	{assign var="id" value=$product.product_id}
	<div class="product-image">
			{include file="common_templates/image.tpl" show_detailed_link=true obj_id=$product.product_id images=$product.main_pair object_type="product" class="cm-thumbnails cm-single"}
	</div>
	
	<div class="product-description">
		{if $product.product_code}
		<p class="sku">{$lang.sku}: {$product.product_code}</p>
		{/if}
		{************************ Discounted Price, Our Price, Price ********************}
		{if $product.price != 0 || $settings.General.zero_price_action == "permit" || $settings.General.zero_price_action == "ask_price"}
			{if $product.discounts && $product.price|floatval}{$lang.discounted_price}
				{include file="common_templates/price.tpl" value=$product.discounted_price span_id="discounted_price_`$product.product_id`" class="price"}
			{else}
				{$lang.price}: {include file="common_templates/price.tpl" value=$product.price span_id="original_price_`$product.product_id`" class="price"}
			{/if}
		{elseif $settings.General.zero_price_action == "refuse"}
			{$lang.contact_us_for_price}
		{/if}

		{if $product.tax != ""}
			&nbsp;({$lang.including_tax}&nbsp;{include file="common_templates/price.tpl" value=$product.tax})
		{/if}
	</div>
</div>

{if $product.full_description || $product.short_description}
	<div class="tabs clear cm-j-tabs">
		<ul>
			<li id="description" class="cm-js cm-active"><a>{$lang.description}</a></li>
		</ul>
	</div>
	
	<div id="tabs_content" class="cm-tabs-content">
		<div id="content_description">
		<p>{$product.full_description|default:$product.short_description|unescape}</p>
		</div>
	</div>
{/if}
<!--content_description_{$group_id}_{$product.product_id}--></div>
