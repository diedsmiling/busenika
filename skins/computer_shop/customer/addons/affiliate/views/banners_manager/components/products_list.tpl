{* $Id: products_list.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $list_data}
{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}
<ul class="bullets-list">
{foreach from=$list_data key=product_id item=product_name}
	<li>{include file="common_templates/popupbox.tpl" id="product_`$product_id`" link_text=$product_name text=$lang.product href="banner_products.view?product_id=`$product_id`"}</li>
{/foreach}
</ul>
{/if}