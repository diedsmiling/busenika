{* $Id: update.tpl 10209 2010-07-26 13:31:28Z angel $ *}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{** banners section **}

{assign var="b_type" value=$banner.type|default:"G"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" class="cm-form-highlight" name="banners_form" enctype="multipart/form-data" class="cm-form-highlight">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="banner_id" value="{$smarty.request.banner_id}" />

<div class="form-field">
	<label for="banner" class="cm-required">{$lang.name}:</label>
	<input type="text" name="banner_data[banner]" id="banner" value="{$banner.banner}" size="25" class="input-text-large main-input" />
</div>

<div class="form-field">
	<label for="type" class="cm-required">{$lang.type}:</label>
	<select name="banner_data[type]" id="type" class="input-text" onchange="$('#banner_graphic').toggle();  $('#banner_text').toggle(); $('#banner_url').toggle();  $('#banner_target').toggle();">
		<option {if $banner.type == "G"}selected="selected"{/if} value="G">{$lang.graphic_banner}
		<option {if $banner.type == "T"}selected="selected"{/if} value="T">{$lang.text_banner}
	</select>
</div>

<div class="form-field {if $b_type != "G"}hidden{/if}" id="banner_graphic">
	<label>{$lang.image}:</label>
	<div class="float-left">
		{include file="common_templates/attach_images.tpl" image_name="banners_main" image_object_type="banner" image_pair=$banner.main_pair image_object_id=$banner.banner_id no_detailed=true hide_titles=true}
	</div>
</div>

<div class="form-field {if $b_type == "G"}hidden{/if}" id="banner_text">
	<label for="man_descr">{$lang.description}:</label>
	<div class="break">
		<textarea id="man_descr" name="banner_data[description]" cols="35" rows="8" class="input-textarea-long">{$banner.description}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="man_descr"}
	</div>
</div>

<div class="form-field {if $b_type == "T"}hidden{/if}" id="banner_target">
	<label for="elm_banner_target">{$lang.open_in_new_window}:</label>
	<input type="hidden" name="banner_data[target]" value="T" />
	<input type="checkbox" name="banner_data[target]" id="elm_banner_target" value="B" {if $banner.target == "B"}checked="checked"{/if} class="checkbox" />
</div>

<div class="form-field {if $b_type == "T"}hidden{/if}" id="banner_url">
	<label for="url">{$lang.url}:</label>
	<input type="text" name="banner_data[url]" id="url" value="{$banner.url}" size="25" class="input-text" />
</div>

<div class="form-field">
	<label for="timestamp_{$banner.banner_id}">{$lang.created_date}:</label>
	{include file="common_templates/calendar.tpl" date_id="timestamp_`$banner.banner_id`" date_name="banner_data[timestamp]" date_val=$banner.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
</div>

{include file="views/localizations/components/select.tpl" data_name="banner_data[localization]" data_from=$banner.localization}

{include file="common_templates/select_status.tpl" input_name="banner_data[status]" id="banner_data" obj_id=$banner.banner_id obj=$banner hidden=true}

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[banners.update]"}
</div>

</form>
{/capture}

{if $mode == "add"}
	{assign var="title" value=$lang.new_banner}
{else}
	{assign var="title" value="`$lang.editing_banner`: `$banner.banner`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}

{** banner section **}
