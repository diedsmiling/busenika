{* $Id: field_editor.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/tabs.js"}
{script src="js/picker.js"}

<script type="text/javascript">
	//<![CDATA[
	{literal}
	function fn_check_field_type(select, id, tab_id)
	{
		var suffix = select.id.str_replace(id, '');
		var value = $(select).val();
	
		$('#' + tab_id + suffix).toggleBy(!(value == 'R' || value == 'S'));
	}
	{/literal}
	//]]>
</script>

{capture name="mainbox"}

<form action="{""|fn_url}" method="POST" name="event_fields_form">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.position_short}</th>
	<th width="100%">{$lang.description}</th>
	<th>{$lang.type}</th>
	<th>{$lang.required}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$event_fields item=field}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="field_ids[]" value="{$field.field_id}" class="checkbox cm-item" /></td>
	<td><input class="input-text-short" type="text" size="3" name="fields_data[{$field.field_id}][position]" value="{$field.position}" /></td>
	<td>
		<input id="descr_elm_{$field.field_id}" class="input-text-long {if $field.field_type == "D"}hidden{/if}" type="text" name="fields_data[{$field.field_id}][description]" value="{$field.description}" />
		<hr id="hr_elm_{$field.field_id}" {if $field.field_type!="D"}class="hidden"{/if} /></td>
	<td>
		<select id="elm_{$field.field_id}" name="fields_data[{$field.field_id}][field_type]" onchange="fn_check_field_type(this, 'elm_{$field.field_id}', 'box_field_variants_{$field.field_id}');">
			<option value="C" {if $field.field_type == "C"}selected="selected"{/if}>{$lang.checkbox}</option>
			<option value="V" {if $field.field_type == "V"}selected="selected"{/if}>{$lang.date}</option>
			<option value="I" {if $field.field_type == "I"}selected="selected"{/if}>{$lang.input_field}</option>
			<option value="R" {if $field.field_type == "R"}selected="selected"{/if}>{$lang.radiogroup}</option>
			<option value="S" {if $field.field_type == "S"}selected="selected"{/if}>{$lang.selectbox}</option>
			<option value="T" {if $field.field_type == "T"}selected="selected"{/if}>{$lang.textarea}</option>
		</select></td>
	<td class="center">
		<input type="hidden" name="fields_data[{$field.field_id}][required]" value="N" />
		<input type="checkbox" name="fields_data[{$field.field_id}][required]" value="Y"  {if $field.required == "Y"}checked="checked"{/if} class="checkbox" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$field.field_id status=$field.status hidden="" object_id_name="field_id" table="giftreg_fields"}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"events.delete_field?field_id=`$field.field_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$field.field_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
<tr id="box_field_variants_{$field.field_id}" {if "ITCV"|substr_count:$field.field_type}class="hidden"{/if}>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="4">
		<table cellpadding="0" cellspacing="0" border="0" width="1" class="table">
		<tr class="cm-first-sibling">
			<th><input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-{$field.field_id}" /></th>
			<th>{$lang.position_short}</th>
			<th>{$lang.description}</th>
			<th>&nbsp;</th>
		</tr>
		{foreach from=$field.variants item=var}
		<tr class="cm-first-sibling">
			<td class="center">
				<input type="checkbox" name="var_ids[]" value="{$var.variant_id}" class="checkbox cm-item-{$field.field_id} cm-item" /></td>
			<td><input class="input-text-short" size="3" type="text" name="fields_data[{$field.field_id}][variants][{$var.variant_id}][position]" value="{$var.position}" /></td>
			<td><input class="input-text" type="text" name="fields_data[{$field.field_id}][variants][{$var.variant_id}][description]" value="{$var.description}" /></td>
			<td><a class="cm-confirm" href="{"events.delete_variant?var_id=`$var.variant_id`"|fn_url}">{include file="buttons/remove_item.tpl" simple=true}</a></td>
		</tr>
		{/foreach}
		<tr id="box_elm_variants_{$field.field_id}">
			<td>&nbsp;</td>
			<td><input class="input-text-short" size="3" type="text" name="fields_data[{$field.field_id}][add_variants][0][position]" /></td>
			<td><input class="input-text" type="text" name="fields_data[{$field.field_id}][add_variants][0][description]" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="elm_variants_`$field.field_id`" tag_level="3"}</td>
		</tr>
		</table>
	</td>      
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[events.delete]" class="cm-process-items cm-confirm" rev="event_fields_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[events.update_fields]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
</div>

<div class="float-right">
{capture name="tools"}
	{capture name="add_new_picker"}
	<form action="{""|fn_url}" method="post" name="add_event_fields_form" class="cm-form-highlight">
	<div class="object-container">
		<div class="tabs cm-j-tabs">
			<ul>
				<li id="tab_general" class="cm-js cm-active"><a>{$lang.general}</a></li>
				<li id="tab_variants" class="cm-js hidden"><a>{$lang.variants}</a></li>
			</ul>
		</div>
		
		<div class="cm-tabs-content">
			<div id="content_tab_general">
				<div class="form-field">
					<label for="descr_add_field_variants" class="cm-required">{$lang.description}</label>
					<input id="descr_add_field_variants" class="input-text-large" type="text" name="add_fields_data[0][description]" value="" />
					<hr id="hr_add_field_variants" width="100%" class="hidden" />
				</div>
				
				<div class="form-field">
					<label for="position">{$lang.position_short}</label>
					<input id="position" class="input-text-short" size="3" type="text" name="add_fields_data[0][position]" value="" />
				</div>
				
				<div class="form-field">
					<label for="cm-required">{$lang.required}</label>
					<input type="hidden" name="add_fields_data[0][required]" value="N" />
					<input id="cm-required" type="checkbox" name="add_fields_data[0][required]" value="Y" checked="checked" class="checkbox" />
				</div>
				
				<div class="form-field">
					<label for="">{$lang.status}</label>
					<input type="hidden" name="add_fields_data[0][status]" value="D" />
					<input type="checkbox" name="add_fields_data[0][status]" value="A" checked="checked" class="checkbox" />
				</div>
				
				<div class="form-field" class="hidden">
					<label for="add_field_variants">{$lang.type}</label>
					<select id="add_field_variants" name="add_fields_data[0][field_type]" onchange="fn_check_field_type(this, 'add_field_variants', 'tab_variants');">
						<option value="C">{$lang.checkbox}</option>
						<option value="V">{$lang.date}</option>
						<option value="I">{$lang.input_field}</option>
						<option value="R">{$lang.radiogroup}</option>
						<option value="S">{$lang.selectbox}</option>
						<option value="T">{$lang.textarea}</option>
					</select>
				</div>
			</div>
			
			<div id="content_tab_variants" class="hidden">
				<div id="box_add_elm_variants" class="cm-row-item">
					<div class="form-field">
						<div class="float-right">
							{include file="buttons/multiple_buttons.tpl" item_id="add_elm_variants" tag_level="3"}
						</div>
						<label for="" class="cm-required">{$lang.description}</label>
						<input class="input-text" type="text" name="add_fields_data[0][variants][0][description]" />
					</div>
					
					<div class="form-field">
						<label>{$lang.position_short}</label>
						<input class="input-text-short" size="3" type="text" name="add_fields_data[0][variants][0][position]" />
					</div>
					<hr />
				</div>
			</div>
		</div>		
	</div>
	
	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" but_name="dispatch[events.add_fields]" create=true cancel_action="close"}
	</div>
	
	</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_field" text=$lang.add_field content=$smarty.capture.add_new_picker link_text=$lang.add_field act="general"}
{/capture}
{include file="common_templates/popupbox.tpl" id="add_new_field" text=$lang.add_field link_text=$lang.add_field act="general"}
</div>
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.custom_event_fields content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
