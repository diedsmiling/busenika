{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{capture name="mainbox"}

{capture name="tabsbox"}

<form action="{""|fn_url}" method="post" class="cm-form-highlight" name="event_form">
{if $event_id}
<input type="hidden" name="event_id" value="{$event_id}" />
{/if}
{if $access_key}
<input type="hidden" name="access_key" value="{$access_key}" />
{/if}

<div id="content_general">
<fieldset>

	<div class="form-field">
		<label for="elm_title" class="cm-required">{$lang.title}:</label>
		<input type="text" id="elm_title" class="input-text-large main-input" size="70" name="event_data[title]" value="{$event_data.title}" />
	</div>
	
	<div class="form-field">
		<label for="elm_owner" class="cm-required">{$lang.your_name}:</label>
		<input type="text" id="elm_owner" class="input-text-large" size="70" name="event_data[owner]" value="{$event_data.owner|default:"`$user_info.firstname` `$user_info.lastname`"}" />
	</div>
	
	<div class="form-field">
		<label for="elm_email" class="cm-required cm-email">{$lang.email}:</label>
		<input type="text" id="elm_email" class="input-text-large" size="70" name="event_data[email]" value="{$event_data.email|default:$user_info.email}" />
	</div>
	
	<div class="form-field">
		<label class="cm-required">{$lang.start_date}:</label>
		{include file="common_templates/calendar.tpl" date_id="from_event_date" date_name="event_data[start_date]" date_val=$event_data.start_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
	</div>
	
	<div class="form-field">
		<label class="cm-required">{$lang.end_date}:</label>
		{include file="common_templates/calendar.tpl" date_id="to_event_date" date_name="event_data[end_date]" date_val=$event_data.end_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
	</div>
	
	<div class="form-field">
		<label for="elm_type" class="cm-required">{$lang.type}:</label>
		<select id="elm_type" class="input-text" name="event_data[type]">
			<option value="P" {if $event_data.type == "P"}selected="selected"{/if}>{$lang.public}</option>
			<option value="U" {if $event_data.type == "U"}selected="selected"{/if}>{$lang.private}</option>
			<option value="D" {if $event_data.type == "D"}selected="selected"{/if}>{$lang.disabled}</option>
		</select>
	</div>
	
	{foreach from=$event_fields item=field}
	{assign var="f_id" value=$field.field_id}
	<div class="form-field">
		<label for="elm_{$field.field_id}" {if $field.required == "Y"}class="cm-required"{/if}>{$field.description}:</label>
		{if $field.field_type == "S"}
			<select id="elm_{$field.field_id}" class="input-text" name="event_data[fields][{$field.field_id}]">
				{if $field.required != "Y"}
					<option value="">--</option>
				{/if}
				{foreach from=$field.variants item=var name="vars"}
					<option value="{$var.variant_id}" {if $var.variant_id == $event_data.fields.$f_id}selected="selected"{/if}>{$var.description}</option>
				{/foreach}
			</select>
		{elseif $field.field_type == "R"}
			{foreach from=$field.variants item=var name="vars"}
				<input {if $var.variant_id == $event_data.fields.$f_id || ($mode == "add" && $smarty.foreach.vars.first)}checked="checked"{/if} type="radio" name="event_data[fields][{$field.field_id}]" value="{$var.variant_id}" />{$var.description}&nbsp;&nbsp;
			{/foreach}
		{elseif $field.field_type == "C"}
			<input type="hidden" name="event_data[fields][{$field.field_id}]" value="N" />
			<input id="elm_{$field.field_id}" type="checkbox" name="event_data[fields][{$field.field_id}]" value="Y" {if $event_data.fields.$f_id == "Y"}checked="checked"{/if} />
		{elseif $field.field_type == "I"}
			<input id="elm_{$field.field_id}" class="input-text" size="50" type="text" name="event_data[fields][{$field.field_id}]" value="{$event_data.fields.$f_id}" />
		{elseif $field.field_type == "T"}
			<textarea id="elm_{$field.field_id}"  class="input-text" cols="70" rows="10" name="event_data[fields][{$field.field_id}]">{$event_data.fields.$f_id}</textarea>
		{elseif $field.field_type == "V"}
			{include file="common_templates/calendar.tpl" date_id="event_date_`$field.field_id`" date_name="event_data[fields][`$field.field_id`]" date_val=$event_data.fields.$f_id start_year="1970" end_year="5"}
		{/if}
	</div>
	{/foreach}
	
	<div class="form-field">
		<label>{$lang.invitees}:</label>
		<div class="float-left">
			<table cellpadding="0" cellspacing="0" border="0" width="1" class="table">
			<tr class="cm-first-sibling">
				<th>{$lang.name}</th>
				<th>{$lang.email}</th>
				<th>&nbsp;</th>
			</tr>
			{if $event_data.subscribers}
			<tbody class="cm-first-sibling">
			{strip}
			{foreach from=$event_data.subscribers item=s name="s_fe"}
			<tr id="box_subscriber_{$smarty.foreach.s_fe.iteration}" class="cm-row-item">
				<td><input class="input-text" type="text" name="event_data[subscribers][{$smarty.foreach.s_fe.iteration}][name]" value="{$s.name}" /></td>
				<td><input class="input-text" type="text" name="event_data[subscribers][{$smarty.foreach.s_fe.iteration}][email]" value="{$s.email}" /></td>
				<td class="center">&nbsp;<a class="cm-delete-row"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="" align="bottom" /></a>&nbsp;</td>
			</tr>
			{assign var="iteration" value=$smarty.foreach.s_fe.iteration}
			{/foreach}
			</tbody>
			{/strip}
			{/if}
			<tr id="box_new_subscriber" class="cm-row-item">
				<td><input class="input-text" type="text" name="event_data[add_subscribers][0][name]" value="" /></td>
				<td><input class="input-text" type="text" name="event_data[add_subscribers][0][email]" value="" /></td>
				<td>{include file="buttons/multiple_buttons.tpl" item_id="new_subscriber"}</td>
			</tr>
			</table>
			{if $event_data.subscribers}
				{$lang.text_delete_recipients}
			{/if}
		</div>
	</div>
	
	{hook name="gift_registry:detailed_content"}
	{/hook}

</fieldset>
<!--content_general--></div>

{hook name="gift_registry:tabs_content"}
{/hook}

<div class="buttons-container buttons-bg cm-toggle-button">
	{if $mode == "add"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[events.add]"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[events.update]"}
	{/if}
</div>

</form>

{if $mode == "update"}
	{include file="addons/gift_registry/views/events/components/event_products.tpl"}
	{include file="addons/gift_registry/views/events/components/notifications.tpl"}

	{hook name="gift_registry:tabs_extra"}
	{/hook}
{/if}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{/capture}
{if $mode == "add"}
	{assign var="title" value="`$lang.new_event`"}
{else}
	{include file="common_templates/view_tools.tpl" url="events.update?event_id="}
	{assign var="title" value="`$lang.editing_event`:&nbsp;`$event_data.title`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.view_tools}
