{* $Id: products_m_update_supplier.tpl 10598 2010-09-03 11:59:33Z 2tl $ *}

	<select	id="field_{$field}__" name="{if $override_box}override_products_data[{$field}]{else}products_data[{$product.product_id}][{$field}]{/if}" {if $override_box}disabled="disabled"{/if}>
		{foreach from=$s_companies item="company"}
		<option	value="{$company.company_id}" {if $product.$field == $company.company_id} selected="selected"{/if}>{$company.company}</option>
		{/foreach}
	</select>
