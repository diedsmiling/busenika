{* $Id: element_types.tpl 9675 2010-05-31 13:50:43Z zeke $ *}

<select id="elm_{$elm_id}" name="page_data[form][elements_data][{$num}][element_type]" onchange="fn_check_element_type(this.value, 'box_element_variants_{$elm_id}', this.id, '{$selectable_elements}');">
	<optgroup label="{$lang.base}">
	<option value="{$smarty.const.FORM_SELECT}" {if $element_type == $smarty.const.FORM_SELECT}selected="selected"{/if}>{$lang.selectbox}</option>
	<option value="{$smarty.const.FORM_RADIO}" {if $element_type == $smarty.const.FORM_RADIO}selected="selected"{/if}>{$lang.radiogroup}</option>
	<option value="{$smarty.const.FORM_MULTIPLE_CB}" {if $element_type == $smarty.const.FORM_MULTIPLE_CB}selected="selected"{/if}>{$lang.multiple_checkboxes}</option>
	<option value="{$smarty.const.FORM_MULTIPLE_SB}" {if $element_type == $smarty.const.FORM_MULTIPLE_SB}selected="selected"{/if}>{$lang.multiple_selectbox}</option>
	<option value="{$smarty.const.FORM_CHECKBOX}" {if $element_type == $smarty.const.FORM_CHECKBOX}selected="selected"{/if}>{$lang.checkbox}</option>
	<option value="{$smarty.const.FORM_INPUT}" {if $element_type == $smarty.const.FORM_INPUT}selected="selected"{/if}>{$lang.input_field}</option>
	<option value="{$smarty.const.FORM_TEXTAREA}" {if $element_type == $smarty.const.FORM_TEXTAREA}selected="selected"{/if}>{$lang.textarea}</option>
	<option value="{$smarty.const.FORM_HEADER}" {if $element_type == $smarty.const.FORM_HEADER}selected="selected"{/if}>{$lang.header}</option>
	<option value="{$smarty.const.FORM_SEPARATOR}" {if $element_type == $smarty.const.FORM_SEPARATOR}selected="selected"{/if}>{$lang.separator}</option>
	</optgroup>
	<optgroup label="{$lang.special}">
	{hook name="pages:form_elements"}
	<option value="{$smarty.const.FORM_DATE}" {if $element_type == $smarty.const.FORM_DATE}selected="selected"{/if}>{$lang.date}</option>
	<option value="{$smarty.const.FORM_EMAIL}" {if $element_type == $smarty.const.FORM_EMAIL}selected="selected"{/if}>{$lang.email}</option>
	<option value="{$smarty.const.FORM_NUMBER}" {if $element_type == $smarty.const.FORM_NUMBER}selected="selected"{/if}>{$lang.number}</option>
	<option value="{$smarty.const.FORM_PHONE}" {if $element_type == $smarty.const.FORM_PHONE}selected="selected"{/if}>{$lang.phone}</option>
	<option value="{$smarty.const.FORM_COUNTRIES}" {if $element_type == $smarty.const.FORM_COUNTRIES}selected="selected"{/if}>{$lang.countries_list}</option>
	<option value="{$smarty.const.FORM_STATES}" {if $element_type == $smarty.const.FORM_STATES}selected="selected"{/if}>{$lang.states_list}</option>
	<option value="{$smarty.const.FORM_FILE}" {if $element_type == $smarty.const.FORM_FILE}selected="selected"{/if}>{$lang.file}</option>
	<option value="{$smarty.const.FORM_REFERER}" {if $element_type == $smarty.const.FORM_REFERER}selected="selected"{/if}>{$lang.referer}</option>
	<option value="{$smarty.const.FORM_IP_ADDRESS}" {if $element_type == $smarty.const.FORM_IP_ADDRESS}selected="selected"{/if}>{$lang.ip_address}</option>
	{/hook}
	</optgroup>
</select>