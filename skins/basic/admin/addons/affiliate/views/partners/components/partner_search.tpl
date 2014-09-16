{* $Id: partner_search.tpl 9743 2010-06-08 08:18:11Z lexa $ *}

{capture name="section"}
<form name="partner_search_form" action="{""|fn_url}" method="get">

<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
		<label for="elm_name">{$lang.name}:</label>
		<div class="break">
			<input class="input-text" type="text" name="name" id="elm_name" value="{$search.name}" />
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
		{include file="buttons/search.tpl" but_name="dispatch[partners.manage]" but_role="submit"}
	</td>
</tr>
</table>

{capture name="advanced_search"}

{if $settings.General.use_email_as_login != "Y"}
<div class="search-field">
	<label for="elm_user_login">{$lang.username}:</label>
	<input class="input-text" type="text" name="user_login" id="elm_user_login" value="{$search.user_login}" />
</div>

<hr />
{/if}

<table cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
	<td width="55%" class="nowrap">
		<div class="search-field">
			<label for="elm_address">{$lang.address}:</label>
			<input class="input-text" type="text" name="address" id="elm_address" value="{$search.address}" />
		</div>

		<div class="search-field">
			<label for="elm_city">{$lang.city}:</label>
			<input class="input-text" type="text" name="city" id="elm_city" value="{$search.city}" />
		</div>

		<div class="search-field">
			<label for="elm_zipcode">{$lang.zip_postal_code}:</label>
			<input class="input-text" type="text" name="zipcode" id="elm_zipcode" value="{$search.zipcode}" />
		</div>
		</td>
	<td class="nowrap">
		<div class="search-field">
			<label for="srch_country" class="cm-country cm-location-search">{$lang.country}:</label>
			<select id="srch_country" name="country" class="cm-location-search">
				<option value="">- {$lang.select_country} -</option>
				{foreach from=$countries item=country}
					<option value="{$country.code}" {if $smarty.request.country == $country.code}selected="selected"{/if}>{$country.country}</option>
				{/foreach}
			</select>
		</div>

		<div class="search-field">
			<label for="srch_state" class="cm-state cm-location-search">{$lang.state}:</label>
			<input type="text" id="srch_state_d" name="state" maxlength="64" value="{$search.state}" disabled="disabled" class="input-text hidden" />
			<select id="srch_state" name="state">
				<option value="">- {$lang.select_state} -</option>
			</select>
		</div>
	</td>
</tr>
</table>

<hr />

<div class="search-field">
	<label for="elm_approved">{$lang.status}:</label>
	{*<input type="checkbox" name="approved" value="Y" {if $search.approved == "Y"}checked="checked"{/if} />*}
	<select name="approved" id="elm_approved">
		<option value="0" {if $search.approved == "0"}selected="selected"{/if}> -- </option>
		<option value="N" {if $search.approved == "N"}selected="selected"{/if}>{$lang.awaiting_approval}</option>
		<option value="A" {if $search.approved == "A"}selected="selected"{/if}>{$lang.approved}</option>
		<option value="D" {if $search.approved == "D"}selected="selected"{/if}>{$lang.Declined}</option>
	</select>
</div>

<div class="search-field">
	<label for="elm_plan_id">{$lang.plan}:</label>
	<select name="plan_id" id="elm_plan_id">
		<option value="0" {if $search.plan_id == "0"}selected="selected"{/if}> -- </option>
		<option value="-1" {if $search.plan_id == "-1"}selected="selected"{/if}>{$lang.without_plan}</option>
		{if $affiliate_plans}{html_options options=$affiliate_plans selected=$search.plan_id}{/if}
	</select>
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch=$dispatch view_type="affiliates"}

</form>
<script type="text/javascript">
//<![CDATA[
	default_state['search'] = '{$search.state|escape:javascript}';
//]]>
</script>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
