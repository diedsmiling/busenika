{* $Id: verify.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

<div class="clear">
	{include file="addons/gift_certificates/views/gift_certificates/components/gift_certificates_verify.tpl"}
</div>
{if $verify_data}
{** /Gift certificates section **}
{capture name="tabsbox"}
<div id="content_detailed" class="hidden">
	<h5 class="info-field-title">{$lang.gift_certificate_info}</h5>
	<div class="info-field-body">
	<table cellpadding="0" cellspacing="4" border="0">
	<tr>
		<td><strong>{$lang.gift_cert_code}:</strong></td>
		<td><strong>{$verify_data.gift_cert_code}</strong></td>
	</tr>
	<tr>
		<td><strong>{$lang.status}:</strong></td>
		<td>{include file="common_templates/status.tpl" status=$verify_data.status display="view" status_type=$smarty.const.STATUSES_GIFT_CERTIFICATE}</td>
	</tr>
	<tr>
		<td><strong>{$lang.gift_cert_to}:</strong></td>
		<td>{$verify_data.recipient}</td>
	</tr>
	<tr>
		<td><strong>{$lang.gift_cert_from}:</strong></td>
		<td>{$verify_data.sender}</td>
	</tr>
	<tr>
		<td><strong>{$lang.amount}:</strong></td>
		<td width="250">{include file="common_templates/price.tpl" value=$verify_data.amount}</td>
	</tr>
	</table>
	</div>
	{if $addons.gift_certificates.free_products_allow == "Y" && $verify_data.products}
	<h5 class="info-field-title">{$lang.free_products}</h5>
	<div class="info-field-body product-options">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="100%">{$lang.product}</th>
			<th>{$lang.quantity}</th>
		</tr>
		{foreach from=$verify_data.products item="product_item"}
		<tr {cycle values=",class=\"table-row\""}>
			<td>
			<a href="{"products.view?product_id=`$product_item.product_id`"|fn_url}">{$product_item.product|default:$lang.deleted_product}</a>
			<p>{include file="common_templates/options_info.tpl" product_options=$product_item.product_options_value}</p>
			</td>
			<td class="center">{$product_item.amount}</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="2"><p class="no-items">{$lang.no_items}</p></td>
		</tr>
		{/foreach}
		<tr class="table-footer">
			<td colspan="2">&nbsp;</td>
		</tr>
		</table>
	</div>
	{/if}

</div>
<div id="content_log" class="hidden">
	{include file="common_templates/pagination.tpl"}

	{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
	{if $sort_order == "asc"}
		{assign var="sort_sign" value="&nbsp;&nbsp;&#8595;"}
	{else}
		{assign var="sort_sign" value="&nbsp;&nbsp;&#8593;"}
	{/if}
	{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
		{assign var="ajax_class" value="cm-ajax"}

	{/if}

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th width="15%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a>{if $sort_by == "timestamp"}{$sort_sign}{/if}</th>
		<th width="15%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=name&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.customer}</a>{if $sort_by == "name"}{$sort_sign}{/if}</th>
		<th width="30%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.balance}</a>{if $sort_by == "amount"}{$sort_sign}{/if}</th>
		<th width="30%"><a class="{$ajax_class}" href="{"`$c_url`&amp;sort_by=debit&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.gift_cert_debit}</a>{if $sort_by == "debit"}{$sort_sign}{/if}</th>
	</tr>
	{foreach from=$log item="l"}
	<tr {cycle values=",class=\"table-row\""}>
		<td>{$l.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
		<td>
		<ul>
			<li>
			{if $l.user_id}
				{$l.firstname} {$l.lastname}
			{elseif $l.order_id}
				{$l.order_firstname} {$l.order_lastname}
			{/if}
			</li>
			<li>
			{if $l.user_id}
				<a href="mailto:{$l.email|escape:url}">{$l.email}</a>
			{else}
				<a href="mailto:{$l.order_email|escape:url}">{$l.order_email}</a>
			{/if}
			</li>
		</ul>
		</td>
		<td>
			{if $addons.gift_certificates.free_products_allow == "Y"}<strong>{$lang.amount}:</strong>&nbsp;{/if}{include file="common_templates/price.tpl" value=$l.amount}
			{if $l.products && $addons.gift_certificates.free_products_allow == "Y"}
				<strong>{$lang.free_products}:</strong>
				<ul class="arrows-list">
				{foreach from=$l.products item="product_item"}
				<li>{$product_item.amount} - {if $product_item.product}<a href="{"products.view?product_id=`$product_item.product_id`"|fn_url}">{$product_item.product|truncate:30:"...":true}</a>{else}{$lang.deleted_product}{/if}</li>
				{/foreach}
				</ul>
			{/if}
		</td>
		<td>
			{if $addons.gift_certificates.free_products_allow == "Y"}<strong>{$lang.amount}:</strong>&nbsp;{/if}{include file="common_templates/price.tpl" value=$l.debit}
			{if $l.debit_products && $addons.gift_certificates.free_products_allow == "Y"}
				<strong>{$lang.free_products}:</strong>
				<ul class="bullets-list">
				{foreach from=$l.debit_products item="product_item"}
				<li>{$product_item.amount} - {if $product_item.product}<a href="{"products.view?product_id=`$product_item.product_id`"|fn_url}">{$product_item.product|truncate:30:"...":true}</a>{else}{$lang.deleted_product}{/if}</li>
				{/foreach}
				</ul>
			{/if}
		</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="5"><p class="no-items">{$lang.no_items}</p></td>
		</tr>
		{/foreach}
		<tr class="table-footer">
			<td colspan="5">&nbsp;</td>
		</tr>
		</table>
		{include file="common_templates/pagination.tpl"}
	</div>
	{/capture}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}
{else}
	<div class="center strong">{$lang.error_gift_cert_code}</div>
{/if}
{** /Gift certificates section **}

{capture name="mainbox_title"}{$lang.gift_certificate_verification}{/capture}