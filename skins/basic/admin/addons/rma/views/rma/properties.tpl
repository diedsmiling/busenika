{* $Id: properties.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="rma_properties_form">
<input type="hidden" name="property_type" value="{$smarty.request.property_type|default:$smarty.const.RMA_REASON}" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	{if $smarty.request.property_type == $smarty.const.RMA_REASON}
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all|escape:html}" class="checkbox cm-check-items" /></th>
	{/if}
	<th>{$lang.position}</th>
	<th width="100%">{if $smarty.request.property_type == "R"}{$lang.reason}{else}{$lang.action}{/if}</th>
	<th>{$lang.status}</th>
	{if $smarty.request.property_type != $smarty.const.RMA_REASON}
	<th>{$lang.update_totals_and_inventory}</th>
	{/if}
	<th>&nbsp;</th>
</tr>
{foreach from=$properties item=property}
<tr {cycle values="class=\"table-row\", "}>
	{if $smarty.request.property_type == $smarty.const.RMA_REASON}
	<td width="1%" class="center">
		<input type="checkbox" name="property_ids[]" value="{$property.property_id}" class="checkbox cm-item" /></td>
	{/if}
	<td>
		<input type="text" name="property_data[{$property.property_id}][position]" size="7" value="{$property.position}" class="input-text-short" /></td>
	<td>
		<input type="text" name="property_data[{$property.property_id}][property]" size="35" value="{$property.property}" class="input-text" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$property.property_id status=$property.status hidden="" object_id_name="property_id" table="rma_properties"}
	</td>
	{if $smarty.request.property_type != $smarty.const.RMA_REASON}
	<td class="center">
		<input type="checkbox" value="{$property.update_totals_and_inventory}" {if $property.update_totals_and_inventory == "Y"}checked="checked"{/if} disabled="disabled" class="checkbox" /></td>
	{/if}
	<td class="nowrap">
		{capture name="tools_items"}
		{if $smarty.request.property_type == $smarty.const.RMA_REASON}
			{assign var="property_type" value=$smarty.request.property_type|default:$smarty.const.RMA_REASON}
			<li><a class="cm-confirm" href="{"rma.delete_property?property_id=`$property.property_id`&amp;property_type=`$property_type`"|fn_url}">{$lang.delete}</a></li>
		{else}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
		{/if}
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$property.property_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="{if $smarty.request.property_type != $smarty.const.RMA_REASON}6{else}4{/if}"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	{if $properties}
	<div class="float-left">
		{include file="buttons/save.tpl" but_name="dispatch[rma.update_properties]" but_role="button_main"}
		
		{if $smarty.request.property_type == $smarty.const.RMA_REASON}
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[rma.delete_properties]" class="cm-process-items cm-confirm" rev="rma_properties_form">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
		{/if}
	</div>
	{/if}
	
	<div class="float-right">
	{if $smarty.request.property_type == $smarty.const.RMA_REASON}
		{capture name="tools"}
			{capture name="add_new_picker"}
			<form action="{""|fn_url}" method="post" name="add_rma_properties_form" class="cm-form-highlight">
			<input type="hidden" name="property_type" value="{$smarty.request.property_type|default:$smarty.const.RMA_REASON}" />
			<div class="object-container">
				<div class="tabs cm-j-tabs">
					<ul>
						<li id="tab_rma_new" class="cm-js cm-active"><a>{$lang.general}</a></li>
					</ul>
				</div>

				<div class="cm-tabs-content" id="content_tab_rma_new">
					<div class="form-field">
						<label class="cm-required" for="add_property_data">{if $smarty.request.property_type == $smarty.const.RMA_REASON}{$lang.reason}{else}{$lang.action}{/if}</label>
						<input type="text" name="add_property_data[0][property]" id="add_property_data" size="35" value="" class="input-text-large main-input" />
					</div>

					<div class="form-field">
						<label for="add_property_position">{$lang.position}</label>
						<input type="text" name="add_property_data[0][position]" id="add_property_position" size="7" value="" class="input-text-short" />
					</div>

					{include file="common_templates/select_status.tpl" input_name="add_property_data[0][status]" id="add_property_data"}
				</div>
			</div>

			<div class="buttons-container">
				{include file="buttons/save_cancel.tpl" but_name="dispatch[rma.add_properties]" create=true cancel_action="close"}
			</div>

			</form>
			{/capture}
			{include file="common_templates/popupbox.tpl" id="add_new_reasons" text=$lang.add_new_reasons content=$smarty.capture.add_new_picker link_text=$lang.add_reason act="general"}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_reasons" text=$lang.add_new_reasons link_text=$lang.add_reason act="general"}
	{/if}
	</div>
</div>

</form>

{/capture}
{if $smarty.request.property_type == $smarty.const.RMA_REASON}
	{include file="common_templates/mainbox.tpl" title=$lang.rma_reasons content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
{else}
	{include file="common_templates/mainbox.tpl" title=$lang.rma_actions content=$smarty.capture.mainbox select_languages=true}
{/if}
