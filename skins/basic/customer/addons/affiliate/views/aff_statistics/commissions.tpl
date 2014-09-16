{* $Id:	aff_statistics.tpl	0 2006-07-28 19:49:30Z	seva $	*}

{include file="addons/affiliate/views/aff_statistics/components/stat_search_form.tpl"}

<p><a onclick="$('#general_statistics').toggle(); return false;"><strong>{$lang.general_statistics} &#187;</strong></a></p>

<div id="general_statistics" class="hidden">

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="40%">{$lang.action}</th>
	<th class="right" width="20%">count</th>
	<th class="right" width="20%">sum</th>
	<th class="right" width="20%">avg</th>
</tr>
{if $general_stats}
{foreach from=$payout_types key="payout_id" item="a"}
	{assign var="payout" value=$general_stats.$payout_id}
	{assign var="payout_var" value=$a.title}
	{if $payout.count}
	<tr {cycle values=" ,class=\"table-row\""}>
		<td><strong>{$lang.$payout_var}</strong></td>
		<td class="right">{$payout.count|default:"0"}</td>
		<td class="right">{include file="common_templates/price.tpl" value=$payout.sum|round:2}</td>
		<td class="right">{include file="common_templates/price.tpl" value=$payout.avg|round:2}</td>
	</tr>
	{/if}
{/foreach}
{if $general_stats.total}
	{assign var="payout" value=$general_stats.total}
<tr>
	<td><strong>{$lang.total}</strong></td>
	<td class="right"><strong>{$payout.count|default:"0"}</strong></td>
	<td class="right"><strong>{include file="common_templates/price.tpl" value=$payout.sum|round:2}</strong></td>
	<td class="right"><strong>{include file="common_templates/price.tpl" value=$payout.avg|round:2}</strong></td>
</tr>
{/if}
{else}
<tr>
	<td colspan="4"><p class="no-items">{$lang.no_data_found}</p></td>
</tr>
{/if}
<tr class="table-footer">
	<td colspan="4">&nbsp;</td>
</tr>
</table>

{if $additional_stats}
<table cellpadding="2" cellspacing="1" border="0" class="margin-top">
{foreach from=$additional_stats key="a_stats_name" item="a_stats_value"}
<tr>
	<td><strong>{$lang.$a_stats_name}</strong>:</td>
	<td>{$a_stats_value}</td>
</tr>
{/foreach}
</table>
{/if}

</div>

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $sort_order == "asc"}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
{else}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
{/if}
{if $settings.DHTML.customer_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=action&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.action}</a>{if $sort_by == "action"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=date&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.date}</a>{if $sort_by == "date"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=cost&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.cost}</a>{if $sort_by == "cost"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=customer&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.customer}</a>{if $sort_by == "customer"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=banner&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.banner}</a>{if $sort_by == "banner"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{$url_prefix}{$c_url}&amp;sort_by=status&amp;sort_order={$sort_order}" rev="pagination_contents">{$lang.status}</a>{if $sort_by == "status"}{$sort_sign}{/if}</th>
	<th width="1%" class="center">
		<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-commissions" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-commissions" /></th>
</tr>
{foreach from=$list_stats item="row_stats" name="commissions"}
{cycle values=",table-row" assign="row_class_name"}
{include file="addons/affiliate/views/aff_statistics/components/additional_data.tpl" data=$row_stats.data assign="additional_data"}
<tr class="{$row_class_name}">
	<td>
		<strong>{$row_stats.title}{$action_title}</strong>
	</td>
	<td>{$row_stats.date|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$row_stats.amount|round:2}{$action_amount}</td>
	<td>
		{$row_stats.customer_lastname} {$row_stats.customer_firstname}
		{if $row_stats.ip}<p><em>({$row_stats.ip})</em></p>{/if}</td>
	<td>{if $row_stats.banner}<a href="{"banners_manager.update?banner_type=`$row_stats.banner_type`&amp;banner_id=`$row_stats.banner_id`"|fn_url}">{$row_stats.banner}</a>{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
	<td>{if $row_stats.payout_id}{$lang.paidup}{elseif $row_stats.approved=="Y"}{$lang.approved}{else}&nbsp;&nbsp;---&nbsp;{/if}</td>
	<td class="center nowrap">
		{if $row_stats.extra_data || $additional_data|trim}
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_commission_{$smarty.foreach.commissions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-commissions" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_commission_{$smarty.foreach.commissions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-commissions" /><a id="sw_commission_{$smarty.foreach.commissions.iteration}" class="cm-combination-commissions">{$lang.extra}</a>
		{else}
		&nbsp;
		{/if}
	</td>
</tr>
{if $row_stats.extra_data || $additional_data|trim}
<tr id="commission_{$smarty.foreach.commissions.iteration}" class="{$row_class_name} hidden">
	<td colspan="7">
	<div class="box">
		{if $row_stats.extra_data}
		<table cellpadding="0" cellspacing="0" width="100%" class="table sortable">
			<tr>
				<th width="25%">{$lang.action}</th>
				<th width="10%">{$lang.cost}</th>
				<th>{$lang.affiliate}</th>
			</tr>
		{foreach from=$row_stats.extra_data item="r_action" name="related_action"}
			<tr>
				<td>{if $r_action.action_id == $row_stats.action_id}<strong>{/if}{$r_action.title}{if $r_action.tier} ({$r_action.tier} {$lang.tier_account}){/if}{if $r_action.action_id == $row_stats.action_id}</strong>{/if}</td>
				<td>{include file="common_templates/price.tpl" value=$r_action.amount|round:2}</td>
				<td>{if $r_action.firstname || $r_action.lastname} {$r_action.firstname} {$r_action.lastname}{/if}</td>
			<tr>
		{/foreach}
		</table>
		{/if}
		{if $additional_data|trim}
			{$lang.additional_data}: {$additional_data}
		{/if}
	</div>
	</td>
</tr>
{/if}
{foreachelse}
<tr>
	<td colspan="7"><p class="no-items">{$lang.no_data_found}</p></td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="7">&nbsp;</td>
</tr>
</table>
{include file="common_templates/pagination.tpl"}

{capture name="mainbox_title"}{$lang.commissions}{/capture}