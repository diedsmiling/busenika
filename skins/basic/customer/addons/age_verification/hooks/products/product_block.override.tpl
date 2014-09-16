{* $Id: product_block.override.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if !$smarty.session.auth.age && $product.age_verification == "Y"}
<div class="product-container clear">
	<div class="product-description">
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>
	</div>
	<div class="box margin-top">
		{$lang.product_need_age_verification}
		<div class="buttons-container">
			{include file="buttons/button.tpl" but_text=$lang.verify but_href="products.view?product_id=`$product.product_id`" but_role="text"}
		</div>
	</div>
</div>
{/if}
