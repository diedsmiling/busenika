{* $Id: profiles_extra_fields.tpl 6221 2008-10-28 15:49:25Z zeke $ *}
{foreach from=$fields item="field"}
{if !$field.field_name}
{assign var="value" value=$order_info.fields[$field.field_id]}
{if $field.description != 'Дом (к.\стр.)'}
{if $field.description != 'Подъезд №'}
{if $field.description != 'Этаж'}
{if $field.description != '№ Кв.\оф.'}
{if $field.description != 'Код дмф.\замка'}
{if $field.description != 'Лифт'}
{if $field.description != 'Точное время'}

{if $field.description != 'Метро'}
{if $field.description != 'Хочу оформить заказ на'}
{if $field.description != 'Точное время'}<tr>
	
	
	<td align="left"><b>{$field.description}</b>:</td>
	{if "AOL"|strpos:$field.field_type !== false} {* Titles/States/Countries *}
		{assign var="title" value="`$field.field_id`_descr"}		
		<td align="left">{$user_data.$title}</td>
	{elseif $field.field_type == "C"}  {* Checkbox *}
		<td align="left">{if $value == "Y"}{$lang.yes}{else}{$lang.no}{/if}</td>
	{elseif $field.field_type == "D"}  {* Date *}
		<td align="left">{$value|date_format:$settings.Appearance.date_format}</td>
	{elseif "RS"|strpos:$field.field_type !== false}  {* Selectbox/Radio *}
		<td align="left">{$field.values.$value}</td>
	{else}  {* input/textarea *}
		<td align="left">{$value|default:"-"}</td>
	{/if}
</tr>
{/if}
{/if}
{/if}
{/if}
{/if}
{/if}
{/if}

{/if}
{/if}
{/if}
{/if}
{/foreach}
