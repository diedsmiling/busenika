{* $Id: products_search_form.tpl 6762 2009-01-15 09:20:26Z angel $ *}

{capture name="section"}

<form action="{""|fn_url}" name="subscribers_search_form" method="get">

<table cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.email}:</label>
		<div class="break">
			<input type="text" name="email" size="20" value="{$search.email}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="$dispatch"}&nbsp;
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.mailing_list}:</label>
		<div class="break">
			<select	name="list_id">
				<option	value="">--</option>
				{foreach from=$mailing_lists key="m_id" item="m"}
					<option	value="{$m_id}" {if $search.list_id == $m_id}selected="selected"{/if}>{$m}</option>
				{/foreach}
			</select>
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.confirmed}:</label>
		<div class="break">
			<select	name="confirmed">
				<option	value="">--</option>
				<option	value="Y" {if $search.confirmed == "Y"}selected="selected"{/if}>{$lang.yes}</option>
				<option	value="N" {if $search.confirmed == "N"}selected="selected"{/if}>{$lang.no}</option>
			</select>
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[$dispatch]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<div class="search-field">
	<label for="elm_search_format">{$lang.format}:</label>
	<select id="elm_search_format" name="format">
		<option value="">--</option>
		<option {if $search.format == $smarty.const.NEWSLETTER_FORMAT_TXT}selected="selected"{/if} value="{$smarty.const.NEWSLETTER_FORMAT_TXT}">{$lang.txt_format}</option>
		<option {if $search.format == $smarty.const.NEWSLETTER_FORMAT_HTML}selected="selected"{/if} value="{$smarty.const.NEWSLETTER_FORMAT_HTML}">{$lang.html_format}</option>
	</select>
</div>

<div class="search-field">
	<label for="elm_search_language">{$lang.language}:</label>
	<select id="elm_search_language" name="language">
		<option value="">--</option>
		{foreach from=$languages item="lng"}
		<option {if $search.language == $lng.lang_code}selected="selected"{/if} value="{$lng.lang_code}">{$lng.name}</option>
		{/foreach}
	</select>
</div>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="subscribers_search_form"}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="subscribers"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
