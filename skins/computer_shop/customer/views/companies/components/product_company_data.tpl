{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
{assign var="lang_vendor_supplier" value=$lang.vendor}
{else}
{assign var="lang_vendor_supplier" value=$lang.supplier}
{/if}

		{if $company_id && $settings.Suppliers.display_supplier == "Y"}
			<div class="form-field product-list-field">
				<label>{$lang_vendor_supplier}:</label>
				{$s_companies[$company_id].company}
			</div>
		{/if}