{* $Id: compare.tpl 10496 2010-08-24 05:59:53Z andyye $ *}

{if !$comparison_data}
	<p class="no-items">{$lang.no_products_selected}</p>
{else}

	{script src="js/exceptions.js"}

	<div align="right" class="info-field-title">
		<ul class="action-bullets">
			<li>{if $action != "show_all"}<a href="{"product_features.compare.show_all"|fn_url}">{$lang.all_features}</a>{else}{$lang.all_features}{/if}</li>
			<li>{if $action != "similar_only"}<a href="{"product_features.compare.similar_only"|fn_url}">{$lang.similar_only}</a>{else}{$lang.similar_only}{/if}</li>
			<li>{if $action != "different_only"}<a href="{"product_features.compare.different_only"|fn_url}">{$lang.different_only}</a>{else}{$lang.different_only}{/if}</li>
		</ul>
	</div>

	{math equation="floor(100/x)" x=$comparison_data.products|sizeof assign="cell_width"}
	{assign var="return_current_url" value=$config.current_url|escape:url}
	<div class="scroll-x">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="compare-table">
	<tr>
		<td valign="middle" class="first-cell center" rowspan="2" colspan="2"><strong>{$lang.compare}:</strong></td>
		{foreach from=$comparison_data.products item=product}
		<td valign="bottom" width="{$cell_width}%" class="left-border product-item-image">
			<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width obj_id=$product.product_id images=$product.main_pair object_type="product" no_ids=true}</a></td>
		{/foreach}
	</tr>
	<tr valign="top">
		{foreach from=$comparison_data.products item=product}
		<td class="left-border bottom-border"><a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>&nbsp;<a href="{"product_features.delete_product?product_id=`$product.product_id`&amp;redirect_url=`$return_current_url`"|fn_url}"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="" align="bottom" /></a></td>
		{/foreach}
	</tr>
	{foreach from=$comparison_data.product_features item="group_features" key="group_id" name="feature_groups"}

	{if $group_id && $group_features}
	<tr {cycle values="class=\"table-row\", " name="fixed"}>
		<td colspan="{math equation="x+2" x=$total_products}" class="bottom-border">
			{include file="common_templates/subheader.tpl" title=$comparison_data.feature_groups.$group_id}
		</td>
	</tr>
	{/if}

	{foreach from=$group_features item="_feature" key=id name="product_features"}
	<tr {cycle values="class=\"table-row\", " name="fixed"}>
		<td height="30" {if $smarty.foreach.product_features.last}class="bottom-border"{/if}><a href="{"product_features.delete_feature?feature_id=`$id`&amp;redirect_url=`$return_current_url`"|fn_url}"><img src="{$images_dir}/icons/delete_product.gif" width="12" height="12" border="0" alt="" align="bottom" /></a>
		</td>
		<td class="nowrap{if $smarty.foreach.product_features.last} bottom-border{/if}"><strong>{$_feature}:</strong>&nbsp;&nbsp;&nbsp;</td>
		{foreach from=$comparison_data.products item=product}
		<td class="left-border{if $smarty.foreach.product_features.last} bottom-border{/if}">

		{if $product.product_features.$id}
		{assign var="feature" value=$product.product_features.$id}
		{else}
		{assign var="feature" value=$product.product_features[$group_id].subfeatures.$id}
		{/if}

		{strip}
		{if $feature.prefix}{$feature.prefix}{/if}
		{if $feature.feature_type == "C"}
			<img src="{$images_dir}/icons/checkbox_{if $feature.value != "Y"}un{/if}ticked.gif" width="13" height="13" alt="{$feature.value}" align="top" />
		{elseif $feature.feature_type == "D"}
			{$feature.value_int|date_format:"`$settings.Appearance.date_format`"}
		{elseif $feature.feature_type == "M" && $feature.variants}
			<ul class="float-left">
			{foreach from=$feature.variants item="var"}
			{if $var.selected}
			<li><img src="{$images_dir}/icons/checkbox_ticked.gif" width="13" height="13" alt="{$var.variant}" />&nbsp;{$var.variant}</li>
			{/if}
			{/foreach}
			</ul>
		{elseif $feature.feature_type == "S" || $feature.feature_type == "E"}
			{foreach from=$feature.variants item="var"}
				{if $var.selected}{$var.variant}{/if}
			{/foreach}
		{elseif $feature.feature_type == "N" || $feature.feature_type == "O"}
			{$feature.value_int|default:"-"}
		{else}
			{$feature.value|default:"-"}
		{/if}
		{if $feature.suffix}{$feature.suffix}{/if}
		{/strip}

		{/foreach}

	</tr>
	{/foreach}
	{/foreach}
	<tr>
		<td colspan="2">&nbsp;</td>
		{foreach from=$comparison_data.products item=product}
		<td class="left-border" valign="top">
			<div class="buttons-container">
			{include file="blocks/list_templates/simple_list.tpl" show_price=true min_qty=true product=$product show_add_to_cart=true but_role="action"}
			</div>
		</td>
		{/foreach}
	</tr>
	</table>
	</div>

	<div class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.clear_list but_href="product_features.clear_list?redirect_url=$index_script"}&nbsp;&nbsp;&nbsp;
		{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script but_role="action"}
	</div>

	{if $comparison_data.hidden_features}
	<p>&nbsp;</p>
	{include file="common_templates/subheader.tpl" title=$lang.add_feature}
	<form action="{""|fn_url}" method="post" name="add_feature_form">
	<input type="hidden" name="redirect_url" value="{$config.current_url}" />

	{html_checkboxes name="add_features" options=$comparison_data.hidden_features columns="4"}

	<div class="buttons-container margin-top">
	{include file="buttons/button.tpl" but_text=$lang.add but_name="dispatch[product_features.add_feature]"}
	</div>
	</form>
	{/if}
{/if}

{capture name="mainbox_title"}{$lang.compare}{/capture}
