{* $Id: orders_search_form.tpl 6971 2009-03-05 09:28:18Z zeke $ *}

{capture name="section"}

<form action="{""|fn_url}" name="product_features_search_form" method="get">

<table cellpadding="10" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.category}:</label>
		<div class="break clear correct-picker-but">
		{if "categories"|fn_show_picker:$smarty.const.CATEGORY_SHOW_ALL}
			{if $search.category_ids}
				{assign var="s_cid" value=$search.category_ids}
			{else}
				{assign var="s_cid" value="0"}
			{/if}
			{include file="pickers/categories_picker.tpl" data_id="location_category" input_name="category_ids" item_ids=$s_cid hide_link=true hide_delete_button=true show_root=true default_name=$lang.all_categories extra=""}
		{else}
			<select	name="category_ids">
				<option	value="0" {if $category_data.parent_id == "0"}selected="selected"{/if}>- {$lang.all_categories} -</option>
				{foreach from=0|fn_get_plain_categories_tree:false item="search_cat"}
					<option	value="{$search_cat.category_id}" {if $search.category_ids == $search_cat.category_id}selected="selected"{/if}>{$search_cat.category|indent:$search_cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
				{/foreach}
			</select>
		{/if}
		</div>
	</td>
	<td class="nowrap search-field">
		<label for="fname">{$lang.feature}:</label>
		<div class="break">
			<input type="text" name="description" id="fname" value="{$search.description}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.type}:</label>

	<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td class="select-field">
			<input id="elm_checkbox_single" class="checkbox" type="checkbox" name="feature_types[]" {if "C"|in_array:$search.feature_types}checked="checked"{/if} value="C"/>
			<label for="elm_checkbox_single">{$lang.checkbox}:&nbsp;{$lang.single}</label>
		</td>
		<td class="select-field">
			<input id="elm_checkbox_multiple" class="checkbox" type="checkbox" name="feature_types[]" {if "M"|in_array:$search.feature_types}checked="checked"{/if} value="M"/>
			<label for="elm_checkbox_multiple">{$lang.checkbox}:&nbsp;{$lang.multiple}</label>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="select-field">
			<input id="elm_selectbox_text" class="checkbox" type="checkbox" name="feature_types[]" {if "S"|in_array:$search.feature_types}checked="checked"{/if} value="S"/>
			<label for="elm_selectbox_text">{$lang.selectbox}:&nbsp;{$lang.text}</label>
		</td>
		<td class="select-field">
			<input id="elm_selectbox_number" class="checkbox" type="checkbox" name="feature_types[]" {if "N"|in_array:$search.feature_types}checked="checked"{/if} value="N"/>
			<label for="elm_selectbox_number">{$lang.selectbox}:&nbsp;{$lang.number}</label>
		</td>
		<td class="select-field">
			<input id="elm_selectbox_extended" class="checkbox" type="checkbox" name="feature_types[]" {if "E"|in_array:$search.feature_types}checked="checked"{/if} value="E"/>
			<label for="elm_selectbox_extended">{$lang.selectbox}:&nbsp;{$lang.extended}</label>
		</td>
	</tr>
	<tr>
		<td class="select-field">
			<input id="elm_others_text" class="checkbox" type="checkbox" name="feature_types[]" {if "T"|in_array:$search.feature_types}checked="checked"{/if} value="T"/>
			<label for="elm_others_text">{$lang.others}:&nbsp;{$lang.text}</label>
		</td>
		<td class="select-field">
			<input id="elm_others_number" class="checkbox" type="checkbox" name="feature_types[]" {if "O"|in_array:$search.feature_types}checked="checked"{/if} value="O"/>
			<label for="elm_others_number">{$lang.others}:&nbsp;{$lang.number}</label>
		</td>
		<td class="select-field">
			<input id="elm_others_date" class="checkbox" type="checkbox" name="feature_types[]" {if "D"|in_array:$search.feature_types}checked="checked"{/if} value="D"/>
			<label for="elm_others_date">{$lang.others}:&nbsp;{$lang.date}</label>
		</td>
	</tr>
	</table>
</div>

<div class="search-field">
	<label for="elm_display_on">{$lang.display_on}:</label>
	<select name="display_on" id="elm_display_on">
		<option value="">--</option>
		<option value="product" {if $search.display_on == "product"}selected="selected"{/if}>{$lang.product}</option>
		<option value="catalog" {if $search.display_on == "catalog"}selected="selected"{/if}>{$lang.catalog_pages}</option>
	</select>
</div>

<div class="search-field">
	<label for="elm_parent_id">{$lang.group}:</label>
	<select name="parent_id" id="elm_parent_id">
		<option value="">--</option>
		<option {if $search.parent_id === "0"}selected="selected"{/if} value="0">{$lang.ungroupped_features}</option>
		{foreach from=$group_features item="group_feature"}
			<option value="{$group_feature.feature_id}"{if $group_feature.feature_id == $search.parent_id}selected="selected"{/if}>{$group_feature.description}</option>
		{/foreach}
	</select>
</div>

{hook name="product_features:search_form"}
{/hook}

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="product_features"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
