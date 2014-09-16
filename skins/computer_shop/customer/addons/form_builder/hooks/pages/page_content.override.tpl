{* $Id: page_content.override.tpl 9650 2010-05-27 15:21:40Z zeke $ *}

{if $page.page_type == $smarty.const.PAGE_TYPE_FORM}
	{if $smarty.request.sent == "Y"}

		{hook name="pages:form_sent"}		
		{assign var="form_submit_const" value=$smarty.const.FORM_SUBMIT}
		<p>{$page.form.general.$form_submit_const|unescape}</p>
		{/hook}

		<p class="center">
		{include file="buttons/button.tpl" but_text=$lang.continue but_href=$continue_url|default:$index_script but_role="action"}
		</p>
	{else}

	{$lang.text_mandatory_fields}
	<p>&nbsp;</p>
	{$page.description|unescape}
	<p>&nbsp;</p>

	{capture name="group"}

	<form action="{""|fn_url}" method="post" name="forms_form" enctype="multipart/form-data">
	<input type="hidden" name="fake" value="1" />
	<input type="hidden" name="page_id" value="{$page.page_id}" />

	{foreach from=$page.form.elements key="element_id" item="element"}


	{if $element.element_type == $smarty.const.FORM_SEPARATOR}
	<hr width="100%" />


	{elseif $element.element_type == $smarty.const.FORM_HEADER}
	{include file="common_templates/subheader.tpl" title=$element.description}


	{elseif $element.element_type != $smarty.const.FORM_IP_ADDRESS && $element.element_type != $smarty.const.FORM_REFERER}
		<div class="form-field">
			<label for="{if $element.element_type == $smarty.const.FORM_FILE}type_{"fb_files[`$element.element_id`]"|md5}{else}elm_{$element.element_id}{/if}" class="{if $element.required == "Y"}cm-required{/if}{if $element.element_type == $smarty.const.FORM_EMAIL} cm-email{/if}{if $element.element_type == $smarty.const.FORM_PHONE} cm-phone{/if}{if $element.element_type == $smarty.const.FORM_EMAIL_CONFIRM} cm-confirm-email{/if} {if $element.element_type == $smarty.const.FORM_COUNTRIES} cm-location-billing cm-country {/if} {if $element.element_type == $smarty.const.FORM_STATES} cm-location-billing cm-state {/if} {if $element.element_type == $smarty.const.FORM_MULTIPLE_CB}cm-multiple-checkboxes{/if}">{$element.description}:</label>

			{if $element.element_type == $smarty.const.FORM_SELECT}
				<select id="elm_{$element.element_id}" name="form_values[{$element.element_id}]">
				{foreach from=$element.variants item=var}
				<option value="{$var.element_id}" {if $form_values.$element_id == $var.element_id}selected="selected"{/if}>{$var.description}</option>
				{/foreach}
				</select>
				
				
			{elseif $element.element_type == $smarty.const.FORM_RADIO}
				{foreach from=$element.variants item=var name="rd"}
				<input class="radio" {if (!$form_values && $smarty.foreach.rd.iteration == 1) || ($form_values.$element_id == $var.element_id)}checked="checked"{/if} type="radio" name="form_values[{$element.element_id}]" value="{$var.element_id}" />&nbsp;{$var.description}&nbsp;&nbsp;
				{/foreach}
				
				
			{elseif $element.element_type == $smarty.const.FORM_CHECKBOX}
				<input type="hidden" name="form_values[{$element.element_id}]" value="N" />
				<input id="elm_{$element.element_id}" class="checkbox" {if $form_values.$element_id == "Y"}checked="checked"{/if} type="checkbox" name="form_values[{$element.element_id}]" value="Y" />
				
				
			{elseif $element.element_type == $smarty.const.FORM_MULTIPLE_SB}
				<select id="elm_{$element.element_id}" name="form_values[{$element.element_id}][]" multiple="multiple" >
				{foreach from=$element.variants item=var}
				<option value="{$var.element_id}" {if $form_values.$element_id == $var.element_id}selected="selected"{/if}>{$var.description}</option>
				{/foreach}
				</select>
				
				
			{elseif $element.element_type == $smarty.const.FORM_MULTIPLE_CB}
				{split data=$element.variants size="4" assign="splitted_variants"}
				<table cellpadding="0" cellspacing="0" border="0">
				{foreach from=$splitted_variants item=variants}
				<tr>
					{foreach from=$variants item=var}
					<td>
						{if $var}<input class="form-checkbox" type="checkbox" {if $form_values.$element_id == $var.element_id}checked="checked"{/if} id="elm_{$element.element_id}_{$var.element_id}" name="form_values[{$element.element_id}][]" value="{$var.element_id}" />&nbsp;{$var.description}&nbsp;&nbsp;&nbsp;{else}&nbsp;{/if}
					</td>
					{/foreach}
				</tr>
				{/foreach}
				</table>
				
				
			{elseif $element.element_type == $smarty.const.FORM_INPUT}
				<input id="elm_{$element.element_id}" class="input-text" size="50" type="text" name="form_values[{$element.element_id}]" value="{$form_values.$element_id}" />
				
				
			{elseif $element.element_type == $smarty.const.FORM_TEXTAREA}
				<textarea id="elm_{$element.element_id}" class="input-textarea" name="form_values[{$element.element_id}]" cols="67" rows="10">{$form_values.$element_id}</textarea>
				
				
			{elseif $element.element_type == $smarty.const.FORM_DATE}
				{include file="common_templates/calendar.tpl" date_name="form_values[`$element.element_id`]" date_id="elm_`$element.element_id`" date_val=$form_values.$element_id}
			
				
			{elseif $element.element_type == $smarty.const.FORM_EMAIL || $element.element_type == $smarty.const.FORM_NUMBER || $element.element_type == $smarty.const.FORM_PHONE || $element.element_type == $smarty.const.FORM_EMAIL_CONFIRM}
				
				
				{if $element.element_type == $smarty.const.FORM_EMAIL || $element.element_type == $smarty.const.FORM_EMAIL_CONFIRM}
				<input type="hidden" name="customer_email" value="{$element.element_id}" />
				{/if}
				<input id="elm_{$element.element_id}" class="input-text{if $element.element_type == $smarty.const.FORM_EMAIL_CONFIRM}-medium{/if}" size="50" type="text" name="form_values[{$element.element_id}]" value="{$form_values.$element_id}" />
				
				{if $element.element_type == $smarty.const.FORM_EMAIL_CONFIRM}
				{$lang.confirm}: <input id="confirm_elm_{$element.element_id}" class="input-text-medium" type="text" name="" value="" />
				{/if}
				
				
			{elseif $element.element_type == $smarty.const.FORM_COUNTRIES}
				<select id="elm_{$element.element_id}" name="form_values[{$element.element_id}]" class="cm-location-billing">
					<option value="">- {$lang.select_country} -</option>
					{assign var="countries" value=1|fn_get_simple_countries}
					{foreach from=$countries item=country key=ccode}
					<option value="{$ccode}" {if ($form_values.$elm_id && $form_values.$elm_id == $ccode) || (!$form_values && $ccode == $settings.General.default_country)}selected="selected"{/if}>{$country}</option>
					{/foreach}
				</select>
				
				
			{elseif $element.element_type == $smarty.const.FORM_STATES}

				{include file="views/profiles/components/profiles_scripts.tpl" states=$smarty.const.CART_LANGUAGE|fn_get_all_states:false:true}
				<script type="text/javascript">
				//<![CDATA[
				var default_state = {$ldelim}'billing':'{$form_values.$elm_id|escape:javascript}'{$rdelim};
				//]]>
				</script>


				<select id="elm_{$element.element_id}" name="form_values[{$element.element_id}]">
					<option label="" value="">- {$lang.select_state} -</option>
				</select>
				<input type="text" class="input-text hidden" id="elm_{$element.element_id}_d" name="form_values[{$element.element_id}]" size="32" maxlength="64" value="{$form_values.$elm_id}" disabled="disabled" />
			
			
			{elseif $element.element_type == $smarty.const.FORM_FILE}
				{script src="js/fileuploader_scripts.js"}
				{include file="common_templates/fileuploader.tpl" var_name="fb_files[`$element.element_id`]"}
			{/if}

			{hook name="pages:form_elements"}
			{/hook}
		</div>
	{/if}
	{/foreach}
	{if $settings.Image_verification.use_for_form_builder == "Y"}
		{include file="common_templates/image_verification.tpl" id="forms_`$page.page_id`"}
	{/if}

	<div class="buttons-container">
		{include file="buttons/button.tpl" but_role="submit" but_text=$lang.submit but_name="dispatch[pages.send_form]"}
	</div>

	</form>

	{/capture}
	{include file="common_templates/group.tpl" content=$smarty.capture.group}
	{hook name="pages:page_content"}{/hook}
	{/if}
{/if}