{* $Id: manage.tpl 9938 2010-07-01 14:18:59Z 2tl $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="countries_form">

{include file="common_templates/pagination.tpl"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">{$lang.code}</th>
	<th class="center">{$lang.code}&nbsp;A3</th>
	<th class="center">{$lang.code}&nbsp;N3</th>
	<th>{$lang.country}</th>
	<th class="center">{$lang.region}</th>
	<th width="5%">{$lang.status}</th>
</tr>

{foreach from=$countries item=country}
<tr {cycle values="class=\"table-row\", "}>
{*	<td>
		<input type="checkbox" name="delete[{$country.code}]" id="delete_checkbox" value="Y" class="checkbox cm-item" /></td>
*}
	<td class="center">
		{* <input type="text" name="country_data[{$country.code}][code]" size="2" value="{$country.code}" class="input-text" />*}{$country.code}</td>
	<td class="center">
		{*<input type="text" name="country_data[{$country.code}][code_A3]" size="3" value="{$country.code_A3}" class="input-text" />*}{$country.code_A3}</td>
	<td class="center">
		{*<input type="text" name="country_data[{$country.code}][code_N3]" size="5" value="{$country.code_N3}" class="input-text" />*}{$country.code_N3}</td>
	<td>
		<input type="text" name="country_description[{$country.code}][country]" size="55" value="{$country.country}" class="input-text" /></td>
	<td class="center">
		{*<input type="text" name="country_data[{$country.code}][region]" size="3" value="{$country.region}" class="input-text" />*}{$country.region}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$country.code status=$country.status hidden="" object_id_name="code" table="countries"}
	</td>
</tr>
{/foreach}
</table>
{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[countries.update]" but_role="button_main"}
	
	{* Deletion of existent countries functionality is disabled by default *}
	
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[countries.delete]" class="cm-process-items cm-confirm" rev="countries_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{*include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action*}
	
</div>

</form>

 {* Add new country functionality is disabled by default *}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.countries content=$smarty.capture.mainbox select_languages=true}
