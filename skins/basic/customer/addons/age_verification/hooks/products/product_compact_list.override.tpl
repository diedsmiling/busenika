{* $Id: product_compact_list.override.tpl 9685 2010-06-01 10:23:29Z klerik $ *}

{if !$smarty.session.auth.age && $product.age_verification == "Y"}
	<tr {cycle values=",class=\"table-row\""} valign="middle">
		<td class="compact" colspan="{if $show_add_to_cart}5{else}4{/if}">
			<div class="product-description">
				{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
			</div>
			<div class="box margin-top">
				{$lang.product_need_age_verification}
				<div class="buttons-container">
					{include file="buttons/button.tpl" but_text=$lang.verify but_href="products.view?product_id=`$product.product_id`" but_role="text"}
				</div>
			</div>
		</td>
	</tr>
{/if}
