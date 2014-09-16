{* $Id: profile_fields_info.tpl 6830 2009-01-26 15:05:14Z angel $ *}

{assign var="first_field" value=true}
<p>
{foreach from=$fields item=field name="fields"}
	{if !$field.field_name}
		{assign var="data_id" value=$field.field_id}
		{assign var="value" value=$user_data.fields.$data_id}
		{if $customer_info}
			{if !$first_field}, {/if}<span class="additional-fields">
		{else}
			<div class="form-field">
		{/if}
		{assign var="first_field" value=false}

			<label>{$field.description}:</label>
			{if "AOL"|strpos:$field.field_type !== false} {* Titles/States/Countries *}
				{assign var="title" value="`$data_id`_descr"}
				{$user_data.$title}
			{elseif $field.field_type == "C"}  {* Checkbox *}
				{if $value == "Y"}{$lang.yes}{else}{$lang.no}{/if}
			{elseif $field.field_type == "D"}  {* Date *}
				{$value|date_format:$settings.Appearance.date_format}
			{elseif "RS"|strpos:$field.field_type !== false}  {* Selectbox/Radio *}
				{$field.values.$value}
			{else}  {* input/textarea *}
				{$value|default:"-"}
			{/if}
		{if $customer_info}
			</span>
		{else}
			</div>
		{/if}
	{/if}
{/foreach}
</p>
