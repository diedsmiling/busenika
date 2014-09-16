{* $Id: data_block.pre.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $show_rating}
{include file="addons/discussion/views/discussion/components/average_rating.tpl" object_id=$product.product_id object_type="P"}
{/if}