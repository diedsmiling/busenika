{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="addons/gift_certificates/views/gift_certificates/components/gift_certificates_search_form.tpl"}

<form action="{""|fn_url}" method="post" enctype="multipart/form-data" name="gift_cert_list_form">

{include file="common_templates/pagination.tpl" save_current_url=true}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "gift_cert_code"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=gift_cert_code&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.code}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "sender"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=sender&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.gift_cert_from}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "recipient"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=recipient&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.gift_cert_to}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "send_via"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=send_via&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.type}</a></th>
	<th><a class="{$ajax_class}{if $search.sort_by == "timestamp"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
	<th>&nbsp;&nbsp;{$lang.current_amount}</th>
	<th><a class="{$ajax_class}{if $search.sort_by == "status"} sort-link-{$search.sort_order}{/if}" href="{"`$c_url`&amp;sort_by=status&amp;sort_order=`$search.sort_order`"|fn_url}" rev="pagination_contents">{$lang.status}</a></th>
	<th>&nbsp;</th>
</tr>
{assign var="gift_status_descr" value=$smarty.const.STATUSES_GIFT_CERTIFICATE|fn_get_statuses:true}
{foreach from=$gift_certificates item="gift"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="gift_cert_ids[]" value="{$gift.gift_cert_id}" class="checkbox cm-item" /></td>
	<td><a href="{"gift_certificates.update?gift_cert_id=`$gift.gift_cert_id`"|fn_url}" class="nowrap">{$gift.gift_cert_code}</a></td>
	<td>{$gift.sender}</td>
	<td>{$gift.recipient}</td>
	<td><span class="nowrap">{if $gift.send_via == "P"}{$lang.mail}{else}{$lang.email}</span> ({$gift.email}){/if}</td>
	<td><a href="{"gift_certificates.update?gift_cert_id=`$gift.gift_cert_id`"|fn_url}" class="underlined">{$gift.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>
	<td>{include file="common_templates/price.tpl" value=$gift.debit}</td>
	<td>
		{include file="common_templates/select_popup.tpl" id=$gift.gift_cert_id status=$gift.status items_status=$gift_status_descr update_controller="gift_certificates" notify=true}
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"gift_certificates.delete?gift_cert_id=`$gift.gift_cert_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$gift.gift_cert_id tools_list=$smarty.capture.tools_items href="gift_certificates.update?gift_cert_id=`$gift.gift_cert_id`"}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="8"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{if  $gift_certificates}
	{include file="common_templates/table_tools.tpl" href="#gift_certificates"}
{/if}

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if  $gift_certificates}
	<div class="float-left">
		{include file="buttons/delete_selected.tpl" but_name="dispatch[gift_certificates.delete]" but_meta="cm-process-items cm-confirm" but_role="button_main"}
	</div>
	{/if}
	
	<div class="float-right">
		{include file="common_templates/tools.tpl" tool_href="gift_certificates.add" prefix="bottom" hide_tools=true link_text=$lang.add_gift_certificate}
	</div>
</div>

{capture name="tools"}
	{include file="common_templates/tools.tpl" tool_href="gift_certificates.add" prefix="top" hide_tools=true link_text=$lang.add_gift_certificate}
{/capture}

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.gift_certificates content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools}