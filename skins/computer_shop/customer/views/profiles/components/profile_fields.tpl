{* $Id: profile_fields.tpl 10427 2010-08-16 12:37:54Z alexions $ *}

{if $show_email}
	<div class="form-field">
		<label for="{$id_prefix}elm_email" class="cm-required cm-email">{$lang.email}:</label>
		<input type="text" id="{$id_prefix}elm_email" name="user_data[email]" size="32" value="{$user_data.email}" class="input-text {$_class}" {$disabled_param} />
	</div>
{else}

{if $profile_fields.$section}

{if $address_flag}
	{capture name="title_extra"}
		<span class="title-extra">
			<input type="hidden" name="copy_address" value="" />
			<input class="checkbox" id="elm_ship_to_another" type="checkbox" name="copy_address" value="Y" onclick="$('#{$body_id}').switchAvailability(this.checked, false);" {if !$ship_to_another}checked="checked"{/if} />
			<a onclick="$('#elm_ship_to_another').click();">{if $section == "S"}{$lang.use_billing}{else}{$lang.use_shipping}{/if}</a>
		</span>
	{/capture}
{/if}

{if !$nothing_extra}
	{include file="common_templates/subheader.tpl" title=$title extra=$smarty.capture.title_extra}
{/if}

{if $body_id}
	<div id="{$body_id}">
{/if}

{if !$ship_to_another && ($section == "S" || $section == "B")}
	{assign var="disabled_param" value="disabled=\"disabled\""}
	{assign var="_class" value="disabled"}
{else}
	{assign var="disabled_param" value=""}
	{assign var="_class" value=""}
{/if}

{foreach from=$profile_fields.$section item=field}
{if $field.field_name}
	{assign var="data_name" value="user_data"}
	{assign var="data_id" value=$field.field_name}
	{assign var="value" value=$user_data.$data_id}
{else}
	{assign var="data_name" value="user_data[fields]"}
	{assign var="data_id" value=$field.field_id}
	{assign var="value" value=$user_data.fields.$data_id}
{/if}

