{* $Id: products_list.tpl 9391 2010-05-06 14:32:09Z lexa $ *}
{include file="common_templates/pagination.tpl"}

{script src="js/exceptions.js"}

{* add-new *}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	{if $hide_amount}
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items"/></th>
	{/if}
	<th>{$lang.product_name}</th>
	{if $show_price}
	<th class="center">{$lang.price}</th>
	{/if}
	{if !$hide_amount}
	<th class="center" width="5%">{$lang.quantity}</th>
	{/if}
</tr>
{if !$checkbox_name}{assign var="checkbox_name" value="add_products_ids"}{/if}
{foreach from=$products item=product}
<tr {cycle values="class=\"table-row\", "}>
	{if $hide_amount}
	<td class="center" width="1%">
		<input type="checkbox" name="{$checkbox_name}[]" value="{$product.product_id}" class="checkbox cm-item" id="checkbox_id_{$product.product_id}" /></td>
	{/if}
	<td>
		<input type="hidden" id="product_{$product.product_id}" value="{$product.product}" />
		{$product.product|unescape}{include file="views/products/components/select_product_options.tpl" id=$product.product_id product_options=$product.product_options name="product_data" show_aoc=$show_aoc additional_class=$additional_class}</td>
	{if $show_price}
	<td>&nbsp;{if !$product.price|floatval}<input class="input-text" type="text" size="3" name="product_data[{$product.product_id}][price]" value="" />{else}{include file="common_templates/price.tpl" value=$product.price}{/if}</td>
	{/if}
	{if !$hide_amount}
	<td class="center nowrap cm-value-changer" width="5%">
		<a class="no-underline strong increase-font cm-increase"> +</a>&nbsp;
		<input id="product_id_{$product.product_id}" type="text" value="0" name="product_data[{$product.product_id}][amount]" size="3" class="input-text cm-amount" />
		<a class="no-underline strong increase-font cm-decrease">&ndash;</a>
	</td>
	{/if}
	
	{hook name="product_list:table_columns"}
	{/hook}
	
	<script type="text/javascript">
	//<![CDATA[
		$('#opt_{$product.product_id} :input').add('#option_{$product.product_id}_AOC').attr('disabled', 'disabled');
	//]]>
	</script>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_items}</p></td>
</tr>
{/foreach}
</table>


<script type="text/javascript">
//<![CDATA[
{literal}
function fn_swithch_aoc_option(id, disable)
{
	if ($('#option_' + id + '_AOC').length) {
		$('#option_' + id + '_AOC').attr('disabled', disable ? 'disabled' : '');
		disable = $('#option_' + id + '_AOC').get(0).checked ? true : disable;
	}
	$('#opt_' + id).switchAvailability(disable, false);
}
{/literal}
{if !$hide_amount}
{literal}
	$('.cm-increase, .cm-decrease').click(function () {
		var inp = $('input', $(this).parents('.cm-value-changer:first'));
		var new_val = parseInt(inp.val()) + ($(this).is('a.cm-increase') ? 1 : -1);
		var disable = new_val > 0 ? false : true;
		var _id = inp.attr('id').replace('product_id_', '');

		fn_swithch_aoc_option(_id, disable);
	});
	
	$('.cm-amount').change(function () {
		var new_val = parseInt($(this).val());
		var disable = new_val > 0 ? false : true;
		var _id = $(this).attr('id').replace('product_id_', '');

		fn_swithch_aoc_option(_id, disable);
	});
{/literal}
{else}
{literal}
	$('.cm-item').click(function () {
		var disable = (this.checked) ? false : true;
		var _id = $(this).attr('id').replace('checkbox_id_', '');

		fn_swithch_aoc_option(_id, disable);
	});

	$('.cm-check-items').click(function () {
		var _checked = this.checked;
		$('.cm-item').each(function () {
			if (_checked && !this.checked || !_checked && this.checked) {
				$(this).click();
			}
		});
	});
{/literal}
{/if}
//]]>
</script>

{include file="common_templates/pagination.tpl"}
