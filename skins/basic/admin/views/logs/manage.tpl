{* $Id: manage.tpl 9475 2010-05-17 15:51:21Z imac $ *}

{capture name="mainbox"}

{include file="views/logs/components/logs_search_form.tpl"}

{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>&nbsp;</th>
	<th>{$lang.time}</th>
	<th>{$lang.user}</th>
	<th>{$lang.type}</th>
	<th width="100%">{$lang.content}</th>
</tr>
{foreach from=$logs item="log"}
{assign var="_type" value="log_type_`$log.type`"}
{assign var="_action" value="log_action_`$log.action`"}
<tr {cycle values="class=\"table-row\","} valign="top">
	<td>
		<img src="{$images_dir}/icons/notification_icon_{$log.event_type|lower}.gif" width="19" height="19" alt="" /></td>
	<td class="nowrap">
		{$log.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="nowrap">
		{if $log.user_id}<a href="{"profiles.update?user_id=`$log.user_id`"|fn_url}">{$log.lastname}{if $log.firstname || $log.lastname}&nbsp;{/if}{$log.firstname}</a>{else}&nbsp;-&nbsp;{/if}</td>
	<td class="nowrap">
		{$lang.$_type}{if $log.action}&nbsp;({$lang.$_action}){/if}</td>
	<td width="100%">
		{foreach from=$log.content key="k" item="v"}
		{$lang.$k}:&nbsp;{$v}<br />
		{foreachelse}
		&nbsp;-&nbsp;
		{/foreach}

		{if $log.backtrace}
		<p><a onclick="$('#backtrace_{$log.log_id}').toggle(); return false;" class="underlined"><strong>{$lang.backtrace}&#155;&#155;</strong></a></p>
		<div id="backtrace_{$log.log_id}" class="notice-box hidden">
		{foreach from=$log.backtrace item="v"}
		{$v.file}{if $v.function}&nbsp;({$v.function}){/if}:&nbsp;{$v.line}<br />
		{/foreach}
		</div>
		{else}
			&nbsp;
		{/if}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.logs content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}