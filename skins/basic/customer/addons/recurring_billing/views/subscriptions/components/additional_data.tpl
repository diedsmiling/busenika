{* $Id: additional_data.tpl 6967 2009-03-04 09:26:06Z angel $ *}

{assign var="item_ids" value=","|explode:$data}
{foreach from=$item_ids item="item_id"}
<p>{$item_id|fn_get_product_name|escape}</p>
{/foreach}