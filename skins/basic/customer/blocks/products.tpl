{* $Id: products.tpl 9393 2010-05-07 08:07:18Z klerik $ *}
{** block-description:products **}

{if $block.properties.hide_add_to_cart_button == "Y"}
	{assign var="_show_add_to_cart" value=false}
{else}
	{assign var="_show_add_to_cart" value=true}
{/if}

{if $block.properties.hide_options == "Y"}
	{assign var="_show_product_options" value=false}
{else}
	{assign var="_show_product_options" value=true}
{/if}

{include file="blocks/product_list_templates/products.tpl" 
products=$items no_sorting="Y" 
obj_prefix="`$block.block_id`000" 
item_number=$block.properties.item_number 
show_add_to_cart=$_show_add_to_cart 
show_product_options=$_show_product_options 
no_pagination=true}