{* $Id: manage.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}

{literal}
<script type="text/javascript">
//<![CDATA[
function fn_check_field_type(value, tab_id)
{
	$('#' + tab_id).toggleBy(!(value == 'R' || value == 'S'));
}
//]]>
</script>
{/literal}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="fields_form">
{math equation = "x + 5" assign="_colspan" x=$profile_fields_areas|sizeof}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.position_short}</th>
	<th width="100%">{$lang.description}</th>
	<th>{$lang.type}</th>
	{foreach from=$profile_fields_areas key="key" item="d"}
	<th class="center">
		<ul>
			<li>{$lang.$d}</li>
			<li>{$lang.show}&nbsp;/&nbsp;{$lang.required}</li>
		</ul>
	</th>
	{/foreach}
	<th>&nbsp;</th>
</tr>

{foreach from=$profile_fields key=section item=fields name="profile_fields"}
	<tr>
		<td colspan="{$_colspan}">
			{if $section == "C"}{assign var="s_title" value=$lang.contact_information}
			{elseif $section == "B"}{assign var="s_title" value=$lang.billing_address}
			{elseif $section == "S"}{assign var="s_title" value=$lang.shipping_address}
			{/if}
			{include file="common_templates/subheader.tpl" title=$s_title}
		</td>
	</tr>
	{foreach from=$fields item=field}
	<tr {cycle values="class=\"table-row\", "}>
		<td class="center">
			{if $section != "B" && $field.is_default != "Y"}{assign var="extra_fields" value=true}{assign var="custom_fields" value=true}{if $field.matching_id}<input type="hidden" name="matches[{$field.matching_id}]" value="{$field.field_id}" />{/if}<input type="checkbox" name="field_ids[]" value="{$field.field_id}" class="checkbox cm-item" />{else}&nbsp;{/if}</td>
		<td><input class="input-text-short" type="text" size="3" name="fields_data[{$field.field_id}][position]" value="{$field.position}" /></td>
		<td>
			<input id="descr_elm_{$field.field_id}" class="input-text" size="20" type="text" name="fields_data[{$field.field_id}][description]" value="{$field.description}" /></td>
		<td class="nowrap">
			{if $field.is_default == "Y" || $section == "B"}
				{if $field.field_type == "C"}{$lang.checkbox}
				{elseif $field.field_type == "I"}{$lang.input_field}
				{elseif $field.field_type == "R"}{$lang.radiogroup}
				{elseif $field.field_type == "S"}{$lang.selectbox}
				{elseif $field.field_type == "T"}{$lang.textarea}
				{elseif $field.field_type == "D"}{$lang.date}
				{elseif $field.field_type == "E"}{$lang.email}
				{elseif $field.field_type == "Z"}{$lang.zip_postal_code}
				{elseif $field.field_type == "P"}{$lang.phone}
				{elseif $field.field_type == "L"}<a href="{"static_data.manage?section=T"|fn_url}" class="underlined">{$lang.titles}&nbsp;&#155;&#155;</a>
				{elseif $field.field_type == "O"}<a href="{"countries.manage"|fn_url}" class="underlined">{$lang.country}&nbsp;&#155;&#155;</a>
				{elseif $field.field_type == "A"}<a href="{"states.manage"|fn_url}" class="underlined">{$lang.state}&nbsp;&#155;&#155;</a>
				{/if}
				<input type="hidden" name="fields_data[{$field.field_id}][field_type]" value="{$field.field_type}" />
			{else}
			<select id="elm_{$field.field_id}" name="fields_data[{$field.field_id}][field_type]" onchange="fn_check_field_type(this.value, 'field_values_{$field.field_id}');">
				<option value="P" {if $field.field_type == "P"}selected="selected"{/if}>{$lang.phone}</option>
				<option value="Z" {if $field.field_type == "Z"}selected="selected"{/if}>{$lang.zip_postal_code}</option>
				<option value="C" {if $field.field_type == "C"}selected="selected"{/if}>{$lang.checkbox}</option>
				<option value="D" {if $field.field_type == "D"}selected="selected"{/if}>{$lang.date}</option>
				<option value="I" {if $field.field_type == "I"}selected="selected"{/if}>{$lang.input_field}</option>
				<option value="R" {if $field.field_type == "R"}selected="selected"{/if}>{$lang.radiogroup}</option>
				<option value="S" {if $field.field_type == "S"}selected="selected"{/if}>{$lang.selectbox}</option>
				<option value="T" {if $field.field_type == "T"}selected="selected"{/if}>{$lang.textarea}</option>
			</select>
			{/if}
		</td>

		{foreach from=$profile_fields_areas key="key" item="d"}
		{assign var="_show" value="`$key`_show"}
		{assign var="_required" value="`$key`_required"}
		<td class="center">
			<input type="hidden" name="fields_data[{$field.field_id}][{$_show}]" value="{if $field.field_name == "email"}Y{else}N{/if}" />
			<input type="checkbox" name="fields_data[{$field.field_id}][{$_show}]" value="Y" {if $field.$_show == "Y"}checked="checked"{/if} {if $field.field_name == "email"}disabled="disabled"{/if} onclick="document.getElementById('req_{$key}_{$field.field_id}').disabled = !this.checked;" />
			<input type="hidden" name="fields_data[{$field.field_id}][{$_required}]" value="{if $field.field_name == "email"}Y{else}N{/if}" />
			<input id="req_{$key}_{$field.field_id}" type="checkbox" name="fields_data[{$field.field_id}][{$_required}]" value="Y" {if $field.$_required == "Y"}checked="checked"{/if} {if $field.$_show == "N" || $field.field_name == "email"}disabled="disabled"{/if} />
		</td>
		{/foreach}
		<td class="nowrap">
			{capture name="tools_items"}
			{if $custom_fields}
				<li><a class="cm-confirm" href="{"profile_fields.delete?field_id=`$field.field_id`"|fn_url}">{$lang.delete}</a></li>
			{else}
				<li><span class="undeleted-element">{$lang.delete}</span></li>
			{/if}
			{/capture}
			{include file="common_templates/table_tools_list.tpl" prefix=$field.field_id tools_list=$smarty.capture.tools_items}
		</td>
	</tr>
	{if $field.is_default == "N" && $section != "B"}
	<tr id="field_values_{$field.field_id}" {if "CHITDNPZ"|substr_count:$field.field_type}class="hidden"{/if}>
		<td colspan="{$_colspan}">
			<table cellpadding="0" cellspacing="0" border="0" width="1" class="table">
			<tr class="cm-first-sibling">
				<th>&nbsp;</th>
				<th>{$lang.position_short}</th>
				<th>{$lang.description}</th>
				<th>&nbsp;</th>
			</tr>
			{foreach from=$field.values item=val}
			<tr class="cm-first-sibling">
				<td class="center">
					<input type="checkbox" name="value_ids[]" value="{$val.value_id}" class="checkbox cm-item" /></td>
				<td><input class="input-text-short" size="3" type="text" name="fields_data[{$field.field_id}][values][{$val.value_id}][position]" value="{$val.position}" /></td>
				<td><input class="input-text" type="text" name="fields_data[{$field.field_id}][values][{$val.value_id}][description]" value="{$val.description}" /></td>
				<td><a class="cm-confirm cm-ajax cm-delete-row" href="{"profile_fields.delete?value_id=`$val.value_id`"|fn_url}">{include file="buttons/remove_item.tpl" simple=true}</a></td>
			</tr>
			{/foreach}
			<tr id="box_elm_values_{$field.field_id}">
				<td>&nbsp;</td>
				<td><input class="input-text-short" size="3" type="text" name="fields_data[{$field.field_id}][add_values][0][position]" /></td>
				<td><input class="input-text" type="text" name="fields_data[{$field.field_id}][add_values][0][description]" /></td>
				<td>{include file="buttons/multiple_buttons.tpl" item_id="elm_values_`$field.field_id`" tag_level=3}</td>
			</tr>
			</table>
		</td>
	</tr>
	{/if}
	{assign var="custom_fields" value=false}
	{/foreach}
{foreachelse}
<tr class="no-items">
	<td colspan="{$_colspan}"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $profile_fields}
		<div class="float-left">
			{include file="buttons/save.tpl" but_name="dispatch[profile_fields.update]" but_role="button_main"}
			{if $extra_fields}
			{capture name="tools_list"}
			<ul>
				<li><a class="cm-process-items cm-confirm" name="dispatch[profile_fields.delete]" rev="fields_form">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
			{/if}
		</div>
	{/if}
	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_field" text=$lang.add_new_field link_text=$lang.add_field act="general"}
	</div>
