{* $Id: buy_together.tpl 10543 2010-08-30 13:04:31Z alexions $ *}
{** block-description:buy_together **}

{if $chains}
	{script src="js/picker.js"}
	{script src="js/jquery.easydrag.js"}
	
	<div id="content_buy_together">
	
	{foreach from=$chains key="key" item="chain"}
		{assign var="obj_prefix" value="bt_`$chain.chain_id`"}
		<form {if $settings.DHTML.ajax_add_to_cart == "Y" && !$no_ajax}class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="chain_form_{$chain.chain_id}" enctype="multipart/form-data">
		<input type="hidden" name="result_ids" value="cart_status,wish_list" />
		{if !$stay_in_cart}
			<input type="hidden" name="redirect_url" value="{$config.current_url}" />
		{/if}
		<input type="hidden" name="product_data[{$chain.product_id}_{$chain.chain_id}][chain]" value="{$chain.chain_id}" />
		<input type="hidden" name="product_data[{$chain.product_id}_{$chain.chain_id}][product_id]" value="{$chain.product_id}" />
		{include file="common_templates/subheader.tpl" title=$chain.name}
		<div class="chain-content clear">
			<div class="chain-products scroll-x clear nowrap">
			{if $chain.products}
				<div class="chain-product">
					<a href="{"products.view?product_id=`$chain.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height obj_id="`$chain.chain_id`_`$chain.product_id`" images=$chain.main_pair object_type="product" show_thumbnail="Y"}</a>
					<div class="chain-note">{$chain.product_name}</div>
					{if $chain.product_options}
						{capture name="buy_together_product_options"}
							<div id="buy_together_options_{$chain.chain_id}_{$key}" class="chain-product-options">
								<div class="object-container cm-reload-{$obj_prefix}{$chain.product_id}_{$chain.chain_id}" id="buy_together_options_update_{$chain.chain_id}_{$key}">
									<input type="hidden" name="appearance[show_product_options]" value="1" />
									<input type="hidden" name="appearance[bt_chain]" value="{$chain.chain_id}" />
									<input type="hidden" name="appearance[bt_id]" value="{$key}" />
									
									{include file="views/products/components/product_options.tpl" id="`$chain.product_id`_`$chain.chain_id`" product_options=$chain.product_options name="product_data" no_script=true}
								</div>
								<div class="buttons-container">
									{include file="buttons/button.tpl" but_id="add_item_close" but_name="" but_text=$lang.save_and_close but_role="action" but_meta="cm-popup-switch cm-cancel"}
								</div>
							</div>
						{/capture}
					{include file="common_templates/popupbox.tpl" id="buy_together_options_`$chain.chain_id`_`$key`" text=$lang.specify_options content=$smarty.capture.buy_together_product_options link_text=$lang.specify_options act="general"}
					{/if}
					<div class="chain-note"><strong>{$chain.min_qty}</strong>&nbsp;{$lang.items}</div>

					{if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "P")}
					{if $chain.price != $chain.discounted_price}
						<div class="chain-note"><strike>{include file="common_templates/price.tpl" value=$chain.price}</strike></div>
					{/if}
					<div class="chain-note"><strong>{include file="common_templates/price.tpl" value=$chain.discounted_price}</strong></div>
					{/if}
				</div>
			{/if}
			
			{foreach from=$chain.products key="_id" item="_product"}
				<div class="chain-plus">+</div>
				
				<div class="chain-product">
					<input type="hidden" name="product_data[{$_product.product_id}][product_id]" value="{$_product.product_id}" />
					<a href="{"products.view?product_id=`$_product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height obj_id="`$chain.chain_id`_`$_product.product_id`" images=$_product.main_pair object_type="product" show_thumbnail="Y"}</a>
					<div class="chain-note">{$_product.product_name}</div>
					{if $_product.product_options}
						{foreach from=$_product.product_options item="option"}
							<div class="chain-note"><strong>{$option.option_name}</strong>: {$option.variant_name}</div>
						{/foreach}
					{elseif $_product.aoc}
						{capture name="buy_together_product_options"}
							<div id="buy_together_options_{$chain.chain_id}_{$_product.product_id}" class="chain-product-options">
								<div class="object-container cm-reload-{$obj_prefix}{$_product.product_id}" id="buy_together_options_update_{$chain.chain_id}_{$_id}">
									<input type="hidden" name="appearance[show_product_options]" value="1" />
									<input type="hidden" name="appearance[bt_chain]" value="{$chain.chain_id}" />
									<input type="hidden" name="appearance[bt_id]" value="{$_id}" />
									{include file="views/products/components/product_options.tpl" id=$_product.product_id product_options=$_product.options name="product_data" no_script=true product=$_product}
								</div>
								<div class="buttons-container">
									{include file="buttons/button.tpl" but_id="add_item_close" but_name="" but_text=$lang.save_and_close but_role="action" but_meta="cm-popup-switch cm-cancel"}
								</div>
							</div>
						{/capture}
						{include file="common_templates/popupbox.tpl" id="buy_together_options_`$chain.chain_id`_`$_product.product_id`" text=$lang.specify_options content=$smarty.capture.buy_together_product_options link_text=$lang.specify_options act="general"}
						
					{/if}
					<div class="chain-note"><strong>{$_product.amount}</strong>&nbsp;{$lang.items}</div>

					{if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "P")}
					{if $_product.price != $_product.discounted_price}
						<div class="chain-note"><strike>{include file="common_templates/price.tpl" value=$_product.price}</strike></div>
					{/if}
					<div class="chain-note"><strong>{include file="common_templates/price.tpl" value=$_product.discounted_price}</strong></div>
					{/if}
				</div>
			{/foreach}
			</div>
			
			{if $chain.description}
				<div class="chain-description">
					{$chain.description|unescape}
				</div>
			{/if}
			
			{if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "P")}
			<div class="chain-price">
				<div class="chain-old-price">
					<span class="chain-old">{$lang.total_list_price}:</span>
					<span class="chain-old-line">{include file="common_templates/price.tpl" value=$chain.total_price}</span>
				</div>
				<div class="chain-new-price">
					<span class="chain-new">{$lang.price_for_all}:</span>
					{include file="common_templates/price.tpl" value=$chain.chain_price}
				</div>
				
				{if !(!$auth.user_id && $settings.General.allow_anonymous_shopping == "B")}
					<div width="100%" class="right">
						<span class="button-submit-action chain-button" id="wrap_chain_button_{$chain.chain_id}">
							<input type="submit" value="{$lang.add_all_to_cart}" name="dispatch[checkout.add]" id="chain_button_{$chain.chain_id}" />
						</span>
					</div>
				{/if}
			</div>
			{else}
			<p class="price">{$lang.sign_in_to_view_price}</p>
			{/if}
		</div>
		
		</form>
	{/foreach}
	
	</div>
{/if}