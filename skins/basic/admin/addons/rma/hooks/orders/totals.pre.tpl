{* $Id: totals.pre.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $order_info.returned_products}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th width="5%">{$lang.sku}</th>
		<th>{$lang.returned_product}</th>
		<th width="5%">{$lang.amount}</th>
		<th width="7%" class="rigth">{$lang.subtotal}</th>
	</tr>
	{foreach from=$order_info.returned_products item="oi"}
	<tr {cycle values="class=\"table-row\", "} valign="top">
		<td>{$oi.product_code}</td>
		<td>
			<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{$oi.product}</a>
			{hook name="orders:returned_product_info"}
			{/hook}
			{if $oi.product_options}<div class="options-info">&nbsp;{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}
			</td>
		<td>{$oi.amount}</td>
		<td class="right"><strong>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.subtotal}{/if}</strong></td>
	</tr>
	{/foreach}
</table>
{/if}