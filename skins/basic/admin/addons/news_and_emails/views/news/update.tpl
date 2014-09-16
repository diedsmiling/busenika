{* $Id: update.tpl 10209 2010-07-26 13:31:28Z angel $ *}

{capture name="mainbox"}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" name="news_form" class="cm-form-highlight">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="news_id" value="{$smarty.request.news_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section|default:"detailed"}" />

<div id="content_detailed">
<fieldset>
	<div class="form-field">
		<label for="news" class="cm-required">{$lang.name}:</label>
		<input type="text" name="news_data[news]" id="news" value="{$news_data.news}" size="40" class="input-text main-input" />
	</div>

	<div class="form-field">
		<label for="news_description">{$lang.description}:</label>
		<textarea id="news_description" name="news_data[description]" cols="35" rows="8" class="input-textarea-long">{$news_data.description}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="news_description"}
	</div>

	<div class="form-field">
		<label for="news_date">{$lang.date}:</label>
		{include file="common_templates/calendar.tpl" date_id="news_date" date_name="news_data[date]" date_val=$news_data.date start_year=$settings.Company.company_start_year}
	</div>

	<div class="form-field">
		<label for="news_separate">{$lang.show_on_separate_page}:</label>
		<input type="hidden" name="news_data[separate]" value="N" />
		<input type="checkbox" name="news_data[separate]" id="news_separate" value="Y" {if $news_data.separate == "Y"}checked="checked"{/if} class="checkbox" />
	</div>

	{include file="views/localizations/components/select.tpl" data_from=$news_data.localization data_name="news_data[localization]"}

	{hook name="news_and_emails:detailed_content"}
	{/hook}

	{include file="common_templates/select_status.tpl" input_name="news_data[status]" id="news" obj_id=$news_data.news_id obj=$news_data}
</fieldset>
</div>

{if $mode == "update"}
<div id="content_blocks">
	{include file="views/block_manager/components/select_blocks.tpl" object_id=$news_data.news_id data_name="news_data" section="news"}
</div>
{/if}

<div id="content_addons">
{hook name="news:detailed_content"}
{/hook}
</div>

{hook name="news_and_emails:tabs_content"}
{/hook}

<div class="buttons-container cm-toggle-button buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[news.update]"}
</div>

</form>

{if $mode == "update"}
{hook name="news_and_emails:tabs_extra"}
{/hook}
{/if}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{if $mode == "update"}
	{assign var="title" value="`$lang.editing_news`:&nbsp;`$news_data.news`"}
{else}
	{assign var="title" value=$lang.new_news}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
