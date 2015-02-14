{* $Id: profiles_info.tpl 10226 2010-07-27 12:20:00Z alexions $ *}

{assign var="profile_fields" value=$location|fn_get_profile_fields}

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr valign="top">
	<td width="{if $payment_info}34%{else}50%{/if}">
		{if $user_data.b_firstname || $user_data.b_lastname || $user_data.b_address || $user_data.b_address_2 || $user_data.b_city || $user_data.b_country_descr || $user_data.b_state_descr || $user_data.b_zipcode || $profile_fields.B}
		{include file="common_templates/subheader.tpl" title=$lang.billing_address}
		<div class="details-block">
			{if $user_data.b_firstname || $user_data.b_lastname}
				<p class="strong">{$user_data.b_firstname} {$user_data.b_lastname}</p>
			{/if}
			{if $user_data.b_address}
				<p>{$user_data.b_address}</p>
			{/if}
			{if $user_data.b_address_2}
				<p>{$user_data.b_address_2}</p>
			{/if}
			{if $user_data.b_city || $user_data.b_state_descr || $user_data.b_zipcode}
				<p>{$user_data.b_city} {$user_data.b_state_descr} {$user_data.b_zipcode}</p>
			{/if}
			{if $user_data.b_country_descr}<p>{$user_data.b_country_descr}</p>{/if}
			{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.B}
			{if $user_data.b_phone}
				<p>{$user_data.b_phone}</p>
			{/if}
		</div>
		{/if}
	</td>
	<td class="details-block-container" width="{if $payment_info}34%{else}50%{/if}">
		{if $user_data.s_firstname || $user_data.s_lastname || $user_data.s_address || $user_data.s_address_2 || $user_data.s_city || $user_data.s_country_descr || $user_data.s_state_descr || $lang.zip_postal_code || $profile_fields.S}
		{include file="common_templates/subheader.tpl" title=$lang.shipping_address}
		<div class="details-block">
			{if $user_data.s_firstname || $user_data.s_lastname}
				<p class="strong">{$user_data.s_firstname} {$user_data.s_lastname}</p>
			{/if}
			{if $user_data.s_address}
				<p>{$user_data.s_address}</p>
			{/if}
			{if $user_data.s_address_2}
				<p>{$user_data.s_address_2}</p>
			{/if}
			{if $user_data.s_city || $user_data.s_state_descr || $user_data.s_zipcode}<p>{$user_data.s_city} {$user_data.s_state_descr} {$user_data.s_zipcode}</p>{/if}
			{if $user_data.s_country_descr}<p>{$user_data.s_country_descr}</p>{/if}
			{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.S}
			{if $user_data.s_phone}
				<p>{$user_data.s_phone}</p>
			{/if}
		</div>
		{/if}
	</td>
</tr>
{if $user_data.email || $user_data.phone || $user_data.fax || $user_data.company || $user_data.url}
<tr>
	<td colspan="2">
	<div class="details-block clear">
		{if $user_data.ip_address}
			<div class="form-field float-right">
				<label>{$lang.ip_address}:</label>
				{$user_data.ip_address}
			</div>
		{/if}
		
		<p class="strong">{if $user_data.title_descr}{$user_data.title_descr}&nbsp;{/if}{$user_data.firstname}&nbsp;{$user_data.lastname}, <a href="mailto:{$user_data.email|escape:url}">{$user_data.email}</a></p>
		<div class="clear">
			<div class="left-col">
				{if $user_data.phone}
					<div class="form-field">
						<label>{$lang.phone}:</label>
						<strong>{$user_data.phone}</strong>
					</div>
				{/if}
				{if $user_data.fax}
					<div class="form-field">
						<label>{$lang.fax}:</label>
						<strong>{$user_data.fax}</strong>
					</div>
				{/if}
			</div>
			<div class="float-left">
				{if $user_data.company}
					<div class="form-field">
						<label>{$lang.company}:</label>
						<strong>{$user_data.company}</strong>
					</div>
				{/if}
				{if $user_data.url}
					<div class="form-field">
						<label>{$lang.website}:</label>
						<strong>{$user_data.url}</strong>
					</div>
				{/if}
			</div>
		</div>

		{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.C customer_info="Y"}
        {if $user_data.referer}<p><label>{$lang.referer}:</label>{$user_data.referer}</p>{/if}
		{if $email_changed}
			<div class="form-field">
				<label>{$lang.attention}</label>
				{$lang.notice_update_customer_details}
			</div>
	
			<div class="select-field">
				<input type="checkbox" name="update_customer_details" id="update_customer_details" value="Y" class="checkbox" />
				<label for="update_customer_details">{$lang.update_customer_info}</label>
			</div>
		{/if}
	</div>
	</td>
</tr>
{/if}
</table>

