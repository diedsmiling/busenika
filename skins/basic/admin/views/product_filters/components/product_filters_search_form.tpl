{* $Id: orders_search_form.tpl 6971 2009-03-05 09:28:18Z zeke $ *}

{capture name="section"}

<form action="{""|fn_url}" name="product_filters_search_form" method="get">

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
		<label for="elm_feature_name">{$lang.feature}:</label>
		<div class="break">
			<input type="text" name="feature_name" id="elm_feature_name" value="{$search.feature_name}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="nowrap search-field">
		<label for="elm_filter_name">{$lang.filter}:</label>
		<div class="break">
			<input type="text" name="filter_name" id="elm_filter_name" value="{$search.filter_name}" size="30" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

{hook name="product_filters:search_form"}
{/hook}

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="product_filters"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
