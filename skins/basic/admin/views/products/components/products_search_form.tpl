{* $Id: products_search_form.tpl 10446 2010-08-18 08:26:13Z andyye $ *}

{capture name="section"}
{if $page_part}
    {assign var="_page_part" value="#`$page_part`"}
{/if}
<form action="{"`$index_script``$_page_part`"|fn_url}" name="{$product_search_form_prefix}search_form" method="get" class="cm-disable-empty">
<input type="hidden" name="type" value="{$search_type|default:"simple"}" />
{if $smarty.request.redirect_url}
<input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}
{if $selected_section != ""}
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
{/if}

{$extra}

<table cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.find_results_with}:</label>
		<div class="break">
			<input type="text" name="q" size="20" value="{$search.q}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="$dispatch"}&nbsp;
			<select name="match">
				<option value="any" {if $search.match == "any"}selected="selected"{/if}>{$lang.any_words}</option>
				<option value="all" {if $search.match == "all"}selected="selected"{/if}>{$lang.all_words}</option>
				<option value="exact" {if $search.match == "exact"}selected="selected"{/if}>{$lang.exact_phrase}</option>
			</select>
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.price}&nbsp;({$currencies.$primary_currency.symbol}):</label>
		<div class="break">
			<input type="text" name="price_from" size="1" value="{$search.price_from}" onfocus="this.select();" class="input-text-price" />&nbsp;&ndash;&nbsp;<input type="text" size="1" name="price_to" value="{$search.price_to}" onfocus="this.select();" class="input-text-price" />
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.search_in_category}:</label>
		<div class="break clear correct-picker-but">
		{if "categories"|fn_show_picker:$smarty.const.CATEGORY_SHOW_ALL}
			{if $search.cid}
				{assign var="s_cid" value=$search.cid}
			{else}
				{assign var="s_cid" value="0"}
			{/if}
			{include file="pickers/categories_picker.tpl" data_id="location_category" input_name="cid" item_ids=$s_cid hide_link=true hide_delete_button=true show_root=true default_name=$lang.all_categories extra=""}
		{else}
			<select	name="cid">
				<option	value="0" {if $category_data.parent_id == "0"}selected="selected"{/if}>- {$lang.all_categories} -</option>
				{foreach from=0|fn_get_plain_categories_tree:false item="search_cat"}
					<option	value="{$search_cat.category_id}" {if $search.cid == $search_cat.category_id}selected="selected"{/if}>{$search_cat.category|indent:$search_cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
				{/foreach}
			</select>
		{/if}
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[$dispatch]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.search_in}:</label>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="select-field">
			<input type="checkbox" value="Y" {if $search.pname == "Y"}checked="checked"{/if} name="pname" id="pname" class="checkbox" /><label for="pname">{$lang.product_name}</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>

		<td class="select-field"><input type="checkbox" value="Y" {if $search.pshort == "Y"}checked="checked"{/if} name="pshort" id="pshort" class="checkbox" /><label for="pshort">{$lang.short_description}</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>

		<td class="select-field"><input type="checkbox" value="Y" {if $search.subcats == "Y"}checked="checked"{/if} name="subcats" class="checkbox" id="subcats" /><label for="subcats">{$lang.subcategories}</label></td>
	</tr>
	<tr>
		<td class="select-field"><input type="checkbox" value="Y" {if $search.pfull == "Y"}checked="checked"{/if} name="pfull" id="pfull" class="checkbox" /><label for="pfull">{$lang.full_description}</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td class="select-field"><input type="checkbox" value="Y" {if $search.pkeywords == "Y"}checked="checked"{/if} name="pkeywords" id="pkeywords" class="checkbox" /><label for="pkeywords">{$lang.keywords}</label></td>
		<td colspan="2">&nbsp;</td>
	</tr>
	</table>
</div>
<hr />
{if $filter_items}
<div class="search-field">
	<label>
		<a class="search-link cm-combination cm-combo-off cm-save-state" id="sw_filter"><img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="" id="on_filter" class="cm-combination cm-save-state {if $smarty.cookies.filter}hidden{/if}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="" id="off_filter" class="cm-combination cm-save-state {if !$smarty.cookies.filter}hidden{/if}" />{$lang.search_by_product_filters}</a>:
	</label>
	<div id="filter"{if !$smarty.cookies.filter} class="hidden"{/if}>
		{include file="views/products/components/advanced_search_form.tpl" filter_features=$filter_items prefix="filter_"}
	</div>
