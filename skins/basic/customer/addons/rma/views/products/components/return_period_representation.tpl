{* $Id: return_period_representation.tpl 8676 2010-01-25 09:42:38Z angel $ *}

{if $addons.rma.display_product_return_period == "Y" && $product.return_period && $product.is_returnable == "Y"}
	<div class="form-field{if !$capture_options_vs_qty} product-list-field{/if}">
		<label>{$lang.return_period}:</label>
		<span class="valign">{$product.return_period}&nbsp;{$lang.days}</span>
	</div>
{/if}
