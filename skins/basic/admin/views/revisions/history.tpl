{* $Id: history.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{if $smarty.request.from}
	{assign var="from_suffix" value="&amp;from=`$smarty.request.from`"}
{/if}

{capture name="mainbox"}

<form action="{""|fn_url}" method="get" name="select_compare_revisions">
<input type="hidden" name="object" value="{$smarty.request.object}" />
<input type="hidden" name="object_id" value="{$smarty.request.object_id}" />
<input type="hidden" name="from" value="{$smarty.request.from}" />

{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="1%">&nbsp;</th>
	<th width="1%">&nbsp;</th>
	<th class="center">{$lang.revision_short}</th>
	<th width="100%">{$lang.last_action}</th>
	<th class="right">{$lang.workflow}</th>
	<th colspan="3">&nbsp;</th>
</tr>
{foreach from=$revisions item="revision"}
<tr {cycle values="class=\"table-row\", "}>
{if $revision.type == "D"}
	<td class="center" width="2%" colspan="2">
		{$lang.deleted}</td>
{else}
	<td class="center" width="1%">
		{if $revision.type == "U" || $revision.type == "C" || $revision.type == "O" || $revision.type == "R"}<input type="radio" name="revision1" value="{$revision.revision}" class="radio" />{else}&nbsp;{/if}</td>
	<td class="center" width="1%">
		{if $revision.type == "U" || $revision.type == "C" || $revision.type == "O" || $revision.type == "R"}<input type="radio" name="revision2" value="{$revision.revision}" class="radio" />{else}&nbsp;{/if}</td>
{/if}
	<td class="center">
		{$revision.revision}
	</td>
	<td>
		{$revision.change_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"} {if $revision.type == "D"}<strong>{$lang.deleted}</strong>{elseif $revision.type == "C"}{$lang.created}{elseif $revision.type == "R"}{$lang.reverted}{elseif $revision.type == "A"}<strong>{$lang.approved}</strong>{elseif $revision.type == "O"}<strong>{$lang.published}</strong>{elseif $revision.type == "P"}{$lang.processed}{elseif $revision.type == "E" && $revision.note}<a onClick="$('#reason_{$revision.id}').toggle(); return false;">{$lang.declined}</a>{elseif $revision.type == "E"}{$lang.declined}{else}{$lang.edited}{/if} {$lang.by} {$revision.firstname}{if $revision.firstname && $revision.lastname}&nbsp;{/if}{$revision.lastname}
		{if $revision.type == "E" && $revision.note}<div class="decline-text hidden" id="reason_{$revision.id}">{$lang.decline_reason}: {$revision.note|nl2br}</div>{/if}
	</td>
	<td width="100%" class="right">
		<div class="nowrap">
		{if $revision.button2 == "reject"}
			{include file="buttons/button.tpl" but_text=$lang.decline but_href="" but_role="text" but_onclick="$('#decline_`$revision.id`').toggle();" allow_href=false but_meta="text-button"}
		{else}
			&nbsp;
		{/if}
		{if $revision.button1 == "approve"}
			{include file="buttons/button.tpl" but_text=$lang.approve but_meta="cm-confirm" but_href="revisions.approve?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_role="text"}{elseif $revision.button1 == "publish"}{include file="buttons/button.tpl" but_text=$lang.publish but_meta="cm-confirm" but_href="revisions.publish?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_role="text"}
		{elseif $revision.button1 == "process"}
			{include file="buttons/button.tpl" but_text=$lang.process but_meta="cm-confirm" but_href="revisions.process?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_role="text"}
		{/if}
		{if $revision.button2 == "reject"}
			<div class="decline-text hidden" id="decline_{$revision.id}">
			<textarea cols="20" rows="3" name="decline_data[note]" class="input-text decline-text"></textarea><br>
			<div class="decline-text" align="left">{include file="buttons/button.tpl" but_text=$lang.decline but_name="dispatch[revisions.decline]" but_meta="cm-confirm" but_role="submit"}</div></div>
		{/if}
		</div>
	</td>
	<td class="nowrap right">
		{if $revision.button2 == "delete"}
			{include file="buttons/button.tpl" but_text=$lang.delete but_href="revisions.delete?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_meta="cm-confirm" but_role="text"}
		{elseif $revision.button2 == "delete_all"}
			{include file="buttons/button.tpl" but_text=$lang.delete_all_previous but_meta="cm-confirm" but_href="revisions.delete_previous?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_role="text"}
		{else}&nbsp;{/if}
	</td>
	<td class="right">
		{if $revision.button1 == "revert"}
			{include file="buttons/button.tpl" but_text=$lang.revert but_href="revisions.revert?object=`$smarty.request.object`&object_id=`$smarty.request.object_id`&revision=`$revision.revision``$from_suffix`" but_role="text"}
		{else}&nbsp;{/if}
	</td>
	<td class="right">
		{if $revision.button == "edit"}
			{include file="buttons/button.tpl" but_text=$lang.edit but_href=$revision.view_link but_role="tool"}
		{elseif $revision.button == "view"}
			{include file="buttons/button.tpl" but_text=$lang.view but_href=$revision.view_link but_role="tool"}
		{else}&nbsp;{/if}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="8">
		<p>{$lang.text_no_revisions_found}</p>
	</td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

{if $revisions}
<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_text=$lang.compare but_name="dispatch[revisions.compare]" but_role="button_main"}
</div>
{/if}

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.history`: `$description`" content=$smarty.capture.mainbox}
