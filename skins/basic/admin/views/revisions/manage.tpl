{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}
{capture name="section"}
<form action="{""|fn_url}" name="search_objects_form" method="get">
<input type="hidden" name="object" value="{$smarty.request.object}">
<table class="search-header" cellspacing="0" border="0" width="100%">
<tr>
	<td class="nowrap search-field">
		<label>{$lang.author}:</label>
		<div class="break">
			<input type="text" name="q_author" size="20" value="{$search.q_author}" class="search-input-text" />
			{include file="buttons/search_go.tpl" search="Y" but_name="revisions.manage"}&nbsp;
		</div>
	</td>
	<td class="nowrap search-field">
		<label>{$lang.object_type}:</label>
		<div class="break">
			<select name="object">
				<option value="">--</option>
				{foreach from=$objects_data item="object_data"}
					<option value="{$object_data.object}" {if $object_data.object == $search.object}selected="selected"{/if}>{$object_data.title}</option>
				{/foreach}
			</select>
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[revisions.manage]" but_arrow="on" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}
	{*
	<div class="search-field">
		<label>{$lang.description}:</label>
			<input type="text" name="q" size="20" value="{$search.q}" class="input-text" />
			&nbsp;
			<select name="match">
				<option value="any" {if $search.match == "any"}selected="selected"{/if}>{$lang.any_words}</option>
				<option value="all" {if $search.match == "all"}selected="selected"{/if}>{$lang.all_words}</option>
				<option value="exact" {if $search.match == "exact"}selected="selected"{/if}>{$lang.exact_phrase}</option>
			</select>
	</div>
	*}
	
	<div class="search-field">
		<label>{$lang.period}:</label>
		{include file="common_templates/period_selector.tpl" period=$search.period form_name="search_objects_form"}
	</div>
	
	<div class="search-field">
		<label for="sort_by">{$lang.sort_by}:</label>
		<select name="sort_by" id="sort_by">
			<option {if $search.sort_by == "change_time"}selected="selected"{/if} value="change_time">{$lang.date}</option>
			{* <option {if $search.sort_by == "description"}selected="selected"{/if} value="description">{$lang.description}</option> *}
			<option {if $search.sort_by == "author"}selected="selected"{/if} value="author">{$lang.author}</option>
		</select>
	
		<select name="sort_order">
			<option {if $search.sort_order == "desc"}selected="selected"{/if} value="desc">{$lang.desc}</option>
			<option {if $search.sort_order == "asc"}selected="selected"{/if} value="asc">{$lang.asc}</option>
		</select>
	</div>
{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="revisions.manage" view_type="revisions"}

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}

<form action="{""|fn_url}" method="get" name="select_compare_revisions">
<input type="hidden" name="object" value="{$smarty.request.object}" />
<input type="hidden" name="object_id" value="{$smarty.request.object_id}" />

{include file="common_templates/pagination.tpl"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
{*<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>*}
	<th>
		{$lang.object_type}</th>
	<th>
		{$lang.revision_short}</th>
	<th>
		{$lang.name}</th>
	<th width="100%">
		{$lang.last_action}</th>
	<th colspan="3">
		&nbsp;</th>
</tr>
{foreach from=$objects item="object"}
<tr {cycle values="class=\"table-row\", "}>
{*<td class="center">
		<input type="checkbox" name="revision_ids[]" value="{$object.revision_id}" class="checkbox cm-item" /></td>*}
	<td>
		{$object.object_name}</td>
	<td class="right">
		{$object.revision}</td>
	<td width="60%">
		{$object.description}</td>
	<td width="20%">
		{$object.change_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"} {if $object.type == "D"}<strong>{$lang.deleted}</strong>{elseif $object.type == "C"}{$lang.created}{elseif $object.type == "R"}{$lang.reverted}{elseif $object.type == "A"}<strong>{$lang.approved}</strong>{elseif $object.type == "O"}<strong>{$lang.published}</strong>{elseif $object.type == "P"}{$lang.processed}{elseif $object.type == "E"}{$lang.declined}{else}{$lang.edited}{/if} {$lang.by} {$object.firstname}{if $object.firstname && $object.lastname}&nbsp;{/if}{$object.lastname}</td>
	<td>
		{if $object.changes_link}{include file="buttons/button.tpl" but_text=$lang.changes but_href=$object.changes_link but_role="text"}{else}&nbsp;{/if}</td>
	<td>
		{include file="buttons/button.tpl" but_text=$lang.history but_href=$object.history_link but_role="text"}</td>
	<td>
		{include file="buttons/button.tpl" but_text=$lang.view but_href=$object.view_link but_role="text"}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>
{include file="common_templates/pagination.tpl"}
{*
<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.approve_revisions but_name="dispatch[revisions.approve]" but_role="submit"}</div> *}
</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.revisions content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
