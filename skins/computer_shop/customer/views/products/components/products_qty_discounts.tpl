{* $Id: products_qty_discounts.tpl 6962 2009-03-02 14:40:38Z angel $ *}

<p><b style="color:#FF6633">{$lang.text_qty_spetially_price}:</b></p>

<table cellpadding="0" cellspacing="1" border="0" class="table qty-discounts">
<tr>
	<th class="left" valign="middle">{$lang.complect_in2}</th>
	{foreach from=$product.prices item="price"}
		<td class="center">&nbsp;{$price.lower_limit} {$lang.ed_st}&nbsp;</td>
	{/foreach}
</tr>
<tr>
	<th class="left" valign="middle">{$lang.price_in}</th>
	{foreach from=$product.prices item="price"}
		<td class="center">&nbsp;{include file="common_templates/price.tpl" value=$price.price}&nbsp;</td>
	{/foreach}
</tr>
</table>
