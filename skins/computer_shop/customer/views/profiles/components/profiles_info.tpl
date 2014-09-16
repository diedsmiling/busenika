{* $Id: profiles_info.tpl 9773 2010-06-09 12:38:38Z zeke $ *}

{include file="common_templates/subheader.tpl" title=$lang.customer_information}

{assign var="profile_fields" value=$location|fn_get_profile_fields}
{split data=$profile_fields.C size=2 assign="contact_fields" simple=true}

<h5 class="info-field-title">{$lang.contact_information}</h5>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<td width="50%" class="info-field-body"{if $profile_fields.B && $profile_fields.S} colspan="2"{/if}>
		{include file="views/profiles/components/profile_fields_info.tpl" fields=$contact_fields.0 title=$lang.contact_information}
	</td>
	<td width="50%" class="info-field-body">
		{include file="views/profiles/components/profile_fields_info.tpl" fields=$contact_fields.1}
	</td>
</tr>
{if $profile_fields.B || $profile_fields.S}
<tr valign="top">
	{if $profile_fields.B}
	<td width="48%"{if !$profile_fields.S} colspan="2"{/if}>
		<h5 class="info-field-title">{$lang.billing_address}</h5>
		<div class="info-field-body">{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.B title=$lang.billing_address}</div>
	</td>
	{/if}
	{if $profile_fields.B && $profile_fields.S}<td width="4%">&nbsp;</td>{/if}
	{if $profile_fields.S}
	<td width="48%"{if !$profile_fields.B} colspan="2"{/if}>
		<h5 class="info-field-title">{$lang.shipping_address}</h5>
		<div class="info-field-body">{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.S title=$lang.shipping_address}</div>
	</td>
	{/if}
</tr>
{/if}
</table>

