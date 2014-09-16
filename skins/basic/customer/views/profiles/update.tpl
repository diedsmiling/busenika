{* $Id: update.tpl 10423 2010-08-13 13:24:52Z alexions $ *}

{include file="views/profiles/components/profiles_scripts.tpl"}

{capture name="tabsbox"}
	<div id="content_general">
		{$lang.text_mandatory_fields}
		<form name="profile_form" action="{""|fn_url}" method="post">
		<input id="selected_section" type="hidden" value="general" name="selected_section"/>
		<input id="default_card_id" type="hidden" value="" name="default_cc"/>
		<input type="hidden" name="profile_id" value="{$user_data.profile_id}" />
		{capture name="group"}
		{include file="views/profiles/components/profiles_account.tpl"}
		{include file="views/profiles/components/profile_fields.tpl" section="C" title=$lang.contact_information}

		{if $profile_fields.B || $profile_fields.S}
			{if $settings.General.user_multiple_profiles == "Y" && $mode == "update"}
				<p>{$lang.text_multiprofile_notice}</p>
				{include file="views/profiles/components/multiple_profiles.tpl" profile_id=$user_data.profile_id}	
			{/if}
			
			{if $settings.General.address_position == "billing_first"}
				{assign var="first_section" value="B"}
				{assign var="first_section_text" value=$lang.billing_address}
				{assign var="sec_section" value="S"}
				{assign var="sec_section_text" value=$lang.shipping_address}
				{assign var="body_id" value="sa"}
			{else}
				{assign var="first_section" value="S"}
				{assign var="first_section_text" value=$lang.shipping_address}
				{assign var="sec_section" value="B"}
				{assign var="sec_section_text" value=$lang.billing_address}
				{assign var="body_id" value="ba"}
			{/if}
			
			{include file="views/profiles/components/profile_fields.tpl" section=$first_section body_id="" ship_to_another="Y" title=$first_section_text}
			{include file="views/profiles/components/profile_fields.tpl" section=$sec_section body_id=$body_id ship_to_another="Y" title=$sec_section_text address_flag=true ship_to_another=$ship_to_another}
		{/if}

		{hook name="profiles:account_update"}
		{/hook}

		{if $mode == "add" && $settings.Image_verification.use_for_register == "Y"}
			{include file="common_templates/image_verification.tpl" id="register" align="center"}
		{/if}

		{/capture}
		{include file="common_templates/group.tpl" content=$smarty.capture.group}

		<div class="buttons-container center">
			{if $action}
				{assign var="_action" value="$action"}
			{/if}
			{if $mode == "update"}
				{include file="buttons/save.tpl" but_name="dispatch[profiles.update.$_action]" but_id="save_profile_but"}
			{else}
				{include file="buttons/register_profile.tpl" but_name="dispatch[profiles.add.$_action]"}
			{/if}
		</div>
		</form>
	</div>
	
	{if $mode == "update"}
	{if $usergroups && $user_data.user_type != "A"}
	<div id="content_usergroups">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
		<tr>
			<th width="30%">{$lang.usergroup}</th>
			<th width="30%">{$lang.status}</th>
			{if $settings.General.allow_usergroup_signup == "Y"}
			<th width="40%">{$lang.action}</th>
			{/if}
		</tr>
		{foreach from=$usergroups item=usergroup}
		{if $user_data.usergroups[$usergroup.usergroup_id]}
			{assign var="ug_status" value=$user_data.usergroups[$usergroup.usergroup_id].status}
		{else}
			{assign var="ug_status" value="F"}
		{/if}
		{if $settings.General.allow_usergroup_signup == "Y" || $settings.General.allow_usergroup_signup != "Y" && $ug_status == "A"}
		<tr {cycle values=",class=\"table-row\""}>
			<td>{$usergroup.usergroup}</td>
			<td class="center">
				{if $ug_status == "A"}
					{$lang.active}
					{assign var="_link_text" value=$lang.remove}
				{elseif $ug_status == "F"}
					{$lang.available}
					{assign var="_link_text" value=$lang.join}
				{elseif $ug_status == "D"}
					{$lang.declined}
					{assign var="_link_text" value=$lang.join}
				{elseif $ug_status == "P"}
					{$lang.pending}
					{assign var="_link_text" value=$lang.cancel}
				{/if}
			</td>
			{if $settings.General.allow_usergroup_signup == "Y"}
			<td>
				<a class="cm-ajax" rev="content_usergroups" href="{"profiles.request_usergroup?usergroup_id=`$usergroup.usergroup_id`&amp;status=`$ug_status`"|fn_url}">{$_link_text}</a>
			</td>
			{/if}
		</tr>
		{/if}
		{/foreach}
		<tr class="table-footer">
			<td colspan="{if $settings.General.allow_usergroup_signup == "Y"}3{else}2{/if}">&nbsp;</td>
		</tr>
		</table>
	<!--content_usergroups--></div>
	{/if}
	{if $settings.General.user_store_cc == "Y"}
	<div id="content_credit_cards">
		{include file="views/profiles/components/credit_cards.tpl"}
	</div>
	{/if}
	{/if}
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{capture name="mainbox_title"}{$lang.profile_details}{/capture}
