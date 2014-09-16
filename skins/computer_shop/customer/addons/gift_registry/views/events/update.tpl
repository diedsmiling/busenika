{* $Id: update.tpl 9827 2010-06-22 06:35:52Z angel $ *}

{$lang.text_mandatory_fields}

{if $mode == "update"}
	{capture name="tabsbox"}
	<div id="content_general">
{/if}

<form action="{""|fn_url}" method="post" name="event_form">
<input type="hidden" name="selected_section" value="" />
{if $event_id}
<input type="hidden" name="event_id" value="{$event_id}" />
{/if}
{if $access_key}
<input type="hidden" name="access_key" value="{$access_key}" />
<p>{$lang.text_remember_access_key}:&nbsp;&nbsp;<strong>{$access_key}</strong></p>
{/if}

{if $auth.user_id}
	{assign var="default_name" value="`$user_info.firstname` `$user_info.lastname`"}
	{assign var="default_email" value=$user_info.email}
{/if}

<div class="form-field">
	<label for="elm_title" class="cm-required">{$lang.title}:</label>
	<input type="text" id="elm_title" class="input-text" size="70" name="event_data[title]" value="{$event_data.title}" />
</div>

<div class="form-field">
	<label for="elm_owner" class="cm-required">{$lang.your_name}:</label>
	<input type="text" id="elm_owner" class="input-text" size="70" name="event_data[owner]" value="{$event_data.owner|default:$default_name}" />
</div>

<div class="form-field">
	<label for="elm_email" class="cm-required">{$lang.email}:</label>
	<input type="text" id="elm_email" class="input-text" size="70" name="event_data[email]" value="{$event_data.email|default:$default_email}" />
</div>

<div class="form-field">
	<label for="elm_start_date" class="cm-required">{$lang.start_date}:</label>
	{include file="common_templates/calendar.tpl" date_id="elm_start_date" date_name="event_data[start_date]" date_val=$event_data.start_date  start_year=$settings.Company.company_start_year}
</div>

<div class="form-field">
	<label for="elm_end_date" class="cm-required">{$lang.end_date}:</label>
	{include file="common_templates/calendar.tpl" date_id="elm_end_date" date_name="event_data[end_date]" date_val=$event_data.end_date  start_year=$settings.Company.company_start_year}
</div>

<div class="form-field">
	<label for="elm_type" class="cm-required">{$lang.event_type}:</label>
	<select id="elm_type" name="event_data[type]">
		<option value="P" {if $event_data.type == "P"}selected="selected"{/if}>{$lang.public}</option>
		<option value="U" {if $event_data.type == "U"}selected="selected"{/if}>{$lang.private}</option>
	</select>
</div>

{hook name="events:fields"}
{foreach from=$event_fields item=field}
{assign var="f_id" value=$field.field_id}
<div class="form-field">
	<label for="elm_{$field.field_id}" {if $field.required == "Y"}class="cm-required"{/if}>{$field.description}:</label>
	{if $field.field_type == "S"}
			<select id="elm_{$field.field_id}" name="event_data[fields][{$field.field_id}]">
			{if $field.required != "Y"}
			<option value="">--</option>
			{/if}
			{foreach from=$field.variants item=var name="vars"}
			<option value="{$var.variant_id}" {if $var.variant_id == $event_data.fields.$f_id}selected="selected"{/if}>{$var.description}</option>
			{/foreach}
			</select>
		{elseif $field.field_type == "R"}
			{foreach from=$field.variants item=var name="vars"}
			<input {if $var.variant_id == $event_data.fields.$f_id || ($mode == "add" && $smarty.foreach.vars.first)}checked="checked"{/if} type="radio" name="event_data[fields][{$field.field_id}]" value="{$var.variant_id}" class="radio" />{$var.description}&nbsp;&nbsp;
			{/foreach}
		{elseif $field.field_type == "C"}
			<input type="hidden" name="event_data[fields][{$field.field_id}]" value="N" />
			<input id="elm_{$field.field_id}" type="checkbox" name="event_data[fields][{$field.field_id}]" value="Y" {if $event_data.fields.$f_id == "Y"}checked="checked"{/if} class="checkbox" />
		{elseif $field.field_type == "I"}
			<input id="elm_{$field.field_id}" class="input-text" size="50" type="text" name="event_data[fields][{$field.field_id}]" value="{$event_data.fields.$f_id}" />
		{elseif $field.field_type == "T"}
			<textarea id="elm_{$field.field_id}"  class="input-textarea" cols="72" rows="10" name="event_data[fields][{$field.field_id}]">{$event_data.fields.$f_id}</textarea>
		{elseif $field.field_type == "V"}
			{include file="common_templates/calendar.tpl" date_id="elm_`$field.field_id`" date_name="event_data[fields][`$field.field_id`]" date_val=$event_data.fields.$f_id start_year="1970"}
		{/if}
</div>
{/foreach}
{/hook}

<div class="form-field">
	<label for="elm_invitees">{$lang.invitees}:</label>
	<div class="float-left" id="elm_invitees">
		<table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr class="cm-first-sibling">
			<th>{$lang.name}</th>
			<th>{$lang.email}</th>
			<th>&nbsp;</th>
		</tr>
		{if $event_data.subscribers}
		{strip}
		{foreach from=$event_data.subscribers item=s name="s_fe"}
		<tr id="box_subscriber_{$smarty.foreach.s_fe.iteration}">
			<td><input class="input-text-auto" type="text" name="event_data[subscribers][{$smarty.foreach.s_fe.iteration}][name]" value="{$s.name}" size="18" /></td>
			<td><input class="input-text-auto" type="text" name="event_data[subscribers][{$smarty.foreach.s_fe.iteration}][email]" value="{$s.email}" size="18" /></td>
			<td class="right">{include file="buttons/multiple_buttons.tpl" item_id="subscriber_`$smarty.foreach.s_fe.iteration`" only_delete="Y"}</td>
		</tr>
		{/foreach}
		{/strip}
		{/if}
		<tr id="box_new_subscriber" class="cm-row-item">
			<td><input class="input-text-auto" type="text" name="event_data[add_subscribers][0][name]" value="" size="18" /></td>
			<td><input class="input-text-auto" type="text" name="event_data[add_subscribers][0][email]" value="" size="18" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="new_subscriber"}</td>
		</tr>
		<tr class="table-footer">
			<td colspan="3">&nbsp;</td>
		</tr>
		</table>
		{if $event_data.subscribers}
			{$lang.text_delete_recipients}
		{/if}
	</div>
</div>

<div class="buttons-container">
	{if $mode == "update"}
	{assign var="title" value=$lang.update_event}
		{include file="buttons/save.tpl" but_name="dispatch[events.$mode]"}
		{include file="buttons/button.tpl" but_text=$lang.delete_this_event but_href="events.delete_event?event_id=$event_id&amp;access_key=$access_key"}
	{else}
		{include file="buttons/add_new.tpl" but_name="dispatch[events.$mode]" but_role="action"}
	{/if}
</div>
</form>

{if $mode == "update"}
		</div>
		{hook name="events:update"}
			{include file="addons/gift_registry/views/events/components/event_products.tpl"}
			{include file="addons/gift_registry/views/events/components/notifications.tpl"}
		{/hook}
	{/capture}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{/if}

{capture name="mainbox_title"}
	{if $mode == "add"}
		{$lang.add_event}
	{else}
		{$lang.editing_event}
	{/if}
{/capture}