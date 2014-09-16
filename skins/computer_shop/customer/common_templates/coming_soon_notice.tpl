{* $Id: coming_soon_notice.tpl 9828 2010-06-22 08:00:34Z alexions $ *}

<div class="product-coming-soon wrapped"{if $block_width} style="width: {$settings.Thumbnails.product_lists_thumbnail_width}px"{/if}>
	{assign var="date" value=$avail_date|date_format:$settings.Appearance.date_format}
	{if $add_to_cart == "N"}{$lang.product_coming_soon|replace:"[avail_date]":$date}{else}{$lang.product_coming_soon_add|replace:"[avail_date]":$date}{/if}
</div>