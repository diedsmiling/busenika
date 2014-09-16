{* $Id: company_field.tpl 7394 2009-04-29 11:43:22Z 2tl $ *}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR" || ($settings.Suppliers.enable_suppliers == "Y" && ($smarty.const.CONTROLLER == "products" || $smarty.const.CONTROLLER == "shippings"))}

{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
{assign var="lang_vendor_supplier" value=$lang.vendor}
{else}
{assign var="lang_vendor_supplier" value=$lang.supplier}
{/if}

<div class="form-field">
	<label for="{$id|default:"company_id"}">{$lang_vendor_supplier}:</label>
	{if "COMPANY_ID"|defined}
		{$companies[$smarty.const.COMPANY_ID]}
		<input type="hidden" name="{$name}" id="{$id|default:"company_id"}" value="{$smarty.const.COMPANY_ID}">
	{else}
	<select name="{$name}" id="{$id|default:"company_id"}" class="select-box">
	{foreach from=$companies item="company" key="company_id"}
		<option value="{$company_id}" {if $company_id == $selected}selected="selected"{/if}>{$company}</option>
	{/foreach}
	</select>
	{/if}
</div>

{/if}