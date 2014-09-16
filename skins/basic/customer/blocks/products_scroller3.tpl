{* $Id: products_scroller3.tpl 9783 2010-06-10 10:24:09Z lexa $ *}
{** block-description:scroller3 **}

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

<div class="center">
	<ul id="scroll_list_{$block.block_id}" class="jcarousel-skin hidden">
		{assign var="total_height" value="90"}
		{math equation="total_height + delim_height" assign="item_height" delim_height=$delim_height total_height=$total_height}
		{foreach from=$items item="product" name="for_products"}
			<li>
				<table cellpadding="5" cellspacing="0" border="0" width="{$item_width}" style="height: {$item_height}px;">
				<tr>
					<td valign="middle" class="left">
						<table cellpadding="0" cellspacing="3" border="0">
						<tr>
							<td valign="top" class="left compact">
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
					</td>
				</tr>
				</table>
			</li>
		{/foreach}
	</ul>
</div>

{script src="js/jquery.jcarousel.js"}
{include file="common_templates/scroller_init.tpl"}
