{* $Id: list.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{if $affiliate_plan}
<h2>{$affiliate_plan.name}</h2>

{if $affiliate_plan.description}<p>{$affiliate_plan.description|unescape}</p>{/if}

<div class="form-field">
	<label>{$lang.aff_cookie_expiration}:</label>
	{$affiliate_plan.cookie_expiration|default:0}
</div>

{if $affiliate_plan.payout_types.init_balance.value}
<div class="form-field">
	<label>{$lang.set_initial_balance}:</label>
	{include file="common_templates/price.tpl" value=$affiliate_plan.payout_types.init_balance.value}
</div>
{/if}

{if $affiliate_plan.min_payment}
<div class="form-field">
	<label>{$lang.minimum_commission_payment}:</label>
	{include file="common_templates/price.tpl" value=$affiliate_plan.min_payment}
</div>
{/if}

{if $affiliate_plan.payout_types}
{include file="common_templates/subheader.tpl" title=$lang.commission_rates}

{foreach from=$payout_types key="payout_id" item=payout_data}
{if $payout_data.default=="Y" && $affiliate_plan.payout_types.$payout_id.value}
<div class="form-field">
	{assign var="lang_var" value=$payout_data.title}
	<label>{$lang.$lang_var}:</label>
	{include file="common_templates/modifier.tpl" mod_value=$affiliate_plan.payout_types.$payout_id.value mod_type=$affiliate_plan.payout_types.$payout_id.value_type}
</div>
{/if}
{/foreach}
{/if}

{if $linked_products}
{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}
{include file="common_templates/subheader.tpl" title=$lang.linked_products}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="70%">{$lang.product_name}</th>
	<th width="30%">{$lang.sales_commission}</th>
</tr>
{foreach from=$linked_products item=product}
<tr {cycle values=" ,class=\"table-row\""}>
	<td>
		{include file="common_templates/popupbox.tpl" id="product_`$product.product_id`" link_text=$product.product text=$lang.product href="banner_products.view?product_id=`$product.product_id`"}</td>
	<td>{include file="common_templates/modifier.tpl" mod_value=$product.sale.value mod_type=$product.sale.value_type}</td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="2">&nbsp;</td>
</tr>
</table>
{/if}

{if $linked_categories}
{include file="common_templates/subheader.tpl" title=$lang.linked_categories}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="70%">{$lang.category_name}</th>
	<th width="30%">{$lang.sales_commission}</th>
</tr>
{foreach from=$linked_categories item=category}
<tr {cycle values=" ,class=\"table-row\""}>
	<td>
		<a href="{"categories.view?category_id=`$category.category_id`"|fn_url}" class="manage-root-item" target="_blank">{$category.category}</a></td>
	<td>
		{include file="common_templates/modifier.tpl" mod_value=$category.sale.value mod_type=$category.sale.value_type}</td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="2">&nbsp;</td>
</tr>
</table>
{/if}

{if $coupons}
{include file="common_templates/subheader.tpl" title=$lang.coupons}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="35%">{$lang.coupon_code}</th>
	<th width="15%">{$lang.valid}</th>
	<th width="30%">{$lang.use_coupons_commission}</th>
</tr>
{foreach from=$coupons item=coupon}
<tr {cycle values=" ,class=\"table-row\""}>
	<td>{$coupon.coupon_code}</td>
	<td class="nowrap{if (($coupon.from_date <= $coupon.current_date)&&($coupon.to_date>=$coupon.current_date))} strong{/if}">
		{$coupon.from_date|date_format:"`$settings.Appearance.date_format`"} - {$coupon.to_date|date_format:"`$settings.Appearance.date_format`"}</td>
	<td>
		{include file="common_templates/modifier.tpl" mod_value=$coupon.use_coupon.value mod_type=$coupon.use_coupon.value_type}</td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="3">&nbsp;</td>
</tr>
</table>
{/if}

{if $affiliate_plan.commissions}
{include file="common_templates/subheader.tpl" title=$lang.commissions}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th width="70%">{$lang.multi_tier_affiliates}</th>
	<th width="30%">{$lang.commission}</th>
</tr>
{foreach from=$affiliate_plan.commissions key="com_id" item="commission"}
<tr {cycle values=" ,class=\"table-row\""}>
	<td>
		{$lang.level} {$com_id+1}</td>
	<td>
		{include file="common_templates/modifier.tpl" mod_value=$commission mod_type="P"}</td>
</tr>
{/foreach}
<tr class="table-footer">
	<td colspan="2">&nbsp;</td>
</tr>
</table>
{/if}
{else}
<p>{$lang.text_no_affiliate_assigned}.</p>
{/if}
<div class="buttons-container">
<strong>{$lang.link_new_affiliate}</strong>: <a href="{"profiles.add?aff_id=`$auth.user_id`"|fn_url:'C':'http'}" onclick="return false;">{"profiles.add?aff_id=`$auth.user_id`"|fn_url:'C':'http'}</a>
</div>

{capture name="mainbox_title"}{$lang.affiliate_plan}{/capture}