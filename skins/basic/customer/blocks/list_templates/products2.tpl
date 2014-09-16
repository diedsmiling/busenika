{* $Id: products2.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{** block-description:products2 **}

{if $columns > $items|count}
	{assign var="columns" value=$items|count}
{/if}

{split data=$products size=$columns assign="splitted_products"}
{math equation="floor(100/x)" x=$columns assign="cell_width"}

{if $block.properties.item_number == "Y"}
	{assign var="cur_number" value=1}
{/if}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
{foreach from=$splitted_products item="sproducts" name="splitted_products"}
<tr>
	{foreach from=$sproducts item="product" name="sproducts"}
	{if $product.product}
		<td width="4"><img src="{$images_dir}/prod_block_tl.gif" width="4" height="4" border="0" alt="" /></td>
		<td valign="top" width="{if $items|count > 1}{$cell_width}{else}100{/if}%" class="lm-top"><img src="{$images_dir}/spacer.gif" width="1" height="1" border="0" alt="" /></td>
		<td width="4"><img src="{$images_dir}/prod_block_tr.gif" width="4" height="4" border="0" alt="" /></td>
		{if !$smarty.foreach.sproducts.last}
			<td width="13"><img src="{$images_dir}/spacer.gif" width="13" height="1" border="0" alt="" /></td>
		{/if}
	{/if}
	{/foreach}
</tr>
<tr>
	{foreach from=$sproducts item="product" name="sproducts"}
	{if $product.product}
		{assign var="obj_id" value=$product.product_id}
		{assign var="obj_id_prefix" value="`$block.block_id`000`$product.product_id`"}
		{capture name=$obj_id}
			{include file="buttons/button.tpl" but_role="text" but_href="products.view?product_id=`$product.product_id`" but_text=$lang.more_info}
		{/capture}
		{include file="common_templates/product_data.tpl" product=$product extra_button=$smarty.capture.$obj_id}
		<td class="lm-left"><img src="{$images_dir}/spacer.gif" width="3" height="1" border="0" alt="" /></td>
		<td valign="top" width="{if $items|count > 1}{$cell_width}{else}100{/if}%" class="lm-center image-border compact left">
		{assign var="form_open" value="form_open_`$obj_id`"}
		{$smarty.capture.$form_open}
			<div class="clear">
				<div class="float-right">
					<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{include file="common_templates/image.tpl" image_width="70" images=$product.main_pair obj_id=$obj_id_prefix object_type="product" show_thumbnail="Y"}</a>
				</div>
				
				{if $block.properties.item_number == "Y"}{$cur_number}.&nbsp;{math equation="num + 1" num=$cur_number assign="cur_number"}{/if}
				{assign var="name" value="name_$obj_id"}{$smarty.capture.$name}
				
				<p>
					{assign var="old_price" value="old_price_`$obj_id`"}
					{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
					
					{assign var="price" value="price_`$obj_id`"}
					{$smarty.capture.$price}
				</p>
			</div>
			
			<div class="buttons-container right">
			{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
			{$smarty.capture.$add_to_cart}
			</div>
		{assign var="form_close" value="form_close_`$obj_id`"}
		{$smarty.capture.$form_close}
		</td>
		<td class="lm-right"><img src="{$images_dir}/spacer.gif" width="3" height="1" border="0" alt="" /></td>
		{if !$smarty.foreach.sproducts.last}
			<td width="13"><img src="{$images_dir}/spacer.gif" width="13" height="1" border="0" alt="" /></td>
		{/if}
	{/if}
	{/foreach}
</tr>

<tr>
	{foreach from=$sproducts item="product" name="sproducts"}
	{if $product.product}
		<td width="4"><img src="{$images_dir}/prod_block_bl.gif" width="4" height="4" border="0" alt="" /></td>
		<td valign="top" width="{if $items|count > 1}{$cell_width}{else}100{/if}%" class="lm-bottom"><img src="{$images_dir}/spacer.gif" width="1" height="1" border="0" alt="" /></td>
		<td width="4"><img src="{$images_dir}/prod_block_br.gif" width="4" height="4" border="0" alt="" /></td>
		{if !$smarty.foreach.sproducts.last}
			<td width="13"><img src="{$images_dir}/spacer.gif" width="13" height="1" border="0" alt="" /></td>
		{/if}
	{/if}
	{/foreach}
</tr>
{if !$smarty.foreach.splitted_products.last}
<tr>
	{foreach from=$sproducts item="product" name="sproducts"}
	{if $product.product}
	<td colspan="3" width="{if $items|count > 1}{$cell_width}{else}100{/if}%"><img src="{$images_dir}/spacer.gif" width="1" height="13" border="0" alt="" /></td>
		{if !$smarty.foreach.sproducts.last}
			<td width="13"><img src="{$images_dir}/spacer.gif" width="13" height="1" border="0" alt="" /></td>
		{/if}
	{/if}
	{/foreach}
</tr>
{/if}
{/foreach}
</table>