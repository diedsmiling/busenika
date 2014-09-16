{* $Id: search.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{if $action != "today_events"}

{literal}
<script type="text/javascript">
//<![CDATA[
var fields = new Array('start_date[Date_Month]', 'start_date[Date_Day]' , 'start_date[Date_Year]', 'end_date[Date_Month]', 'end_date[Date_Day]' , 'end_date[Date_Year]');

function fn_disable_select_date(disable)
{
	for (i in fields) {
		document.events_search.elements[fields[i]].disabled = disable;
	}
}
//]]>
</script>
{/literal}

{capture name="section"}
<form action="{""|fn_url}" method="get" name="events_search">
{if $access_key}
<input type="hidden" name="access_key" value="{$access_key}" />
{/if}

{include file="common_templates/period_selector.tpl" period=$smarty.request.period form_name="events_search"}

<div class="form-field">
	<label for="title">{$lang.title}:</label>
	<input class="input-text" name="title" id="title" size="50" type="text" value="{$smarty.request.title}" />
</div>

<div class="form-field">
	<label for="owner">{$lang.owner}:</label>
	<input class="input-text" name="owner" id="owner" size="25" type="text" value="{$smarty.request.owner}" />
</div>

<div class="form-field">
	<label for="subscriber">{$lang.subscriber}:</label>
	<input class="input-text" name="subscriber" id="subscriber" size="25" type="text" value="{$smarty.request.subscriber}" />
</div>

<div class="form-field">
	<label for="status">{$lang.status}:</label>
	<select name="status" id="status">
			<option value="">--</option>
			<option {if $smarty.request.status == "A"}selected="selected"{/if} value="A">{$lang.awaiting}</option>
			<option {if $smarty.request.status == "P"}selected="selected"{/if} value="P">{$lang.in_progress}</option>
			<option {if $smarty.request.status == "F"}selected="selected"{/if} value="F">{$lang.finished}</option>
		</select>
</div>

<div class="form-field">
	<label for="type">{$lang.event_type}:</label>
	<select name="type" id="type">
			<option value="">--</option>
			<option {if $smarty.request.type == "P"}selected="selected"{/if} value="P">{$lang.public}</option>
			<option {if $smarty.request.type == "U"}selected="selected"{/if} value="U">{$lang.private}</option>
			<option {if $smarty.request.type == "D"}selected="selected"{/if} value="D">{$lang.disabled}</option>
		</select>
</div>

{foreach from=$event_fields item=field}
{assign var="f_id" value=$field.field_id}
<div class="form-field">
	<label {if $field.field_type != "V"}for="search_fields_{$field.field_id}"{/if}>{$field.description}:</label>
	{if $field.field_type == "S" || $field.field_type == "R"}
			<select name="search_fields[{$field.field_id}]" id="search_fields_{$field.field_id}">
			<option value=""> -- </option>
			{foreach from=$field.variants item=var}
			<option value="{$var.variant_id}" {if $smarty.request.search_fields.$f_id == $var.variant_id}selected="selected"{/if}>{$var.description}</option>
			{/foreach}
			</select>
		{elseif $field.field_type == "C"}
		    <select name="search_fields[{$field.field_id}]" id="search_fields_{$field.field_id}">
			<option value=""> -- </option>
			<option value="Y" {if $smarty.request.search_fields.$f_id == "Y"}selected="selected"{/if}>{$lang.yes}</option>
			<option value="N" {if $smarty.request.search_fields.$f_id == "N"}selected="selected"{/if}>{$lang.no}</option>
			</select>
		{elseif $field.field_type == "I" || $field.field_type == "T"}
			<input class="input-text" size="50" type="text" name="search_fields[{$field.field_id}]" value="{$smarty.request.search_fields.$f_id}" id="search_fields_{$field.field_id}" />
		{elseif $field.field_type == "V"}
			{html_select_date field_array="search_fields[`$field.field_id`]" start_year="1970" end_year="+5" all_empty="--" time=$smarty.request.search_fields.$f_id}
		{/if}
</div>
{/foreach}

<div class="buttons-container">
{include file="buttons/search.tpl" but_name="dispatch[events.search.search]"}
</div>
</form>

{/capture}
{include file="common_templates/section.tpl" section_title=$lang.search section_content=$smarty.capture.section}

{/if}

<form action="{""|fn_url}" method="post" name="delete_events_form">
{if $access_key}
<input type="hidden" name="access_key" value="{$access_key}" />
{/if}

{include file="common_templates/pagination.tpl" save_current_url=true}
{foreach from=$events item=event}
	{if $auth.user_id && $auth.user_id == $event.user_id}{assign var="can_delete" value="Y"}{/if}
{/foreach}

<table cellpadding="0" cellspacing="0" width="100%" border="0" class="table">
<tr>
	{if $can_delete == "Y"}
	<th width="1%">&nbsp;</th>
	{/if}
	<th>{$lang.title}</th>
	<th>{$lang.start_date}</th>
	<th>{$lang.end_date}</th>
	<th>{$lang.status}</th>
	<th>{$lang.event_type}</th>
</tr>
{foreach from=$events item=event}
<tr {cycle values=",class=\"table-row\""}>
	{if $can_delete == "Y"}
	<td class="center">{if $auth.user_id && $auth.user_id == $event.user_id}<input type="checkbox" name="event_ids[]" value="{$event.event_id}" />{else}&nbsp;{/if}</td>
	{/if}
	<td>
		{if $auth.user_id && $auth.user_id == $event.user_id}
			{assign var=event_mode value="update"}
		{else}
			{assign var=event_mode value="view"}
		{/if}
		<strong><a href="{"events.`$event_mode`?event_id=`$event.event_id`"|fn_url}>{$event.title}</a></strong>
	</td>
	<td>{$event.start_date|date_format:$settings.Appearance.date_format}</td>
	<td>{$event.end_date|date_format:$settings.Appearance.date_format}</td>
	<td>{if $event.status == "A"}{$lang.awaiting}{elseif $event.status == "P"}{$lang.in_progress}{else}{$lang.finished}{/if}</td>
	<td>{if $event.type == "P"}{$lang.public}{elseif $event.type == "U"}{$lang.private}{else}{$lang.disabled}{/if}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="6"><p class="no-items">{$lang.no_items_found}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="6">&nbsp;</td>
</tr>
</table>
{include file="common_templates/pagination.tpl"}

{if $can_delete == "Y"}
<div class="buttons-container">
{include file="buttons/button.tpl" but_text=$lang.delete_selected but_name="dispatch[events.delete_events]" but_role="action"}
</div>
{/if}
</form>

<div class="buttons-container">
{include file="buttons/add_new.tpl" but_href="events.add"}
</div>

{capture name="mainbox_title"}{$lang.search}{/capture}