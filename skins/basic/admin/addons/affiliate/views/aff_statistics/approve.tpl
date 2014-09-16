{* $Id: approve.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="addons/affiliate/views/aff_statistics/components/stat_search_form.tpl" dispatch="aff_statistics.approve"}

{include file="addons/affiliate/views/aff_statistics/components/general_statistics.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{*assign var="c_url_esc" value=$config.current_url|escape:url*}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<form action="{""|fn_url}" method="post" name="commissions_approve_form">

<div id="aff_stats_list">
{include file="common_templates/pagination.tpl"}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "action"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=action&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.action}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "date"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "cost"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=cost&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.cost}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "customer"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=customer&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "partner"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=partner&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.affiliate}</a></th>
	<th width="10%"><a class="{$ajax_class}{if $search.sort_by == "banner"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=banner&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.banner}</a></th>
	<th width="15%"><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th width="1%" class="center">
		<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-commissions" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-commissions" /></th>
	<th>&nbsp;</th>
</tr>
{if $affiliate_commissions}
	{foreach from=$affiliate_commissions item="row_stats" name="commissions"}
	<tbody class="hover">
	{include file="addons/affiliate/views/payouts/components/additional_data.tpl" data=$row_stats.data assign="additional_data"}
	<tr>
		<td>
			<input type="checkbox" name="action_ids[]" value="{$row_stats.action_id}" {if $row_stats.payout_id}disabled="disabled"{/if} class="checkbox cm-item" /></td>
		<td>
			<strong>{$row_stats.title}{$action_title}</strong>

			{if $row_stats.related_actions}
			<p>{$lang.related_actions}:</p>
			{foreach from=$row_stats.related_actions item="r_action"}
			<p>{$r_action.title}, {include file="common_templates/price.tpl" value=$r_action.amount|round:2}, {if $r_action.payout_id}{$lang.paidup}{elseif $r_action.approved == "Y"}{$lang.approved}{else}&nbsp;&nbsp;---&nbsp;{/if}</p>
			{/foreach}
			{/if}
		</td>
		<td>{$row_stats.date|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
		<td class="right">{include file="common_templates/price.tpl" value=$row_stats.amount|round:2}{$action_amount}</td>
		<td>
			{if $row_stats.customer_firstname || $row_stats.customer_lastname}
				<a href="{"profiles.update?user_id=`$row_stats.customer_id`"|fn_url}">{$row_stats.customer_firstname} {$row_stats.customer_lastname}</a>
			{/if}&nbsp;{if $row_stats.ip}<em>({$row_stats.ip})</em>{/if}
		</td>
		<td>{if $row_stats.partner_firstname || $row_stats.partner_lastname}<a href="{"partners.update?user_id=`$row_stats.partner_id`"|fn_url}">{$row_stats.partner_firstname} {$row_stats.partner_lastname}</a>{else}&nbsp;&nbsp;---&nbsp;{/if}
		{if $row_stats.plan}<em>(<a href="{"affiliate_plans.update?plan_id=`$row_stats.plan_id`"|fn_url}">{$row_stats.plan}</a>)</em>{/if}
		{$action_partner}</td>
		<td>{if $row_stats.banner}
			<a href="{"banners_manager.update?banner_id=`$row_stats.banner_id`"|fn_url}">{$row_stats.banner}</a>
			{else}&nbsp;&nbsp;---&nbsp;{/if}
		</td>
		<td>
			{if $row_stats.payout_id}{$lang.paidup}{elseif $row_stats.approved == "Y"}{$lang.approved}{else}&nbsp;&nbsp;---&nbsp;{/if}
		</td>
		<td class="center nowrap">
		{if $row_stats.extra_data || $additional_data|trim}
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_commission_{$smarty.foreach.commissions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-commissions" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_commission_{$smarty.foreach.commissions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-commissions" /><a id="sw_commission_{$smarty.foreach.commissions.iteration}" class="cm-combination-commissions">{$lang.extra}</a>
		{else}
		&nbsp;
		{/if}
		</td>
		<td class="nowrap">
			{capture name="tools_items"}
			{if $row_stats.payout_id}
			<li><span class="undeleted-element">{$lang.delete}</span></li>
			{else}
			<li><a class="cm-confirm" href="{"aff_statistics.delete?action_id=`$row_stats.action_id`"|fn_url}">{$lang.delete}</a></li>
			{/if}
			{/capture}
			{include file="common_templates/table_tools_list.tpl" prefix=$row_stats.action_id tools_list=$smarty.capture.tools_items}
		</td>
	</tr>
	{if $row_stats.extra_data || $additional_data|trim}
	<tr id="commission_{$smarty.foreach.commissions.iteration}" class="hidden">
		<td></td>
		<td colspan="8">
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
					<td>{if $r_action.firstname || $r_action.lastname} <a href="{"profiles.update?user_id=`$r_action.partner_id`"|fn_url}">{$r_action.firstname} {$r_action.lastname}</a>{/if}</td>
				<tr>
			{/foreach}
			</table>
			{/if}
			{if $additional_data|trim}
				{$lang.additional_data}: {$additional_data}
			{/if}
		</td>
		<td></td>
	</tr>
	{/if}
	</tbody>
	{/foreach}
{else}
<tr class="no-items">
	<td colspan="9"><p>{$lang.no_data}</p></td>
</tr>
{/if}
</table>

{if $affiliate_commissions}
	{include file="common_templates/table_tools.tpl" href="#commissions_approve"}
{/if}

{include file="common_templates/pagination.tpl"}

{if $affiliate_commissions}
	<div class="buttons-container buttons-bg">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[aff_statistics.m_disapprove]" class="cm-process-items" rev="commissions_approve_form">{$lang.disapprove_commissions}</a></li>
			<li><a name="dispatch[aff_statistics.m_delete]" class="cm-process-items cm-confirm" rev="commissions_approve_form">{$lang.delete_commissions}</a></li>
		</ul>
		{/capture}
		{include file="buttons/button.tpl" but_meta="cm-process-items" but_text=$lang.approve_commissions but_name="dispatch[aff_statistics.m_approve]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
{/if}
<!--aff_stats_list--></div>
</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.approve_commissions content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}