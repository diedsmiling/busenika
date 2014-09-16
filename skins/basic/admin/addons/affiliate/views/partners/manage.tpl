{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{include file="views/profiles/components/profiles_scripts.tpl"}

{script src="js/picker.js"}

{capture name="mainbox"}

{include file="addons/affiliate/views/partners/components/partner_search.tpl" dispatch="partners.manage"}

{$lang.text_list_of_user_accounts|replace:"[account]":$lang.affiliate}
<p>

<form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="partnerlist_form">
<input type="hidden" name="fake" value="1" />

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="7%"><a class="{$ajax_class}{if $search.sort_by == "id"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a></th>
	{if $settings.General.use_email_as_login == "Y"}
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "email"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=email&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.email}</a></th>
	{else}
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "username"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=username&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.username}</a></th>
	{/if}
	<th width="40%"><a class="{$ajax_class}{if $search.sort_by == "name"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=name&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.name}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.registered}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "plan"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=plan&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.plan}</a></th>
	<th>&nbsp;</th>
</tr>
{foreach from=$partners item=user}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
		<input type="checkbox" name="partner_ids[]" value="{$user.user_id}" {*if $user.approved == "A" || $user.approved == "D"}disabled="disabled"{/if*} class="checkbox cm-item" /></td>
	<td><a href="{"partners.update?user_id=`$user.user_id`"|fn_url}" class="underlined">&nbsp;{$user.user_id}&nbsp;</a></td>
	{if $settings.General.use_email_as_login == "Y"}
	<td><a href="{"partners.update?user_id=`$user.user_id`"|fn_url}" class="underlined">{$user.email}</a></td>
	{else}
	<td><a href="{"partners.update?user_id=`$user.user_id`"|fn_url}" class="underlined">{$user.user_login}</a></td>
	{/if}
	<td><a href="{"partners.update?user_id=`$user.user_id`"|fn_url}" class="underlined">{$user.lastname} {$user.firstname}</a></td>
	<td>{$user.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>
		{if $user.approved == "A"}
			{$lang.approved}
		{elseif $user.approved == "D"}
			{$lang.declined}
		{else}
			<span class="required-field-mark">{$lang.awaiting_approval}</span>
		{/if}
	</td>
	{if $user.approved == "A" || ($user.approved == "D" && $user.plan_id)}
	<td>
		<select name="update_data[{$user.user_id}][plan_id]" id="id_select_plan_{$user.user_id}" {if $user.approved == "D"}disabled="disabled"{/if}>
			<option value="0" {if !$user.plan_id}selected="selected"{/if}> -- </option>
			{if $affiliate_plans}{html_options options=$affiliate_plans selected=$user.plan_id}{/if}
		</select>
	</td>
	{else}
	<td>- {$lang.no} -</td>
	{/if}
	{*<td>{include file="common_templates/price.tpl" value=$user.balance}</td>*}
	<td class="nowrap">
		{include file="common_templates/table_tools_list.tpl" href="partners.update?user_id=`$user.user_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{if $partners}
	{include file="common_templates/table_tools.tpl" href="#partners"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $partners}
	{capture name="approve_selected"}
		{include file="addons/affiliate/views/partners/components/reason_container.tpl" name="action_reason_approved"}
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_text=$lang.proceed but_name="dispatch[partners.m_approve]" cancel_action="close" but_meta="cm-process-items"}
		</div>
	{/capture}
	
	{capture name="decline_selected"}
		{include file="addons/affiliate/views/partners/components/reason_container.tpl" name="action_reason_declined"}
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_text=$lang.proceed but_name="dispatch[partners.m_decline]" cancel_action="close" but_meta="cm-process-items"}
		</div>
	{/capture}

	<div class="buttons-container buttons-bg">
		{capture name="tools_list"}
		<ul>
			<li><a onclick="jQuery.show_picker('approve_selected', '', '.object-container');">{$lang.approve_selected}</a></li>
			<li><a onclick="jQuery.show_picker('decline_selected', '', '.object-container');">{$lang.decline_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[partners.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}

		{include file="common_templates/popupbox.tpl" id="approve_selected" text=$lang.approve_selected content=$smarty.capture.approve_selected link_text=$lang.approve_selected}
		{include file="common_templates/popupbox.tpl" id="decline_selected" text=$lang.decline_selected content=$smarty.capture.decline_selected link_text=$lang.decline_selected}
	</div>
{/if}
</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.affiliates content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
