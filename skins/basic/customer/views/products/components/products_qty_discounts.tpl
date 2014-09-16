{* $Id: products_qty_discounts.tpl 6962 2009-03-02 14:40:38Z angel $ *}

<p>{$lang.text_qty_discounts}:</p>

<table cellpadding="0" cellspacing="1" border="0" class="table qty-discounts">
<tr>
	<th class="left" valign="middle">{$lang.quantity}</th>
	{foreach from=$product.prices item="price"}
		<td class="center">&nbsp;{$price.lower_limit}+&nbsp;</td>
	{/foreach}
</tr>
<tr>
	<th class="left" valign="middle">{$lang.price}</th>
	{foreach from=$product.prices item="price"}
		<td class="center">&nbsp;{include file="common_templates/price.tpl" value=$price.price}&nbsp;</td>
	{/foreach}
</tr>
</table>
