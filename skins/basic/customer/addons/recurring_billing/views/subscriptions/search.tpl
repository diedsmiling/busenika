{* $Id:	aff_statistics.tpl	0 2006-07-28 19:49:30Z	seva $	*}

{include file="addons/recurring_billing/views/subscriptions/components/subscriptions_search_form.tpl"}

<form action="{""|fn_url}" method="post" name="subscriptions_list_form">

{include file="common_templates/pagination.tpl"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
{else}
{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
{/if}
{if $settings.DHTML.customer_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th><a class="{$ajax_class}" href="{"`$url_prefix``$c_url`&amp;sort_by=subscription_id&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.id}</a>{if $search.sort_by == "subscription_id"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{"`$url_prefix``$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a>{if $search.sort_by == "date"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{"`$url_prefix``$c_url`&amp;sort_by=price&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.price}</a>{if $search.sort_by == "price"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{"`$url_prefix``$c_url`&amp;sort_by=duration&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.rb_duration_short}</a>{if $search.sort_by == "duration"}{$sort_sign}{/if}</th>
	<th><a class="{$ajax_class}" href="{"`$url_prefix``$c_url`&amp;sort_by=last_paid&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.last_order}</a>{if $search.sort_by == "last_paid"}{$sort_sign}{/if}</th>
	<th>&nbsp;</th>
	<th width="1%" class="right">
		<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-subscriptions" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-subscriptions" /></th>
</tr>
{foreach from=$subscriptions item="sub" name="subscriptions"}
{cycle values=",table-row" assign="row_class_name"}
{include file="addons/recurring_billing/views/subscriptions/components/additional_data.tpl" data=$sub.product_ids assign="additional_data"}
<tr class="{$row_class_name}">
	<td><a href="{"subscriptions.view?subscription_id=`$sub.subscription_id`"|fn_url}">&nbsp;<strong>#{$sub.subscription_id}</strong>&nbsp;</a></td>
	<td>{$sub.timestamp|date_format:$settings.Appearance.date_format}</td>
	<td class="right">{include file="common_templates/price.tpl" value=$sub.price}</td>
	<td>
		{if $sub.allow_change_duration == "Y"}
			<input type="text" name="update_duration[{$sub.subscription_id}]" value="{$sub.duration}" size="6" class="input-text-short" />
		{else}
			{$sub.duration}
		{/if}
	</td>
	<td>{$sub.last_timestamp|date_format:$settings.Appearance.date_format}</td>
	<td>
		{if $sub.allow_unsubscribe == "Y"}
			{assign var="need_update" value=true}
			<a href="{"subscriptions.unsubscribe?subscription_id=`$sub.subscription_id`"|fn_url}">{$lang.unsubscribe}</a>
		{else}
			&nbsp;
		{/if}
	</td>
	<td class="right nowrap">
		{if $additional_data|trim}
			<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_products_{$smarty.foreach.subscriptions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-subscriptions" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_products_{$smarty.foreach.subscriptions.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-subscriptions" /><a id="sw_products_{$smarty.foreach.subscriptions.iteration}" class="cm-combination-subscriptions">{$lang.extra}</a>
		{else}
		&nbsp;
		{/if}
	</td>
</tr>
{if $additional_data|trim}
<tr id="products_{$smarty.foreach.subscriptions.iteration}" class="{$row_class_name} hidden">
	<td colspan="7">
	<div class="box">
		{if $additional_data|trim}
			{$lang.products}: {$additional_data}
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

{if $need_update}
	<div class="buttons-container">
	{include file="buttons/button.tpl" but_name="dispatch[subscriptions.update]" but_text=$lang.update but_role="action"}
	</div>
{/if}
</form>

{capture name="mainbox_title"}{$lang.rb_subscriptions}{/capture}