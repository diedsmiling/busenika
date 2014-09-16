{* $Id: requests.tpl 7165 2009-03-31 12:38:30Z angel $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="usergroup_requests_form">

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
	<tr>
		<th width="1%" class="center">
			<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
		<th width="34%"><a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.user}</a></th>
		<th width="35%"><a class="{$ajax_class}{if $search.sort_by == "usergroup"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=usergroup&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.usergroup}</a></th>
		<th width="30%">{$lang.status}</th>
	</tr>
	{foreach from=$usergroup_requests item=ug_request}
	<tr {cycle values="class=\"table-row\", "}>
		<td class="center">
			<input type="checkbox" name="link_ids[]" value="{$ug_request.link_id}" class="checkbox cm-item" /></td>
		<td><a href="{"profiles.update?user_id=`$ug_request.user_id`"|fn_url}">{$ug_request.lastname} {$ug_request.firstname}</a></td>
		<td><a href="{"usergroups.manage#group`$ug_request.usergroup_id`"|fn_url}">{$ug_request.usergroup}</a></td>
		<td>
			{include file="common_templates/select_popup.tpl" id=$ug_request.usergroup_id status=$ug_request.status hidden="" items_status="A: `$lang.active`, P: `$lang.pending`, F: `$lang.available`, D: `$lang.declined`" extra="&amp;user_id=`$ug_request.user_id`" update_controller="usergroups" notify=true}
		</td>
	</tr>
	{foreachelse}
	<tr class="no-items">
		<td colspan="4"><p>{$lang.no_items}</p></td>
	</tr>
	{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

{if $usergroup_requests}
<div class="select-field notify-customer">
	<input type="checkbox" name="notify_user" id="notify_user" value="Y" class="checkbox" />
	<label for="notify_user">{$lang.notify_customer}</label>
</div>

<div class="buttons-container buttons-bg">
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a class="cm-process-items cm-confirm" name="dispatch[usergroups.bulk_update_status.decline]" rev="usergroup_requests_form">{$lang.decline_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_text=$lang.approve_selected but_name="dispatch[usergroups.bulk_update_status.approve]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
</div>
{/if}
</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.user_group_requests content=$smarty.capture.mainbox}
