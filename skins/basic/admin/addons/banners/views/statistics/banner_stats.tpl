{* $Id: campaign_stats.tpl 6926 2009-02-19 15:17:24Z zeke $ *}

<div id="content_banner_stats_{$smarty.request.banner_id}">

<div class="object-container">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.date}</th>
	<th>{$lang.clicks}</th>
	<th>{$lang.views}</th>
	<th>{$lang.conversion}</th>
</tr>
{foreach from=$stat item="s" key="t"}
<tr>
	<td>
	{if $period == "year"}
		{$t|date_format:"%Y"}
	{elseif $period == "month"}
		{$t|date_format:"%Y, %B"}
	{elseif $period == "day"}
		{$t|date_format:"%B, %d"}
	{elseif $period == "hour"}
		{$t|date_format:"%A, %H:%M"}
	{/if}
	</td>
	<td>{$s.C.number|default:"0"}</td>
	<td>{$s.V.number|default:"0"}</td>
	<td>{$s.conversion|default:"0"}%</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4">
		<p>{$lang.no_data}</p>
	</td>
</tr>
{/foreach}
</table>

</div>

<!--content_banner_stats_{$smarty.request.banner_id}--></div>