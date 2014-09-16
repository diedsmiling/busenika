{* $Id: userlog.tpl 10028 2010-07-09 11:17:28Z 2tl $ *}

{script src="js/picker.js"}

{script src="js/tabs.js"}

{capture name="mainbox"}

{** userlog description section **}
{foreach from=$users item=user}

<div class="form-field">
	<label>{$lang.customer}:</label>
	<a href="{"profiles.update?user_id=`$user.user_id`"|fn_url}">{$user.firstname} {$user.lastname}</a>
</div>

<div class="form-field">
	<label>{$lang.points}:</label>
	{if $user.points}{$user.points|unserialize}{else}0{/if}
</div>
{/foreach}

{include file="common_templates/subheader.tpl" title=$lang.log}
{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}
<form action="{""|fn_url}" method="post" name="userlog_form" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="user_id" value="{$smarty.request.user_id}" />

{include file="common_templates/pagination.tpl" save_current_url=true}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="15%"><a class="{$ajax_class}{if $sort_by == "timestamp"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $sort_by == "amount"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.points}</a></th>
	<th width="75%">&nbsp;&nbsp;{$lang.reason}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$userlog item="ul"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="change_ids[]" value="{$ul.change_id}" class="checkbox cm-item" /></td>
	<td>{$ul.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td>{$ul.amount}</td>
	<td>
		{if $ul.action == $smarty.const.CHANGE_DUE_ORDER}
			{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:true:true:true}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{assign var="order_exist" value=$reason.order_id|fn_get_order_name}
			{$lang.order}&nbsp;{if $order_exist}<a href="{"orders.details?order_id=`$reason.order_id`"|fn_url}" class="underlined">{/if}<strong>#{$reason.order_id}</strong>{if $order_exist}</a>{/if}:&nbsp;{$statuses[$reason.from]}&nbsp;&#8212;&#8250;&nbsp;{$statuses[$reason.to]}{if $reason.text}&nbsp;({$reason.text|fn_get_lang_var}){/if}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_USE}
			{assign var="order_exist" value=$ul.reason|fn_get_order_name}
			{$lang.text_points_used_in_order}: {if $order_exist}<a href="{"orders.details?order_id=`$ul.reason`"|fn_url}">{/if}<strong>#{$ul.reason}</strong>{if $order_exist}</a>{/if}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_ORDER_DELETE}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{$lang.order} <strong>#{$reason.order_id}</strong>: {$lang.deleted}
		{elseif $ul.action == $smarty.const.CHANGE_DUE_ORDER_PLACE}
			{assign var="reason" value=$ul.reason|unescape|unserialize}
			{assign var="order_exist" value=$reason.order_id|fn_get_order_name}
			{$lang.order} {if $order_exist}<a href="{"orders.details?order_id=`$reason.order_id`"|fn_url}" class="underlined">{/if}<strong>#{$reason.order_id}</strong>{if $order_exist}</a>{/if}: {$lang.placed}
		{else}
			{hook name="reward_points:userlog"}
			&nbsp;{$ul.reason}
			{/hook}
		{/if}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"reward_points.do_userlog_delete?user_id=`$smarty.request.user_id`&amp;change_id=`$ul.change_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$ul.change_id tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>

{if $userlog}
	{include file="common_templates/table_tools.tpl" href="#userlog"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $userlog}
	<div class="buttons-container buttons-bg">
		<div class="float-left">
			{capture name="tools_list"}
			<ul>
				<li><a name="dispatch[reward_points.do_userlog_delete]" class="cm-process-items cm-confirm" rev="userlog_form">{$lang.delete_selected}</a></li>
			</ul>
			{/capture}
			{include file="buttons/button.tpl" but_text="`$lang.cleanup_log`" but_name="dispatch[reward_points.do_cleanup_logs]" but_role="button_main"}
			{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
		</div>
		
		<div class="float-right">
			{include file="common_templates/popupbox.tpl" id="change_points" text=$lang.change_points link_text=$lang.add_subtract_points act="general"}
		</div>
	</div>
{/if}

</form>
{** / userlog description section **}

{** Change points section **}

{capture name="tools"}
{capture name="add_new_picker"}

<form action="{""|fn_url}" method="post" name="reward_points_form" enctype="multipart/form-data" class="cm-form-highlight">
<input type="hidden" name="user_id" value="{$smarty.request.user_id}" />
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_general" class="cm-js cm-active"><a>{$lang.general}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="content_tab_general">
	<fieldset>
		<div class="form-field">
			<label>{$lang.action}:</label>
			<div class="select-field float-left nowrap">
				<input type="radio" name="reason[action]" id="reason_action_A" value="A" checked="checked" class="radio" />
				<label for="reason_action_A">{$lang.add}</label>
		
				<input type="radio" name="reason[action]" id="reason_action_S" value="S" class="radio" />
				<label for="reason_action_S">{$lang.subtract}</label>
			</div>
		</div>
		
		<div class="form-field">
			<label for="reason_amount" class="cm-required">{$lang.value}:</label>
			<input type="text" value="" name="reason[amount]" id="reason_amount" class="input-text" size="5" />
		</div>
		
		<div class="form-field">
			<label for="reason_reason">{$lang.reason}:</label>
			<textarea name="reason[reason]" id="reason_reason" cols="55" rows="8" class="input-textarea-long"></textarea>
		</div>
		
		<div class="form-field">
			<label for="notify_user">{$lang.notify_customer}:</label>
			<input type="checkbox" name="notify_user" value="Y" checked="checked" id="notify_user" class="checkbox" />
		</div>
	</fieldset>
	</div>
</div>
		
<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[reward_points.do_change_points]" cancel_action="close" but_text=$lang.change}
</div>

</form>

{/capture}
{include file="common_templates/popupbox.tpl" id="change_points" text=$lang.change_points content=$smarty.capture.add_new_picker link_text=$lang.add_subtract_points act="general"}
{/capture}
{** /Change points section **}
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.reward_points_log content=$smarty.capture.mainbox tools=$smarty.capture.tools}