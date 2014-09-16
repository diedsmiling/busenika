{* $Id: notification_product.override.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $product.extra.buy_together}
	<li>
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product_id|fn_get_product_name|escape}</a>
	</li>
	<li>
		<strong class="valign">{$product.amount}</strong>&nbsp;x&nbsp;{include file="common_templates/price.tpl" value=$product.display_price span_id="price_`$key`" class="none"}
	</li>
	{if $product.product_option_data}
		<li>{include file="common_templates/options_info.tpl" product_options=$product.product_option_data}</li>
	{/if}
	<li><ul>
	{foreach from=$added_products item="_product" key="_key"}
		{if $_product.extra.parent.buy_together == $key}
			<li>
				<a href="{"products.view?product_id=`$_product.product_id`"|fn_url}">{$_product.product_id|fn_get_product_name|escape}</a>
			</li>
			<li>
				<strong class="valign">{$_product.amount}</strong>&nbsp;x&nbsp;{include file="common_templates/price.tpl" value=$_product.display_price span_id="price_`$_key`" class="none"}
			</li>
			{if $_product.product_option_data}
				<li>{include file="common_templates/options_info.tpl" product_options=$_product.product_option_data}</li>
			{/if}
		{/if}
	{/foreach}
	</ul></li>
{elseif $product.extra.parent.buy_together}
	&nbsp;
{/if}