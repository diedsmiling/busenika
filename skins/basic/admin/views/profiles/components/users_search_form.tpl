{* $Id: users_search_form.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

{capture name="section"}

<form name="user_search_form" action="{""|fn_url}" method="get">

{if $smarty.request.redirect_url}
<input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}

{if $selected_section != ""}
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
{/if}

{if $search.user_type}
<input type="hidden" name="user_type" value="{$search.user_type}" />
{/if}

{$extra}

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field nowrap">
		<label for="elm_name">{$lang.name}:</label>
		<div class="break">
			<input class="search-input-text" type="text" name="name" id="elm_name" value="{$search.name}" />
			{include file="buttons/search_go.tpl" search="Y" but_name=$dispatch}
		</div>
	</td>
	<td class="search-field">
		<label for="elm_company">{$lang.company}:</label>
		<div class="break">
			<input class="input-text" type="text" name="company" id="elm_company" value="{$search.company}" />
		</div>
	</td>
	<td class="search-field">
		<label for="elm_email">{$lang.email}:</label>
		<div class="break">
			<input class="input-text" type="text" name="email" id="elm_email" value="{$search.email}" />
		</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/search.tpl" but_name="dispatch[$dispatch]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<td>
		{if $settings.General.use_email_as_login != "Y"}
		<div class="search-field">
			<label for="elm_user_login">{$lang.username}:</label>
			<input class="input-text" type="text" name="user_login" id="elm_user_login" value="{$search.user_login}" />
		</div>
		{/if}
		<div class="search-field">
			<label for="elm_usergroup_id">{$lang.usergroup}:</label>
			<select name="usergroup_id" id="elm_usergroup_id">
				<option value="{$smarty.const.ALL_USERGROUPS}"> -- </option>
				<option value="0" {if $search.usergroup_id === "0"}selected="selected"{/if}>{$lang.not_a_member}</option>
				{foreach from=$usergroups item=usergroup}
				<option value="{$usergroup.usergroup_id}" {if $search.usergroup_id == $usergroup.usergroup_id}selected="selected"{/if}>{$usergroup.usergroup}</option>
				{/foreach}
			</select>
		</div>
		<div class="search-field">
			<label for="elm_tax_exempt">{$lang.tax_exempt}:</label>
			<select name="tax_exempt" id="elm_tax_exempt">
				<option value="">--</option>
				<option value="Y" {if $search.tax_exempt == "Y"}selected="selected"{/if}>{$lang.yes}</option>
				<option value="N" {if $search.tax_exempt == "N"}selected="selected"{/if}>{$lang.no}</option>
			</select>
		</div>

		<div class="search-field">
			<label for="elm_address">{$lang.address}:</label>
			<input class="input-text" type="text" name="address" id="elm_address" value="{$search.address}" />
		</div>
	</td>
	<td>

		<div class="search-field">
			<label for="elm_city">{$lang.city}:</label>
			<input class="input-text" type="text" name="city" id="elm_city" value="{$search.city}" />
		</div>
		<div class="search-field">
			<label for="srch_country" class="cm-country cm-location-search">{$lang.country}:</label>
			<select id="srch_country" name="country" class="cm-location-search">
				<option value="">- {$lang.select_country} -</option>
				{foreach from=$countries item=country}
				<option value="{$country.code}" {if $search.country == $country.code}selected="selected"{/if}>{$country.country}</option>
				{/foreach}
			</select>
		</div>

		<div class="search-field">
			<label for="srch_state" class="cm-state cm-location-search">{$lang.state}:</label>
			<input type="text" id="srch_state_d" name="state" maxlength="64" value="{$search.state}" disabled="disabled" class="input-text" />
			<select id="srch_state" class="hidden" name="state">
				<option value="">- {$lang.select_state} -</option>
			</select>
		</div>

		<div class="search-field">
			<label for="elm_zipcode">{$lang.zip_postal_code}:</label>
			<input class="input-text" type="text" name="zipcode" id="elm_zipcode" value="{$search.zipcode}" />
		</div>
	</td>
</tr>
</table>

{hook name="profiles:search_form"}
{/hook}

<div class="search-field">
	<label>{$lang.ordered_products}:</label>
	{include file="pickers/search_products_picker.tpl"}
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="users"}

</form>
<script type="text/javascript">
//<![CDATA[
	default_state = {$ldelim}'search':'{$smarty.request.state|escape:javascript}'{$rdelim};
//]]>
</script>

{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
