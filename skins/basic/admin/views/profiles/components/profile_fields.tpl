{* $Id: profile_fields.tpl 10304 2010-08-03 06:35:36Z 2tl $ *}

{if $profile_fields.$section}

{if !$nothing_extra}
	{include file="common_templates/subheader.tpl" title=$title}
{/if}

{if $shipping_flag}
<div class="select-field">
	<input class="checkbox hidden" id="elm_ship_to_another" type="checkbox" name="ship_to_another" value="Y" onclick="$('#sa').switchAvailability(!this.checked);" {if $ship_to_another}checked="checked"{/if} />

	<p {if $ship_to_another}class="hidden"{/if} id="on_sta_notice">
		{$lang.text_ship_to_billing}.&nbsp;<a class="cm-combination dashed" onclick="$('#elm_ship_to_another').click(); jQuery.profiles.rebuild_states('shipping');">{$lang.ship_to_another}</a>
	</p>
	<p {if !$ship_to_another}class="hidden"{/if} id="off_sta_notice">
		<a class="cm-combination dashed" onclick="$('#elm_ship_to_another').click();">{$lang.text_ship_to_billing}</a>
	</p>
</div>
{elseif $section == "S"}
	{assign var="ship_to_another" value=true}
	<input type="hidden" name="ship_to_another" value="Y" />
{/if}

{if $body_id}
	<div id="{$body_id}" {if !$ship_to_another}class="hidden"{/if}>
{/if}

{if $section == "S" && !$ship_to_another}
	{assign var="disabled_param" value="disabled=\"disabled\""}
{else}
	{assign var="disabled_param" value=""}
{/if}

{if $location == "checkout" && $auth.user_id && $settings.General.user_multiple_profiles == "Y" && ($section == "B" || $section == "S")} {* Select user profile *}
<div class="form-field">
	<label for="elm_profile_id">{$lang.select_profile}:</label>
	<select name="profile_id" id="elm_profile_id" onchange="jQuery.ajaxRequest('{"checkout.checkout"|fn_url:'C':'rel':'&'}', {$ldelim}result_ids: 'checkout_steps, cart_items, checkout_totals', 'user_data[profile_id]': this.value, 'update_step': '{$update_step}', 'edit_steps[]': '{$update_step}', 'ship_to_another': '{$cart.ship_to_another}'{$rdelim});" class="select-expanded">
	{*if !$skip_create}
		<option selected="selected" value="0">-&nbsp;{$lang.create_profile}&nbsp;-</option>
	{/if*}
	{foreach from=$user_profiles item="user_profile"}
		<option value="{$user_profile.profile_id}" {if $cart.profile_id == $user_profile.profile_id}selected="selected"{/if}>{$user_profile.profile_name}</option>
	{/foreach}
	</select>
	{if $cart.user_data.profile_id && $cart.user_data.profile_type != "P"}
		<a {if $use_ajax}class="cm-ajax"{/if} href="{"profiles.update.delete_profile?profile_id=`$cart.profile_id`"|fn_url}" rev="checkout_steps,cart_items,checkout_totals">{$lang.delete}</a>
	{/if}
</div>
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
		<select id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			{foreach from=$titles item="t"}
			<option {if $value == $t.param}selected="selected"{/if} value="{$t.param}">{$t.descr}</option>
			{/foreach}
		</select>

	{elseif $field.field_type == "A"}  {* State selectbox *}
		<select id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			<option value="">- {$lang.select_state} -</option>
			{* Initializing default states *}
			{assign var="country_code" value=$settings.General.default_country}
			{assign var="state_code" value=$value|default:$settings.General.default_country}
			{if $states}
				{foreach from=$states.$country_code item=state}
					<option {if $state_code == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
				{/foreach}
			{/if}
		</select><input type="text" id="elm_{$field.field_id}_d" name="{$data_name}[{$data_id}]" size="32" maxlength="64" value="{$value}" disabled="disabled" class="input-text hidden cm-skip-avail-switch" />
	
	{elseif $field.field_type == "O"}  {* Countries selectbox *}
		{assign var="_country" value=$value|default:$settings.General.default_country}
		<select id="{$id_prefix}elm_{$field.field_id}" class="{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			<option value="">- {$lang.select_country} -</option>
			{foreach from=$countries item=country}
			<option {if $_country == $country.code}selected="selected"{/if} value="{$country.code}">{$country.country}</option>
			{/foreach}
		</select>
	
	{elseif $field.field_type == "C"}  {* Checkbox *}
		<input type="hidden" name="{$data_name}[{$data_id}]" value="N" {$disabled_param} />
		<input type="checkbox" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" value="Y" {if $value == "Y"}checked="checked"{/if} class="checkbox" {$disabled_param} />

	{elseif $field.field_type == "T"}  {* Textarea *}
		<textarea class="input-textarea" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" cols="32" rows="3" {$disabled_param}>{$value}</textarea>
	
	{elseif $field.field_type == "D"}  {* Date *}
		{include file="common_templates/calendar.tpl" date_id="elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value start_year="1902" end_year="0" extra=$disabled_param}

	{elseif $field.field_type == "S"}  {* Selectbox *}
		<select id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" {$disabled_param}>
			{if $field.required != "Y"}
			<option value="">--</option>
			{/if}
			{foreach from=$field.values key=k item=v}
			<option {if $value == $k}selected="selected"{/if} value="{$k}">{$v}</option>
			{/foreach}
		</select>
	
	{elseif $field.field_type == "R"}  {* Radiogroup *}
		<div class="select-field">
		{foreach from=$field.values key=k item=v name="rfe"}
		<input class="radio" type="radio" id="{$id_prefix}elm_{$field.field_id}_{$k}" name="{$data_name}[{$data_id}]" value="{$k}" {if (!$value && $smarty.foreach.rfe.first) || $value == $k}checked="checked"{/if} {$disabled_param} /><label for="{$id_prefix}elm_{$field.field_id}_{$k}">{$v}</label>
		{/foreach}
		</div>

	{else}  {* Simple input *}
		<input type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" size="32" value="{$value}" class="input-text" {$disabled_param} />
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
