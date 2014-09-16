{* $Id: additional_data.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{if $data}
	{foreach from=$data key="key_name" item="item_id" name="for_add_data"}
		{if $key_name == "O"}
			<p>{$lang.order}: <a href="{"orders.details?order_id=`$item_id`"|fn_url}">#{$item_id}</a> {include file="common_templates/status.tpl" display="view" status=$data.order_status}</p>
		{elseif $key_name == "P" && $data.product_name}
			<p>{$lang.product}: <a href="{"products.update?product_id=`$item_id`"|fn_url}">{$data.product_name}</a></p>
		{elseif $key_name == "D" && $data.coupon.coupon_code}
			{if $data.coupon.type == "G"}
				{assign var="_mode" value="globals"}
			{elseif $data.coupon.type == "C"}
				{assign var="_mode" value="categories"}
			{elseif $data.coupon.type == "P"}
				{assign var="_mode" value="products"}
			{elseif $data.coupon.type == "U"}
				{assign var="_mode" value="users"}
			{/if}
			<p>{$lang.coupon_code}: <a href="{"discounts.`$_mode`?discount_id=`$item_id`"}">{$data.coupon.coupon_code}</a></p>
		{elseif $key_name == "R" && $item_id}
			 <p>{$lang.url}: <a href="{$item_id|fn_url}" target="_blank">{$item_id}</a></p>
		{/if}
	{/foreach}
{/if}
