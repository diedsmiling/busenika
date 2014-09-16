{* $Id: products_scroller.tpl 9783 2010-06-10 10:24:09Z lexa $ *}
{** block-description:scroller **}

{if $scrollers_initialization != "Y"}
<script type="text/javascript">
//<![CDATA[
var scroller_directions = {""|fn_get_block_scroller_directions|to_json};
var scrollers_list = [];
//]]>
</script>
{capture name="scrollers_initialization"}Y{/capture}
{/if}

{assign var="obj_prefix" value="`$block.block_id`000"}

{assign var="item_width" value="140"}
{assign var="delim_height" value="20"}

<div align="center">
	<ul id="scroll_list_{$block.block_id}" class="jcarousel-skin hidden">
		{assign var="image_h" value="123"}
		{assign var="text_h" value="90"}
		{assign var="cellspacing" value="2"}

		{math equation="3 * cellspacing + image_h + text_h" assign="item_height" cellspacing=$cellspacing image_h=$image_h text_h=$text_h}

		{foreach from=$items item="product" name="for_products"}
			<li>
			{assign var="obj_id" value="scr_`$block.block_id`000`$product.product_id`"}
			{assign var="img_object_type" value="product"}
			{include file="common_templates/image.tpl" assign="object_img" image_width=$block.properties.thumbnail_width images=$product.main_pair no_ids=true object_type=$img_object_type show_thumbnail="Y"}
			<table cellpadding="0" cellspacing="{$cellspacing}" border="0" width="{$item_width}">
			<tr>
				<td class="center product-image" style="height: {$image_h}px;">
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$object_img}</a></td>
			</tr>
			<tr>
				<td class="center compact" style="height: {$text_h}px;">
					{if $block.properties.hide_add_to_cart_button == "Y"}
						{assign var="_show_add_to_cart" value=false}
					{else}
						{assign var="_show_add_to_cart" value=true}
					{/if}
					{strip}
					{if $block.properties.item_number == "Y"}{$smarty.foreach.for_products.iteration}.&nbsp;{/if}
					{include file="blocks/list_templates/simple_list.tpl" product=$product show_trunc_name=true show_price=true show_add_to_cart=$_show_add_to_cart but_role="text"}
					{/strip}
				</td>
			</tr>
			</table>
			</li>
		{/foreach}
	</ul>
</div>

{script src="js/jquery.jcarousel.js"}
{include file="common_templates/scroller_init.tpl"}
