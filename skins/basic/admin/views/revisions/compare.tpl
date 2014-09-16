{* $Id: compare.tpl 7053 2009-03-16 14:30:28Z zeke $ *}

{capture name="mainbox"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">{$lang.field}</th>
	<th width="50%">
		{$lang.revision} {$revision1.revision} ({$revision1.change_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"} {$lang.by} {$revision1.firstname}{if $revision1.firstname && $revision1.lastname}&nbsp;{/if}{$revision1.lastname})</th>
	<th width="50%">
		{$lang.revision} {$revision2.revision} ({$revision2.change_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"} {$lang.by} {$revision2.firstname}{if $revision2.firstname && $revision2.lastname}&nbsp;{/if}{$revision2.lastname})</th>
</tr>
{foreach from=$changes item="data"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="right nowrap">
	{if $data.field_description}{$data.field_description}{else}{$data.table}.{$data.field}{/if}:</td>
	<td width="50%">
		<span class="diff-old">{$data.value1|unescape}</span>&nbsp;</td>
	<td width="50%">
		<span class="diff-new">{$data.value2|unescape}</span>&nbsp;</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="3">
		<p>{$lang.text_no_changes_found}</p>
	</td>
</tr>
{/foreach}
</table>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.back but_onclick="history.go(-1);" but_role="text"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.changes`: `$description`" content=$smarty.capture.mainbox}