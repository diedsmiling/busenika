{* $Id: pages_form_elements.tpl 9650 2010-05-27 15:21:40Z zeke $ *}
{literal}
<script type="text/javascript">
	//<![CDATA[
	function fn_check_element_type(elm, id, type_id, selectable_elements)
	{
		$('#' + id).toggleBy(selectable_elements.indexOf(elm) == -1);

		// Hide description box for separator
		$('#descr_' + type_id).toggleBy((elm == 'D'));
		$('#hr_' + type_id).toggleBy((elm != 'D'));

		$('#req_' + type_id).attr('disabled', (elm == 'D' || elm == 'H') ? 'disabled' : '');
	}
	//]]>
</script>
{/literal}

<hr width="100%" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.position_short}</th>
	<th width="100%">{$lang.name}</th>
	<th>{$lang.type}</th>
	<th>{$lang.required}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$elements item="element" name="fe_e"}
{assign var="num" value=$smarty.foreach.fe_e.iteration}
<tbody class="cm-row-item">
<tr>
	<td>
		<input type="hidden" name="page_data[form][elements_data][{$num}][element_id]" value="{$element.element_id}" />
		<input class="input-text-short" type="text" size="3" name="page_data[form][elements_data][{$num}][position]" value="{$element.position}" /></td>
	<td>
		<input id="descr_elm_{$element.element_id}" class="input-text-long {if $element.element_type == $smarty.const.FORM_SEPARATOR}hidden{/if}" type="text" name="page_data[form][elements_data][{$num}][description]" value="{$element.description}" />
		<hr id="hr_elm_{$element.element_id}" width="100%" {if $element.element_type != $smarty.const.FORM_SEPARATOR}class="hidden"{/if} /></td>
	<td>
		{include file="addons/form_builder/views/pages/components/element_types.tpl" element_type=$element.element_type elm_id=$element.element_id}</td>
	<td class="center">
		<input type="hidden" name="page_data[form][elements_data][{$num}][required]" value="N" />
		<input id="req_elm_{$element.element_id}" type="checkbox" {if "HD"|strstr:$element.element_type}disabled="disabled"{/if} name="page_data[form][elements_data][{$num}][required]" value="Y" {if $element.required == "Y"}checked="checked"{/if} class="checkbox" /></td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$element.element_id prefix="elm" status=$element.status hidden="" object_id_name="element_id" table="form_options"}</td>
	<td>
		{include file="buttons/multiple_buttons.tpl" only_delete="Y"}</td>
</tr>
<tr id="box_element_variants_{$element.element_id}" {if !$selectable_elements|substr_count:$element.element_type}class="hidden"{/if}>
	<td>&nbsp;</td>
	<td colspan="5">
		<table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr class="cm-first-sibling">
			<th>{$lang.position_short}</th>
			<th>{$lang.name}</th>
			<th>&nbsp;</th>
		</tr>
		{foreach from=$element.variants item=var key="vnum"}
		<tr class="cm-first-sibling cm-row-item">
			<td>
				<input type="hidden" name="page_data[form][elements_data][{$num}][variants][{$vnum}][element_id]" value="{$var.element_id}" />
				<input class="input-text-short" size="3" type="text" name="page_data[form][elements_data][{$num}][variants][{$vnum}][position]" value="{$var.position}" /></td>
			<td><input class="input-text" type="text" name="page_data[form][elements_data][{$num}][variants][{$vnum}][description]" value="{$var.description}" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" only_delete="Y"}</td>
		</tr>
		{/foreach}
		{math equation="x + 1" assign="vnum" x=$vnum|default:0}
		<tr id="box_elm_variants_{$element.element_id}" class="cm-row-item">
			<td><input class="input-text-short" size="3" type="text" name="page_data[form][elements_data][{$num}][variants][{$vnum}][position]" /></td>
			<td><input class="input-text" type="text" name="page_data[form][elements_data][{$num}][variants][{$vnum}][description]" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="elm_variants_`$element.element_id`" tag_level="5"}</td>
		</tr>
		</table>
	</td>
</tr>
</tbody>
{/foreach}

{math equation="x + 1" assign="num" x=$num|default:0}
<tbody class="cm-row-item" id="box_add_elements">
<tr class="no-border">
	<td>
		<input class="input-text-short" size="3" type="text" name="page_data[form][elements_data][{$num}][position]" value="" /></td>
	<td>
		<input id="descr_elm_add_variants" class="input-text-long" type="text" name="page_data[form][elements_data][{$num}][description]" value="" />
		<hr id="hr_elm_add_variants" class="hidden" /></td>
	<td>
		{include file="addons/form_builder/views/pages/components/element_types.tpl" element_type="" elm_id="add_variants"}</td>
	<td class="center">
		<input type="hidden" name="page_data[form][elements_data][{$num}][required]" value="N" />
		<input id="req_elm_add_variants" type="checkbox" name="page_data[form][elements_data][{$num}][required]" value="Y" checked="checked" class="checkbox" /></td>
	<td class="center">
		{include file="common_templates/select_status.tpl" input_name="page_data[form][elements_data][`$num`][status]" display="select"}</td>
	<td>
		{include file="buttons/multiple_buttons.tpl" item_id="add_elements"}</td>
</tr>
<tr id="box_element_variants_add_variants">
	<td>&nbsp;</td>
	<td colspan="5">
		<table cellpadding="0" cellspacing="0" border="0" width="1" class="table">
		<tr class="cm-first-sibling">
			<th>{$lang.position_short}</th>
			<th>{$lang.description}</th>
			<th>&nbsp;</th>
		</tr>
		<tr id="box_elm_variants_add_variants" class="cm-row-item">
			<td><input class="input-text-short" size="3" type="text" name="page_data[form][elements_data][{$num}][variants][0][position]" /></td>
			<td><input class="input-text" type="text" name="page_data[form][elements_data][{$num}][variants][0][description]" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="elm_variants_add_variants" tag_level="5"}</td>
		</tr>
		</table>
	</td>
</tr>
</tbody>


</table>
