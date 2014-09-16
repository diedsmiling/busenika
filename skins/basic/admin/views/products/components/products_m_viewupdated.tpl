{* $Id: products_m_viewupdated.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{strip}
<p>{$lang.text_products_updated}</p>
{foreach from=$updated_products item=product}
<p>&nbsp;-&nbsp;<a href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|unescape}</a></p>
{/foreach}
{/strip}
