{* $Id: search_form.tpl 10294 2010-08-02 11:04:41Z andyye $ *}

{capture name="section"}

<form action="{""|fn_url}" name="{$key}_filter_form" method="get">
<input type="hidden" name="report" value="{$report_data.report}" />
<input type="hidden" name="reports_group" value="{$reports_group}" />
<input type="hidden" name="selected_section" value="{$report_data.report}" />
{$extra}

{include file="common_templates/period_selector.tpl" period=$search.period extra="" display="form" but_name="dispatch[`$dispatch`]"}

{if !$hide_advanced}
{capture name="advanced_search"}


<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<td>

		<div class="search-field">
			<label for="filter_search_phrase">{$lang.search_phrase}:</label>
			<input type="text" name="search_phrase" id="filter_search_phrase" value="{$search.search_phrase}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_referrer_url">{$lang.referrer_url}:</label>
			<input type="text" name="referrer_url" id="filter_referrer_url" value="{$search.referrer_url}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_url">{$lang.url}:</label>
			<input type="text" name="url" id="filter_url" value="{$search.url}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_page_title">{$lang.page_title}:</label>
			<input type="text" name="page_title" id="filter_page_title" value="{$search.page_title}" size="10" class="input-text-medium" />
		</div>
	  
		<div class="search-field">
			<label for="filter_ip_address">{$lang.ip_address}:</label>
			<input type="text" name="ip_address" id="filter_ip_address" value="{$search.ip_address}" size="10" class="input-text-medium" />
		</div>

	</td>
	<td>

		<div class="search-field">
			<label for="filter_browser_name">{$lang.browser_name}:</label>
			<input type="text" name="browser_name" id="filter_browser_name" value="{$search.browser_name}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_browser_version">{$lang.browser_version}:</label>
			<input type="text" name="browser_version" id="filter_browser_version" value="{$search.browser_version}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_operating_system">{$lang.operating_system}:</label>
			<input type="text" name="operating_system" id="filter_operating_system" value="{$search.operating_system}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_language">{$lang.language}:</label>
			<input type="text" name="language" id="filter_language" value="{$search.language}" size="10" class="input-text-medium" />
		</div>

		<div class="search-field">
			<label for="filter_country">{$lang.country}:</label>
			<input type="text" name="country" id="filter_country" value="{$search.country}" size="10" class="input-text-medium" />
		</div>
	</td>
</tr>
</table>

<hr />

<div class="search-field">
	<label for="filter_exclude_condition">{$lang.exclude}:</label>
	<input type="checkbox" name="exclude_condition" id="filter_exclude_condition" value="Y" {if $search.exclude_condition == "Y"}checked="checked"{/if} class="checkbox" />
</div>

<div class="search-field">
	<label for="filter_limit">{$lang.limit}:</label>
	<input type="text" name="limit" id="filter_limit" value="{$search.limit}" class="input-text-short cm-value-integer" />
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="statistics"}
{/if}
</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
