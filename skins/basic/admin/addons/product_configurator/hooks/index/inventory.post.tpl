{* $Id: inventory.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<li>{$lang.configurable}:&nbsp;{if $product_stats.configurable}<a href="{"products.manage?type=extended&amp;match=any&amp;configurable=C"|fn_url}">{$product_stats.configurable}</a>{else}0{/if}</li>