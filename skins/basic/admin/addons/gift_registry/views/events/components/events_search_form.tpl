{* $Id: events_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}
<form action="{""|fn_url}" method="get" name="events_search">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="title">{$lang.title}:</label>
		<div class="break">
			<input class="input-text" name="title" id="title" size="25" type="text" value="{$search.title}" />
		</div>
	</td>
	<td class="search-field">
		<label for="owner">{$lang.owner}:</label>
		<div class="break">
		<input class="input-text" name="owner" id="owner" size="25" type="text" value="{$search.owner}" />
		</div>
	</td>
	<td class="search-field">
		<label for="subscriber">{$lang.subscriber}:</label>
		<div class="break">
			<input class="input-text" name="subscriber" id="subscriber" size="25" type="text" value="{$search.subscriber}" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[events.search]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
		<div class="search-field">
			<label for="status">{$lang.status}:</label>
			<select name="status" id="status">
				<option value="">--</option>
				<option {if $search.status == "A"}selected="selected"{/if} value="A">{$lang.awaiting}</option>
				<option {if $search.status == "P"}selected="selected"{/if} value="P">{$lang.in_progress}</option>
				<option {if $search.status == "F"}selected="selected"{/if} value="F">{$lang.finished}</option>
			</select>&nbsp;&nbsp;&nbsp;
		</div>
	</td>
	<td>
		<div class="search-field">
			<label for="type">{$lang.type}:</label>
			<select name="type" id="type">
				<option value="">--</option>
				<option {if $search.type == "P"}selected="selected"{/if} value="P">{$lang.public}</option>
				<option {if $search.type == "U"}selected="selected"{/if} value="U">{$lang.private}</option>
				<option {if $search.type == "D"}selected="selected"{/if} value="D">{$lang.disabled}</option>
			</select>
		</div>
	</td>
</tr>
</table>

<div class="search-field">
	<label>{$lang.period}:</label>
	{include file="common_templates/period_selector.tpl" period=$search.period form_name="events_search"}
</div>

{foreach from=$event_fields item=field}
{assign var="f_id" value=$field.field_id}
<div class="search-field">
	<label for="search_fields_{$field.field_id}">{$field.description}:&nbsp;</label>
	{if $field.field_type == "S" || $field.field_type == "R"}
		<select name="search_fields[{$field.field_id}]" id="search_fields_{$field.field_id}">
			<option value=""> -- </option>
			{foreach from=$field.variants item=var}
				<option value="{$var.variant_id}" {if $search.search_fields.$f_id == $var.variant_id}selected="selected"{/if}>{$var.description}</option>
			{/foreach}
		</select>
	{elseif $field.field_type == "C"}
	    <select name="search_fields[{$field.field_id}]" id="search_fields_{$field.field_id}">
			<option value=""> -- </option>
			<option value="Y" {if $search.search_fields.$f_id == "Y"}selected="selected"{/if}>{$lang.yes}</option>
			<option value="N" {if $search.search_fields.$f_id == "N"}selected="selected"{/if}>{$lang.no}</option>
		</select>
	{elseif $field.field_type == "I" || $field.field_type == "T"}
		<input class="input-text" size="50" type="text" name="search_fields[{$field.field_id}]" value="{$search.search_fields.$f_id}" id="search_fields_{$field.field_id}" />
	{elseif $field.field_type == "V"}
		{include file="common_templates/calendar.tpl" date_id="search_date_`$field.field_id`" date_name="search_fields[`$field.field_id`]" date_val=$search.search_fields.$f_id start_year="1970" end_year="5"}
	{/if}
</div>
{/foreach}

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="events.search" view_type="events"}

</form>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