</div>
{/if}
{if $feature_items}
<div class="search-field">
	<label>
		<a class="search-link cm-combination cm-combo-off cm-save-state" id="sw_feature"><img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="" id="on_feature" class="cm-combination cm-save-state {if $smarty.cookies.feature}hidden{/if}" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="" id="off_feature" class="cm-combination cm-save-state {if !$smarty.cookies.feature}hidden{/if}" />{$lang.search_by_product_features}</a>:
	</label>
	<div id="feature"{if !$smarty.cookies.feature} class="hidden"{/if}>
		<input type="hidden" name="advanced_filter" value="Y" />
		{include file="views/products/components/advanced_search_form.tpl" filter_features=$feature_items prefix="feature_"}
	</div>
</div>
{/if}

<div class="search-field">
	<label for="pcode">{$lang.search_by_sku}:</label>
	<input type="text" name="pcode" id="pcode" value="{$search.pcode}" onfocus="this.select();" class="input-text" />
</div>

<hr />
{hook name="products:search_form"}
{/hook}
{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR" || $settings.Suppliers.enable_suppliers == "Y"}
	{if $smarty.const.PRODUCT_TYPE == "MULTIVENDOR"}
		{assign var="lang_search_by_vendor_supplier" value=$lang.search_by_vendor}
		{assign var="lang_all_vendors_suppliers" value=$lang.all_vendors}
	{elseif $smarty.const.PRODUCT_TYPE == "PROFESSIONAL" || $smarty.const.PRODUCT_TYPE == "COMMUNITY"}
		{assign var="lang_search_by_vendor_supplier" value=$lang.search_by_supplier}
		{assign var="lang_all_vendors_suppliers" value=$lang.all_suppliers}
	{/if}
	<div class="search-field">
		<label for="company_id">{$lang_search_by_vendor_supplier}:</label>
		<select	name="company_id" id="company_id">
			<option	value=""{if strval($search.company_id) == ''} selected="selected"{/if}>- {$lang_all_vendors_suppliers} -</option>
			{foreach from=$companies item="company" key="company_id"}
				<option	value="{$company_id}" {if strval($search.company_id) == strval($company_id)}selected="selected"{/if}>{$company}</option>
			{/foreach}
		</select>
	</div>
{/if}
<div class="search-field">
	<label for="shipping_freight_from">{$lang.shipping_freight}&nbsp;({$currencies.$primary_currency.symbol}):</label>
	<input type="text" name="shipping_freight_from" id="shipping_freight_from" value="{$search.shipping_freight_from}" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="shipping_freight_to" value="{$search.shipping_freight_to}" onfocus="this.select();" class="input-text" />
</div>

<div class="search-field">
	<label for="weight_from">{$lang.weight}&nbsp;({$settings.General.weight_symbol}):</label>
	<input type="text" name="weight_from" id="weight_from" value="{$search.weight_from}" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="weight_to" value="{$search.weight_to}" onfocus="this.select();" class="input-text" />
</div>

{assign var="have_amount_filter" value=0}
{foreach from=$filter_items item="ff"}{if $ff.field_type eq "A"}{assign var="have_amount_filter" value=1}{/if}{/foreach}
{if !$have_amount_filter}
<div class="search-field">
	<label for="amount_from">{$lang.quantity}:</label>
	<input type="text" name="amount_from" id="amount_from" value="{$search.amount_from}" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="amount_to" value="{$search.amount_to}" onfocus="this.select();" class="input-text" />
</div>
{/if}

<hr />

<div class="search-field">
	<label for="free_shipping">{$lang.free_shipping}:</label>
	<select name="free_shipping" id="free_shipping">
		<option value="">--</option>
		<option value="Y" {if $search.free_shipping == "Y"}selected="selected"{/if}>{$lang.yes}</option>
		<option value="N" {if $search.free_shipping == "N"}selected="selected"{/if}>{$lang.no}</option>
	</select>
</div>

<div class="search-field">
	<label for="status">{$lang.status}:</label>
	<select name="status" id="status">
		<option value="">--</option>
		<option value="A" {if $search.status == "A"}selected="selected"{/if}>{$lang.active}</option>
		<option value="H" {if $search.status == "H"}selected="selected"{/if}>{$lang.hidden}</option>
		<option value="D" {if $search.status == "D"}selected="selected"{/if}>{$lang.disabled}</option>
	</select>
</div>

<hr />

<div class="search-field">
	<label for="popularity_from">{$lang.popularity}:</label>
	<input type="text" name="popularity_from" id="popularity_from" value="{$search.popularity_from}" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="popularity_to" value="{$search.popularity_to}" onfocus="this.select();" class="input-text" />
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="products"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
