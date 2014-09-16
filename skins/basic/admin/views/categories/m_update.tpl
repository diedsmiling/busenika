{* $Id: m_update.tpl 10293 2010-08-02 11:02:07Z klerik $ *}
{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

<script type="text/javascript">
//<![CDATA[
	var images_dir = '{$images_dir|escape:javascript}';
{literal}
	function fn_generate_scroller()
	{
		elm_orig = document.getElementById('scrolled_div');
		elm_scroller = document.getElementById('scrolled_div_top');
		elm_scroller.innerHTML = '<img src="' + images_dir + '/spacer.gif" width="'+ elm_orig.scrollWidth +'" height="1" />';
		elm_scroller.style.height = '25px';
		elm_scroller.onscroll = function(){document.getElementById('scrolled_div').scrollLeft = document.getElementById('scrolled_div_top').scrollLeft}
		elm_orig.onscroll = function(){document.getElementById('scrolled_div_top').scrollLeft = document.getElementById('scrolled_div').scrollLeft}
	}
{/literal}
	$(document).ready( function() {ldelim}
		fn_generate_scroller();
	{rdelim});
//]]>
</script>
{capture name="mainbox"}
<form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="categories_m_update_form">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="redirect_url" value="{"categories.m_update"|fn_url}" />

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table-fixed">
<tr>
	<td width="100%">
		<div id="scrolled_div_top" class="scroll-x">&nbsp;</div>
		<div id="scrolled_div" class="scroll-x">

		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			{foreach from=$filled_groups item=v}
			<th>&nbsp;</th>
			{/foreach}
			{foreach from=$field_names item="field_name" key=field_key}
			<th>{if $field_name|is_array}{$field_key|fn_get_lang_var}{else}{$field_name}{/if}</th>
			{/foreach}
		</tr>
		{foreach from=$categories_data item="category"}

		<tr {cycle values="class=\"table-row\", "} valign="top">
			{foreach from=$filled_groups item=v key=type}
			<td>
				<table cellpadding="0" cellspacing="0">
				{foreach from=$field_groups.$type item=name key=field}
				{if $v.$field}
				<tr valign="top">
					<td class="nowrap {if $field == "category"}strong{/if}">{$v.$field}:&nbsp;</td>
					<td>
						{if $type == "A"}
						<input type="text" value="{$category.$field}" class="input-text" name="{$name}[{$category.category_id}][{$field}]" />
						{elseif $type == "C"}
						<textarea class="input-text" name="{$name}[{$category.category_id}][{$field}]" rows="3" cols="40">{$category.$field}</textarea>
						{/if}
					</td>
				</tr>
				{/if}
				{/foreach}
				</table>
			</td>
			{/foreach}

			{foreach from=$field_names key="field" item=v}
			<td>
					{if $field == "status"}
						<select name="categories_data[{$category.category_id}][{$field}]">
							<option value="A" {if $category.status == "A"}selected="selected"{/if}>{$lang.active}</option>
							<option value="D" {if $category.status == "D"}selected="selected"{/if}>{$lang.disabled}</option>
							<option value="H" {if $category.status == "H"}selected="selected"{/if}>{$lang.hidden}</option>
						</select>
					{elseif $field == "usergroup_ids"}
						{include file="common_templates/select_usergroups.tpl" id="category_ug_`$category.category_id`" name="categories_data[`$category.category_id`][`$field`]" usergroups="C"|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$category.usergroup_ids input_extra="" list_mode=true}
					{elseif $field == "discussion_type"}
						{include file="addons/discussion/views/discussion_manager/components/bulk_allow_discussion.tpl" prefix="categories_data" object_id=$category.category_id object_type="C"}
					{elseif $field == "image_pair"}
						<table width="320">
						<tr>
							<td>{include file="common_templates/attach_images.tpl" image_key=$category.category_id image_name="category_main" image_object_type="category" image_pair=$category.main_pair image_object_id=$category.category_id no_thumbnail=true}</td>
						</tr>
						</table>
					{elseif $field == "timestamp"}
						{include file="common_templates/calendar.tpl" date_id="date_`$category.category_id`" date_name="categories_data[`$category.category_id`][$field]" date_val=$category.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
					{elseif $field == "localization"}
						{include file="views/localizations/components/select.tpl" no_div=true data_from=$category.localization data_name="categories_data[`$category.category_id`][`$field`]"}
					{elseif $field == "product_details_layout"}
						<select name="categories_data[{$category.category_id}][{$field}]">
						{foreach from="category"|fn_get_product_details_views key="layout" item="item"}
							<option {if $category.product_details_layout == $layout}selected="selected"{/if} value="{$layout}">{$item|escape}</option>
						{/foreach}
						</select>
					{else}
					{assign var="f_category" value=$category.$field}
					<input type="text" value="{$f_category}" class="input-mupdate-{$field}" name="categories_data[{$category.category_id}][{$field}]" />
					{/if}

			</td>
			{/foreach}
		</tr>
		{/foreach}
		</table>
		</div>
	</td>
</tr>
</table>

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[categories.m_update]" but_role="button_main"}
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.update_categories content=$smarty.capture.mainbox select_languages=true}
