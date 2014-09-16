{* $Id: product_small_list.override.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if !$smarty.session.auth.age && $product.age_verification == "Y"}
<table border="0" cellpadding="3" cellspacing="3" width="100%">
<tr>
	<td width="{$cell_width}%" valign="top">
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a>
		<div class="box margin-top">
			{$lang.product_need_age_verification}
			<div class="buttons-container">
				{include file="buttons/button.tpl" but_text=$lang.verify but_href="products.view?product_id=`$product.product_id`" but_role="text"}
			</div>
		</div>
	</td>
</tr>
</table>
{/if}
