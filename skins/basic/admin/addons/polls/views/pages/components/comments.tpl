{* $Id: comments.tpl 6966 2009-03-04 06:42:39Z angel $ *}

{if $smarty.request.answer_id}
	{assign var="suffix" value="_a_`$smarty.request.answer_id`"}
{elseif $smarty.request.item_id}
	{assign var="suffix" value="_q_`$smarty.request.item_id`"}
{/if}

<div id="content_poll_statistics_comments{$suffix}">
{if $comments}
<div class="object-container">
{include file="common_templates/pagination.tpl" div_id="pagination_comments_`$suffix`"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.date}</th>
	<th>{$lang.comment}</th>
	<th width="100%">&nbsp;</th>
</tr>
{foreach from=$comments item="comment"}
<tr {cycle values="class=\"table-row\","}>
   	<td class="nowrap">{$comment.time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
   	<td class="nowrap" width="350">{$comment.comment}</td>
   	<td width="100%">&nbsp;</td>
</tr>
{/foreach}
</table>
{include file="common_templates/pagination.tpl" div_id="pagination_comments_`$suffix`"}
</div>
{/if}
<!--content_poll_statistics_comments{$suffix}--></div>

