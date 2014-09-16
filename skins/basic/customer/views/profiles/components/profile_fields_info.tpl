{* $Id: profile_fields_info.tpl 6706 2009-01-05 09:40:13Z angel $ *}

{foreach from=$fields item=field}
{if $field.field_name}
	{assign var="data_id" value=$field.field_name}
	{assign var="value" value=$user_data.$data_id}
{else}
	{assign var="data_id" value=$field.field_id}
	{assign var="value" value=$user_data.fields.$data_id}
{/if}

<div class="info-field">
	<label>{$field.description}:</label>
	{if "AOL"|strpos:$field.field_type !== false} {* Titles/States/Countries *}
		{assign var="title" value="`$data_id`_descr"}
		{$user_data.$title|default:"-"}
	{elseif $field.field_type == "C"}  {* Checkbox *}
		{if $value == "Y"}{$lang.yes}{else}{$lang.no}{/if}
	{elseif $field.field_type == "D"}  {* Date *}
		{$value|date_format:$settings.Appearance.date_format}
	{elseif "RS"|strpos:$field.field_type !== false}  {* Selectbox/Radio *}
		{$field.values.$value}
	{else}  {* input/textarea *}
		{$value|default:"-"}
	{/if}
</div>
{/foreach}
