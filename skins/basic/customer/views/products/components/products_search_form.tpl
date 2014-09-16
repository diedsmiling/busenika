{* $Id: products_search_form.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{capture name="section"}

<form action="{""|fn_url}" name="advanced_search_form" method="get">
<input type="hidden" name="type" value="extended" />
<input type="hidden" name="search_performed" value="Y" />

{$search_extra}

<div class="form-field">
	<label for="match">{$lang.find_results_with}:</label>
	<select name="match" id="match" class="valign">
		<option {if $search.match == "any"}selected="selected"{/if} value="any">{$lang.any_words}</option>
		<option {if $search.match == "all"}selected="selected"{/if} value="all">{$lang.all_words}</option>
		<option {if $search.match == "exact"}selected="selected"{/if} value="exact">{$lang.exact_phrase}</option>
	</select>&nbsp;&nbsp;
	<input type="text" name="q" size="38" value="{$search.q}" class="input-text-large valign" />
</div>

<div class="form-field">
	<label>{$lang.search_in}:</label>
	<div class="select-field">
		<label for="pname">
			<input type="hidden" name="pname" value="N" />
			<input type="checkbox" value="Y" {if $search.pname == "Y" || !$search.pname}checked="checked"{/if} name="pname" id="pname" class="checkbox" />{$lang.product_name}
		</label>

		<label for="pshort">
			<input type="checkbox" value="Y" {if $search.pshort == "Y"}checked="checked"{/if} name="pshort" id="pshort" class="checkbox" />{$lang.short_description}
		</label>

		<label for="pfull">
			<input type="checkbox" value="Y" {if $search.pfull == "Y"}checked="checked"{/if} name="pfull" id="pfull" class="checkbox" />{$lang.full_description}
		</label>

		<label for="pkeywords">
			<input type="checkbox" value="Y" {if $search.pkeywords == "Y"}checked="checked"{/if} name="pkeywords" id="pkeywords" class="checkbox" />{$lang.keywords}
		</label>

		{hook name="products:search_in"}{/hook}
	</div>
</div>

<div class="form-field">
	<label>{$lang.search_in_category}:</label>
	{if "categories"|fn_show_picker:$smarty.const.CATEGORY_SHOW_ALL}
		{if $search.cid}
			{assign var="s_cid" value=$search.cid}
		{else}
			{assign var="s_cid" value="0"}
		{/if}
		{include file="pickers/categories_picker.tpl" data_id="location_category" input_name="cid" item_ids=$s_cid hide_link=true hide_delete_button=true show_root=true default_name=$lang.all_categories extra=""}
	{else}
	<div class="float-left">{* dont delete this div. its really needed! *}
		{assign var="all_categories" value=0|fn_get_plain_categories_tree:false}
		<select	name="cid" class="valign">
			<option	value="0" {if $category_data.parent_id == "0"}selected{/if}>- {$lang.all_categories} -</option>
			{foreach from=$all_categories item="cat"}
			<option	value="{$cat.category_id}"{if $search.cid == $cat.category_id} selected="selected"{/if}>{$cat.category|escape|indent:$cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
			{/foreach}
		</select>
	</div>
	{/if}
	<div class="select-field subcategories">
		<label for="subcats">
			<input type="checkbox" value="Y"{if $search.subcats == "Y"} checked="checked"{/if} name="subcats" id="subcats" class="checkbox" />
			{$lang.search_in_subcategories}
		</label>
	</div>
</div>

{if !$simple_search_form}
	{include file="common_templates/subheader.tpl" title=$lang.advanced_search_options}

	<div class="form-field">
		<label for="pcode">{$lang.search_by_sku}:</label>
		<input type="text" name="pcode" id="pcode" value="{$search.pcode}" onfocus="this.select();" class="input-text" size="30" />
	</div>

	{assign var="have_price_filter" value=0}
	{foreach from=$filter_features item="ff"}{if $ff.field_type eq "P"}{assign var="have_price_filter" value=1}{/if}{/foreach}
	{if !$have_price_filter}
	<div class="form-field">
		<label for="price_from">{$lang.search_by_price}&nbsp;({$currencies.$primary_currency.symbol}):</label>
		<input type="text" name="price_from" id="price_from" value="{$search.price_from}" onfocus="this.select();" class="input-text" size="30" />&nbsp;-&nbsp;<input type="text" name="price_to" value="{$search.price_to}" onfocus="this.select();" class="input-text" size="30" />
	</div>
	{/if}

	<div class="form-field">
		<label for="weight_from">{$lang.search_by_weight}&nbsp;({if $config.localization.weight_symbol}{$config.localization.weight_symbol}{else}{$settings.General.weight_symbol}{/if}):</label>
		<input type="text" name="weight_from" id="weight_from" value="{$search.weight_from}" onfocus="this.select();" class="input-text" size="30" />&nbsp;-&nbsp;<input type="text" name="weight_to" value="{$search.weight_to}" onfocus="this.select();" class="input-text" size="30" />
	</div>
	{include file="views/products/components/product_filters_advanced_form.tpl"}
{/if}

<div class="buttons-container">
	{include file="buttons/search.tpl" but_name="dispatch[`$dispatch`]"}&nbsp;{$lang.or}&nbsp;&nbsp;<a class="tool-link cm-reset-link">{$lang.reset}</a>
</div>

</form>

{/capture}
{include file="common_templates/section.tpl" section_title=$lang.search_options section_content=$smarty.capture.section class="search-form"}