</div>

</form>

{capture name="tools"}
	{capture name="add_new_picker"}
	<form action="{""|fn_url}" method="post" name="add_fields_form" class="cm-form-highlight">
	<div class="object-container">
		<div class="tabs cm-j-tabs">
			<ul>
				<li id="tab_new_profile" class="cm-js cm-active"><a>{$lang.general}</a></li>
				<li id="tab_variants" class="cm-js hidden"><a>{$lang.variants}</a></li>
			</ul>
		</div>
		<div class="cm-tabs-content">
			<div id="content_tab_new_profile">
				<div class="form-field">
					<label class="cm-required">{$lang.description}:</label>
					<input id="descr_add_field_values_section" class="input-text-large main-input" type="text" name="add_fields_data[0][description]" value="" />
				</div>
	
				<div class="form-field">
					<label>{$lang.position}:</label>
					<input class="input-text-short" type="text" size="3" name="add_fields_data[0][position]" value="" />
				</div>
	
				<div class="form-field">
					<label>{$lang.type}:</label>
					<select id="add_field_values_section" name="add_fields_data[0][field_type]" onchange="fn_check_field_type(this.value, 'tab_variants');">
						<option value="P">{$lang.phone}</option>
						<option value="Z">{$lang.zip_postal_code}</option>
						<option value="C">{$lang.checkbox}</option>
						<option value="D">{$lang.date}</option>
						<option value="I">{$lang.input_field}</option>
						<option value="R">{$lang.radiogroup}</option>
						<option value="S">{$lang.selectbox}</option>
						<option value="T">{$lang.textarea}</option>
					</select>
				</div>
	
				<div class="form-field">
					<label>{$lang.section}:</label>
					<select name="add_fields_data[0][section]">
						<option value="C">{$lang.contact_information}</option>
						<option value="BS">{$lang.billing_address}/{$lang.shipping_address}</option>
					</select>
				</div>
	
				{foreach from=$profile_fields_areas key="key" item="d"}
				{assign var="_show" value="`$key`_show"}
				{assign var="_required" value="`$key`_required"}
				<div class="form-field">
					<label>{$lang.$d}&nbsp;({$lang.show}&nbsp;/&nbsp;{$lang.required}):</label>
					<input type="hidden" name="add_fields_data[0][{$_show}]" value="N" />
					<input type="checkbox" name="add_fields_data[0][{$_show}]" value="Y" checked="checked" />&nbsp;
					<input type="hidden" name="add_fields_data[0][{$_required}]" value="N" />
					<input type="checkbox" name="add_fields_data[0][{$_required}]" value="Y" checked="checked" />
				</div>
				{/foreach}
			<!--content_tab_new_profile--></div>

			<div class="hidden" id="content_tab_variants">
				<table cellpadding="0" cellspacing="0" border="0" class="table">
				<tr>
					<th>{$lang.position}</th>
					<th>{$lang.description}</th>
					<th>&nbsp;</th>
				</tr>
				<tr id="box_add_field_values">
					<td><input class="input-text-short" size="3" type="text" name="add_fields_data[0][values][0][position]" /></td>
					<td><input class="input-text" type="text" name="add_fields_data[0][values][0][description]" /></td>
					<td>{include file="buttons/multiple_buttons.tpl" item_id="add_field_values" tag_level="3"}</td>
				</tr>
				</table>
			<!--content_tab_variants--></div>
		</div>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[profile_fields.add]" cancel_action="close"}
	</div>

	</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_field" text=$lang.add_new_field content=$smarty.capture.add_new_picker link_text=$lang.add_field act="general"}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.profile_fields content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
