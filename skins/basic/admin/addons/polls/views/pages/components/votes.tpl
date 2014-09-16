{* $Id: votes.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $smarty.request.answer_id}
	{assign var="suffix" value="a_`$smarty.request.answer_id`"}
{elseif $smarty.request.item_id}
	{assign var="suffix" value="q_`$smarty.request.item_id`"}
{elseif $smarty.request.completed == "Y"}
	{assign var="suffix" value="completed"}
{else}
	{assign var="suffix" value="total"}
{/if}

<div id="content_poll_statistics_votes_{$suffix}">
<div class="object-container">

{include file="common_templates/pagination.tpl" div_id="pagination_contents_`$suffix`"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.date}</th>
	<th>{$lang.user}</th>
	<th>{$lang.ip}</th>
	<th>{$lang.completed}</th>
	<th width="100%">&nbsp;</th>
</tr>
{foreach from=$votes item="vote"}
<tr class="cm-row-item">
   	<td class="nowrap">{$vote.time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
   	<td>{if $vote.user_id}{$vote.lastname}{if $vote.lastname && $vote.firstname}&nbsp;{/if}{$vote.firstname}{else}{$lang.anonymous}{/if}</td>
   	<td>{$vote.ip_address}</td>
   	<td>{if $vote.type == "C"}{$lang.yes}{else}{$lang.no}{/if}</td>
   	<td width="100%">{include file="buttons/clone_delete.tpl" href_delete="pages.delete_vote?vote_id=`$vote.vote_id`"}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}

</table>
{include file="common_templates/pagination.tpl" div_id="pagination_contents_`$suffix`"}

</div>
<!--content_poll_statistics_votes_{$suffix}--></div>