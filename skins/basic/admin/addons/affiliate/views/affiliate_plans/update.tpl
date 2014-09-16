{* $Id: update.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{capture name="mainbox"}

{capture name="tabsbox"}

<div id="content_general">
<form action="{""|fn_url}" method="post" name="affiliate_plan_form" class="cm-form-highlight">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="" />

<fieldset>
	<div class="form-field">
		<label for="plan_name" class="cm-required">{$lang.name}:</label>
		<input type="text" name="affiliate_plan[name]" id="plan_name" value="{$affiliate_plan.name}" size="50" class="input-text main-input" />
	</div>
	
	<div class="form-field">
		<label for="description">{$lang.description}:</label>
		<textarea name="affiliate_plan[description]" id="description" cols="50" rows="4" class="input-textarea-long">{$affiliate_plan.description}</textarea>
	</div>
	
	<div class="form-field">
		<label for="cookie_expiration">{$lang.aff_cookie_expiration}:</label>
		<input type="text" name="affiliate_plan[cookie_expiration]" id="cookie_expiration" value="{$affiliate_plan.cookie_expiration|default:0}" size="10" class="input-text" />
	</div>
	
	<div class="form-field">
		<label for="init_balance">{$lang.set_initial_balance} ({$currencies.$primary_currency.symbol}):</label>
		<input type="text" name="affiliate_plan[payout_types][init_balance][value]" id="init_balance" value="{$affiliate_plan.payout_types.init_balance.value|default:"0"}" size="10" class="input-text" />
		<input type="hidden" name="affiliate_plan[payout_types][init_balance][value_type]" value="{$affiliate_plan.payout_types.init_balance.value_type|default:"A"}" />
	</div>
	
	<div class="form-field">
		<label for="min_payment" class="cm-required">{$lang.minimum_commission_payment} ({$currencies.$primary_currency.symbol}):</label>
		<input type="text" name="affiliate_plan[min_payment]" id="min_payment" value="{$affiliate_plan.min_payment}" size="10" class="input-text" />
	</div>
	
	<div class="form-field">
		<label for="method_based_selling_price">{$lang.method_based_selling_price}:</label>
		<input type="hidden" name="affiliate_plan[method_based_selling_price]" value="N" />
		<input type="checkbox" name="affiliate_plan[method_based_selling_price]" id="method_based_selling_price" {if $affiliate_plan.method_based_selling_price == "Y"}checked="checked"{/if} value="Y" class="checkbox" />
	</div>

	<div class="form-field">
		<label for="use_coupon_commission">{$lang.coupon_commission_overide_all}:</label>
		<input type="hidden" name="affiliate_plan[use_coupon_commission]" value="N" />
		<input type="checkbox" name="affiliate_plan[use_coupon_commission]" id="use_coupon_commission" {if $affiliate_plan.use_coupon_commission == "Y" || !$affiliate_plan}checked="checked"{/if} value="Y" class="checkbox" />
	</div>
	
	{include file="common_templates/select_status.tpl" input_name="affiliate_plan[status]" id="affiliate_plan" obj=$affiliate_plan}
</fieldset>

{if $payout_types}
<fieldset>
	{foreach from=$payout_types key="payout_id" item=payout_data name="payout_types"}
	
	{if $payout_data && $smarty.foreach.payout_types.first}
	
		{include file="common_templates/subheader.tpl" title=$lang.commission_rates}
	{/if}
	
	{if $payout_data.default == "Y"}
		{assign var="payout_var" value=$payout_data.title}
		<div class="form-field">
			<label for="payout_types_{$payout_data.id}">{$lang.$payout_var}:</label>
			<input type="text" name="affiliate_plan[payout_types][{$payout_data.id}][value]" id="payout_types_{$payout_data.id}" value="{$affiliate_plan.payout_types.$payout_id.value|default:"0"}" size="10" class="input-text" />&nbsp;
			<select name="affiliate_plan[payout_types][{$payout_data.id}][value_type]">
				{foreach from=$payout_data.value_types key="value_type" item="name_lang_var"}
					<option value="{$value_type}" {if $affiliate_plan.payout_types.$payout_id.value_type==$value_type}selected="selected"{/if}>{$lang.$name_lang_var} {if $value_type == "A"}({$currencies.$primary_currency.symbol}){elseif $value_type == "P"}(%){/if}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	
	{/foreach}
</fieldset>
{/if}

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[affiliate_plans.update]"}
</div>
</form>
</div>

{if $affiliate_plan}

{** Multi affiliates **}
<div id="content_multi_tier_affiliates">

<form action="{""|fn_url}" method="post" name="add_level_commissions_to_plan_form">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="multi_tier_affiliates" />

<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th>{$lang.level}</th>
	<th>{$lang.commission} (%)</th>
	<th>&nbsp;</th>
</tr>
{if $affiliate_plan.commissions}
{foreach from=$affiliate_plan.commissions key="com_id" item="commission"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center">
   		<input type="checkbox" name="commission_ids[]" value="{$com_id}" class="checkbox cm-item" /></td>
	<td>
		{$lang.level}&nbsp;{$com_id+1}</td>
   	<td>
   		<input type="text" name="affiliate_plan[commissions][{$com_id}]" value="{$commission}" size="10" class="input-text" /></td>
   	<td class="nowrap right">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"affiliate_plans.delete_commission?commission_id=`$com_id`&amp;plan_id=`$affiliate_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$com_id+1 tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

<div class="buttons-container buttons-bg">
	{if $affiliate_plan.commissions}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[affiliate_plans.delete_commissions]" class="cm-process-items cm-confirm" rev="add_level_commissions_to_plan_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[affiliate_plans.update_commissions]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="buttons-container float-right no-clear">
	{include file="common_templates/popupbox.tpl" id="add_commissions" text=$lang.add_commissions_multi_affiliates but_text=$lang.add_commissions act="create"}
	</div>
</div>

</form>

{capture name="levels_m_addition_picker"}
<form action="{""|fn_url}" method="post" name="levels_m_addition_form">
<input type="hidden" name="plan_id" value="{$smarty.request.plan_id}" />
<input type="hidden" name="selected_section" value="multi_tier_affiliates" />

<div class="object-container">
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr class="cm-first-sibling">
	<th>{$lang.commission} (%)</th>
	<th>&nbsp;</th>
</tr>
<tr id="box_new_level">
	<td><input type="text" name="levels[0][commission]" size="10" class="input-text-large" /></td>
	<td width="100%">
		{include file="buttons/multiple_buttons.tpl" item_id="new_level"}</td>
</table>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[affiliate_plans.add_commissions]" but_text=$lang.add_selected cancel_action="close"}
</div>

</form>
{/capture}
{include file="common_templates/popupbox.tpl" id="add_commissions" text=$lang.add_commissions_multi_affiliates content=$smarty.capture.levels_m_addition_picker act=""}

</div>
{** /Multi affiliates **}

{** Linked products **}
<div id="content_linked_products">

<form action="{""|fn_url}" method="post" name="linked_products_form">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="linked_products" />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.product_name}</th>
{if $payout_types}
	<th width="10%">{$lang.sales_commission}</th>
	<th>&nbsp;</th>
{/if}
</tr>
{if $linked_products}
{foreach from=$linked_products key=product_id item=product}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center"><input type="checkbox" name="product_ids[]" value="{$product_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a></td>
{if $payout_types}
	<td class="nowrap">
		<input type="text" name="sales[{$product.product_id}][value]" value="{$product.sale.value}" size="10" class="input-text" />&nbsp;
		<select name="sales[{$product.product_id}][value_type]">
		{foreach from=$payout_types.sale.value_types key="value_type" item="name_lang_var"}
			<option value="{$value_type}" {if $product.sale.value_type==$value_type}selected="selected"{/if}>{$lang.$name_lang_var} {if $value_type == "A"}({$currencies.$primary_currency.symbol}){elseif $value_type == "P"}(%){/if}</option>
		{/foreach}
		</select>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"affiliate_plans.delete_product?product_id=`$product_id`&amp;plan_id=`$affiliate_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$product.product_id tools_list=$smarty.capture.tools_items href="products.update?product_id=`$product.product_id`"}
	</td>
{/if}
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="{if $payout_types}4{else}2{/if}"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

<div class="buttons-container buttons-bg">
	{if $linked_products}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[affiliate_plans.delete_products]" class="cm-process-items cm-confirm" rev="linked_products_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[affiliate_plans.update_products]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="float-right">
	{include file="pickers/products_picker.tpl" extra_var="dispatch=affiliate_plans.add_products&plan_id=`$affiliate_plan.plan_id`&selected_section=linked_products" data_id="affiliate"}
	</div>
</div>
</form>

<!--content_linked_products--></div>
{** /Linked products **}

{** Linked categories **}
<div id="content_linked_categories">

<form action="{""|fn_url}" method="post" name="update_linked_categories_form">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="linked_categories" />

<table cellpadding="0" cellspacing="0" border="0" class="table">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="90%">{$lang.category}</th>
	<th width="10%">{$lang.sales_commission}</th>
	<th>&nbsp;</th>
</tr>
{if $linked_categories}
{foreach from=$linked_categories item=category}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center"><input type="checkbox" name="category_ids[]" value="{$category.category_id}" class="checkbox cm-item" /></td>
	<td>
		<a href="{"categories.update?category_id=`$category.category_id`"|fn_url}">{$category.category}</a></td>
	<td class="nowrap">
		<input type="text" name="sales[{$category.category_id}][value]" value="{$category.sale.value}" size="10" class="input-text" />&nbsp;
		<select name="sales[{$category.category_id}][value_type]">
		{foreach from=$payout_types.sale.value_types key="value_type" item="name_lang_var"}
			<option value="{$value_type}" {if $category.sale.value_type==$value_type}selected="selected"{/if}>{$lang.$name_lang_var} {if $value_type == "A"}({$currencies.$primary_currency.symbol}){elseif $value_type == "P"}(%){/if}</option>
		{/foreach}
		</select>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"affiliate_plans.delete_category?category_id=`$category.category_id`&amp;plan_id=`$affiliate_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$category.category_id tools_list=$smarty.capture.tools_items href="categories.update?category_id=`$category.category_id`"}
	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{** Form submit section **}
<div class="buttons-container buttons-bg">
	{if $linked_categories}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[affiliate_plans.delete_categories]" class="cm-process-items cm-confirm" rev="update_linked_categories_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[affiliate_plans.update_categories]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	<div class="float-right">
	{include file="pickers/categories_picker.tpl" extra_var="dispatch=affiliate_plans.add_categories&plan_id=`$affiliate_plan.plan_id`&amp;selected_section=linked_categories" multiple=true}
	</div>
</div>
{** /Form submit section **}

</form>

</div>
{** /Linked categories **}

{** Coupons **}
<div id="content_coupons">

<form action="{""|fn_url}" name="delete_coupons_form" method="POST">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="coupons" />

<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
<tr>
	<th width="1%" class="center">
	<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.coupon}</th>
	<th>{$lang.use_coupons_commission}</th>
	<th>{$lang.valid}</th>
	<th>&nbsp;</th>
</tr>
{if $affiliate_plan.coupons}
{foreach from=$affiliate_plan.coupons item=coupon}
<tr {cycle values="class=\"table-row\", " name="1"}>
	<td class="center">
		<input type="checkbox" name="promotion_ids[]" value="{$coupon.promotion_id}" class="checkbox cm-item" /></td>
	<td width="100%">
		<a href="{"promotions.update?promotion_id=`$coupon.promotion_id`"|fn_url}">{$coupon.name}</a></td>
	<td class="nowrap">
		<input type="text" name="coupons[{$coupon.promotion_id}][value]" value="{$coupon.use_coupon.value}" size="10" class="input-text" />&nbsp;
		<select name="coupons[{$coupon.promotion_id}][value_type]">
		{foreach from=$payout_types.use_coupon.value_types key="value_type" item="name_lang_var"}
			<option value="{$value_type}" {if $coupon.use_coupon.value_type==$value_type}selected="selected"{/if}>{$lang.$name_lang_var} {if $value_type == "A"}({$currencies.$primary_currency.symbol}){elseif $value_type == "P"}(%){/if}</option>
		{/foreach}
		</select>
	</td>
	<td class="nowrap {if (($coupon.from_date <= $coupon.current_date) && ($coupon.to_date >= $coupon.current_date))} strong{/if}">
		{$coupon.from_date|date_format:"`$settings.Appearance.date_format`"} - {$coupon.to_date|date_format:"`$settings.Appearance.date_format`"}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"affiliate_plans.delete_coupon?promotion_id=`$coupon.promotion_id`&amp;plan_id=`$affiliate_plan.plan_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$coupon.promotion_id tools_list=$smarty.capture.tools_items href="promotions.update?promotion_id=`$coupon.promotion_id`"}
	</td>
</tr>
{/foreach}
{else}
<tr class="no-items">
	<td colspan="5"><p>{$lang.no_items}</p></td>
</tr>
{/if}
</table>

{capture name="add_coupons_picker"}
<form action="{""|fn_url}" name="add_coupons_form" method="POST">
<input type="hidden" name="plan_id" value="{$affiliate_plan.plan_id}" />
<input type="hidden" name="selected_section" value="coupons" />

	<div class="object-container">
		<table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr>
			<th class="center" width="1%">
				<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
			<th width="65%">{$lang.name}</th>
			<th width="20%">{$lang.use_coupons_commission}</th>
			<th width="15%">{$lang.valid}</th>
		</tr>
		{if $coupons}
			{foreach from=$coupons item=coupon}
			<tr {cycle values="class=\"table-row\", "}>
				<td class="center">
					<input type="checkbox" name="promotion_ids[]" value="{$coupon.promotion_id}" class="checkbox cm-item" /></td>
				<td>
					<a href="{"promotions.update?promotion_id=`$coupon.promotion_id`"|fn_url}">{$coupon.name}</a></td>
				<td class="nowrap">
					<input type="text" name="coupons[{$coupon.promotion_id}][value]" size="10" class="input-text" />&nbsp;
					<select name="coupons[{$coupon.promotion_id}][value_type]">
					{foreach from=$payout_types.use_coupon.value_types key="value_type" item="name_lang_var"}
						<option value="{$value_type}">{$lang.$name_lang_var} {if $value_type == "A"}({$currencies.$primary_currency.symbol}){elseif $value_type == "P"}(%){/if}</option>
					{/foreach}
					</select>
				</td>
				<td class="nowrap {if (($coupon.from_date <= $coupon.current_date) && ($coupon.to_date >= $coupon.current_date))}strong{/if}">
					{$coupon.from_date|date_format:"`$settings.Appearance.date_format`"} - {$coupon.to_date|date_format:"`$settings.Appearance.date_format`"}</td>
			</tr>
			{/foreach}
		{else}
			<tr class="no-items">
				<td colspan="4"><p>{$lang.no_items}</p></td>
			</tr>
		{/if}
		</table>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" but_meta="cm-process-items" but_name="dispatch[affiliate_plans.add_coupons]" but_text=$lang.add_selected cancel_action="close"}
	</div>
</form>
{/capture}

<div class="buttons-container buttons-bg">
	{if $affiliate_plan.coupons}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[affiliate_plans.delete_coupons]" class="cm-process-items cm-confirm" rev="delete_coupons_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[affiliate_plans.update_coupons]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="buttons-container float-right no-clear">
	{include file="common_templates/popupbox.tpl" id="add_coupons" but_text=$lang.add_coupons act="create"}
	</div>
</div>
</form>
{include file="common_templates/popupbox.tpl" id="add_coupons" text=$lang.add_coupons content=$smarty.capture.add_coupons_picker}
</div>
{** /Coupons **}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}

{/if}

{if $mode == "add"}
	{assign var="title" value=$lang.new_plan}
{else}
	{assign var="title" value="`$lang.editing_plan`: `$affiliate_plan.name`"}
{/if}

{/capture}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