<div class="form-field">
	<label for="{$id_prefix}elm_{$field.field_id}" class="{if $field.required == "Y"}cm-required{/if}{if $field.field_type == "P"} cm-phone{/if}{if $field.field_type == "Z"} cm-zipcode{/if}{if $field.field_type == "E"} cm-email{/if}{if $field.field_type == "A"} cm-state{/if}{if $field.field_type == "O"} cm-country{/if} {if $field.field_type == "O" || $field.field_type == "A" || $field.field_type == "Z"}{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}{/if}">{$field.description}:</label>
	{if $field.field_type == "L"} {* Titles selectbox *}
		<select id="{$id_prefix}elm_{$field.field_id}" class="{$_class}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			{foreach from=$titles item="t"}
			<option {if $value == $t.param}selected="selected"{/if} value="{$t.param}">{$t.descr}</option>
			{/foreach}
		</select>

	{elseif $field.field_type == "A"}  {* State selectbox *}
		<select id="{$id_prefix}elm_{$field.field_id}" class="{$_class}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			<option value="">- {$lang.select_state} -</option>
			{* Initializing default states *}
			{assign var="country_code" value=$settings.General.default_country}
			{assign var="state_code" value=$value|default:$settings.General.default_country}
			{if $states}
				{foreach from=$states.$country_code item=state}
					<option {if $state_code == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
				{/foreach}
			{/if}
		</select><input type="text" id="elm_{$field.field_id}_d" name="{$data_name}[{$data_id}]" size="32" maxlength="64" value="{$value}" disabled="disabled" class="input-text hidden {if $_class}disabled{/if} cm-skip-avail-switch" />
	
	{elseif $field.field_type == "O"}  {* Countries selectbox *}
		{assign var="_country" value=$value|default:$settings.General.default_country}
		<select id="{$id_prefix}elm_{$field.field_id}" class="{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {$_class}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			<option value="">- {$lang.select_country} -</option>
			{foreach from=$countries item=country}
			<option {if $_country == $country.code}selected="selected"{/if} value="{$country.code}">{$country.country}</option>
			{/foreach}
		</select>
	
	{elseif $field.field_type == "C"}  {* Checkbox *}
		<input type="hidden" name="{$data_name}[{$data_id}]" value="N" {$disabled_param} />
		<input type="checkbox" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" value="Y" {if $value == "Y"}checked="checked"{/if} class="checkbox {$_class}" {$disabled_param} />

	{elseif $field.field_type == "T"}  {* Textarea *}
		<textarea class="input-textarea {$_class}" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" cols="32" rows="3" {$disabled_param}>{$value}</textarea>
	
	{elseif $field.field_type == "D"}  {* Date *}
		{include file="common_templates/calendar.tpl" date_id="`$id_prefix`elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value start_year="1902" end_year="0" extra=$disabled_param}

	{elseif $field.field_type == "S"}  {* Selectbox *}
		<select id="{$id_prefix}elm_{$field.field_id}" class="{$_class}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			{if $field.required != "Y"}
			<option value="">--</option>
			{/if}
			{foreach from=$field.values key=k item=v}
			<option {if $value == $k}selected="selected"{/if} value="{$k}">{$v}</option>
			{/foreach}
		</select>
	
	{elseif $field.field_type == "R"}  {* Radiogroup *}
		{foreach from=$field.values key=k item=v name="rfe"}
		<input class="radio valign {$_class}" type="radio" id="{$id_prefix}elm_{$field.field_id}_{$k}" name="{$data_name}[{$data_id}]" value="{$k}" {if (!$value && $smarty.foreach.rfe.first) || $value == $k}checked="checked"{/if} {$disabled_param} /><span class="radio">{$v}</span>
		{/foreach}

	{else}  {* Simple input *}
		<input type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" size="32" value="{$value}" class="input-text {$_class}" {$disabled_param} />
	{/if}

	{if ($section == "B" || $section == "S") && $field.field_type == "A"}
	<span>&nbsp;
	<script type="text/javascript">
	//<![CDATA[
	default_state[{if $section == 'S'}'shipping'{else}'billing'{/if}] = '{$value|default:$settings.General.default_country|escape:javascript}';
	
	//]]>
	</script>
	</span>
	{/if}
</div>
{/foreach}

{if $body_id}
</div>
{/if}

{/if}
{/if}
{literal}
	<script type="text/javascript" class="cm-ajax-force">
		console.log("executed2");
	$(document).ready(function(){
		$("#elm_35").change(function(){
			if (/^(7[\- ]?)?(\(?9\d{2}\)?[\- ]?)?[\d\- ]{7,10}$/.test($("#elm_35").val())){
				$("#elm_35").removeClass("cm-failed-field");
				$("#elm_35").removeClass("failedEarlier");
				$(".error-message, elm_35").remove();
			}else{
				$("#elm_35").addClass("cm-failed-field");		
				$("#elm_35").addClass("failedEarlier");			
				if ($(".error-message.elm_35").length){
					$(".error-message.elm_35 .message").html("Неправильный номер");
				}else {
					$($("#elm_35").parent()).append('<div class="error-message elm_35"><div class="arrow"></div><div class="message"> Неправильный номер </div></div>');
				}
			}
		});
		$("#elm_35").mask("7(999) 999-9999");
	});
	
	</script>
{/literal}
{if $selfService}
	{literal}
	<script type="text/javascript" class="cm-ajax-force">
	console.log("executed3");
	$(document).ready(function(){
		$(".cm-required").not("label[for='elm_35']").removeClass("cm-required");
		$("#elm_54, #elm_25, #elm_44, #elm_19, #elm_56, #elm_58, #elm_60, #elm_62, #elm_48").addClass("disabled").attr("disabled", true);
	});
	</script>
	{/literal}
{/if}