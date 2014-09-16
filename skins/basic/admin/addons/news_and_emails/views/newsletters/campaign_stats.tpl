{* $Id: campaign_stats.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

<div id="content_campaign_stats_{$campaign.campaign_id}">

<div class="object-container">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>{$lang.title}</th>
	<th>{$lang.clicks}</th>
</tr>
{foreach from=$campaign_stats item="newsletter"}
<tr>
	<td>{$newsletter.newsletter}</td>
	<td>{$newsletter.clicks|default:0}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="2">
		<p>{$lang.no_data}</p>
	</td>
</tr>
{/foreach}
</table>

</div>

<!--content_campaign_stats_{$campaign.campaign_id}--></div>