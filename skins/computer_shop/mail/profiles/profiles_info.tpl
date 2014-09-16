{* $Id: profiles_info.tpl 10184 2010-07-23 11:11:24Z klerik $ *}
{assign var="send_password" value=true}
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td colspan="2" class="form-title">{$lang.user_account_info}<hr size="1" noshade></td>
		</tr>
		{if $settings.General.use_email_as_login != 'Y' && $user_data.user_type != 'S'}
		<tr>
			<td class="form-field-caption" nowrap>{$lang.username}:&nbsp;</td>
			<td >{$user_data.user_login}</td>
		</tr>
		{else}
		<tr>
			<td class="form-field-caption" nowrap>{$lang.email}:&nbsp;</td>
			<td >{$user_data.email}</td>
		</tr>
		{/if}
		{if $send_password}
		<tr>
			<td class="form-field-caption" nowrap>{$lang.password}:&nbsp;</td>
			<td >{$password}</td>
		</tr>
		{/if}
		{if $usergroups && $user_data.usergroup_id}
		<tr>
			<td class="form-field-caption" nowrap>{$lang.usergroup}:&nbsp;</td>
			<td>
			{foreach from=$usergroups item=usergroup}
			{if $user_data.usergroup_id == $usergroup.usergroup_id}{$usergroup.usergroup}{/if}
			{/foreach}
			</td>
		</tr>
		<tr>
			<td class="form-field-caption" nowrap>{$lang.usergroup_status}:&nbsp;</td>
			<td>{if $user_data.usergroup_status == 'P'}{$lang.pending}{else}{$lang.active}{/if}</td>
		</tr>
		{/if}
		{if $settings.General.user_multiple_profiles == 'Y'}
		<tr>
			<td class="form-title">{$lang.profile_name}:&nbsp;</td>
			<td>{$user_data.profile_name}</td>
		</tr>
		{/if}
		{if $user_data.tax_exempt == 'Y'}
		<tr>
			<td class="form-title">{$lang.tax_exempt}:&nbsp;</td>
			<td>{$lang.yes}</td>
		</tr>
		{/if}
		</table>
	</td>	
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
</table>

{assign var="profile_fields" value=$user_data.user_type|fn_get_profile_fields}
{split data=$profile_fields.C size=2 assign="contact_fields" simple=true}
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top" width="50%">
		<table>
			{include file="profiles/profile_fields_info.tpl" fields=$contact_fields.0 title=$lang.contact_information}
		</table>
	</td>
	<td width="1%">&nbsp;</td>
	<td valign="top" width="49%">
		<table>
			{include file="profiles/profile_fields_info.tpl" fields=$contact_fields.1}
		</table>
	</td>
</tr>
{if $profile_fields.B || $profile_fields.S}
<tr>
	<td valign="top">
	{if $profile_fields.B}
		<p></p>
		<table>
			{include file="profiles/profile_fields_info.tpl" fields=$profile_fields.B title=$lang.billing_address}
		</table>
	{else}
		&nbsp;
	{/if}
	</td>
	<td>&nbsp;</td>
	<td valign="top">
	{if $profile_fields.S}	
		<p></p>
		<table>
			{include file="profiles/profile_fields_info.tpl" fields=$profile_fields.S title=$lang.shipping_address}
		</table>
	{else}
		&nbsp;
	{/if}
	</td>
</tr>
{/if}
</table>
