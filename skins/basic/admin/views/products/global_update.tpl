{* $Id: global_update.tpl 10495 2010-08-23 14:46:44Z 2tl $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="global_update_form" class="cm-form-highlight"/>

<p>{$lang.global_update_description}</p>

<div class="form-field">
	<label for="gu_price">{$lang.price}:</label>
	<input type="text" id="gu_price" name="update_data[price]" size="6" value="" class="input-text-medium" />
	<select name="update_data[price_type]">
		<option value="A" >{$currencies.$primary_currency.symbol}</option>
		<option value="P" >%</option>
	</select>
</div>

<div class="form-field">
	<label for="gu_list_price">{$lang.list_price}:</label>
	<input type="text" id="gu_list_price" name="update_data[list_price]" size="6" value="" class="input-text-medium" />
	<select name="update_data[list_price_type]">
		<option value="A" >{$currencies.$primary_currency.symbol}</option>
		<option value="P" >%</option>
	</select>
</div>

<div class="form-field">
	<label for="gu_in_stock">{$lang.in_stock}:</label>
	<input type="text" id="gu_in_stock" name="update_data[amount]" size="6" value="" class="input-text" />
</div>

{hook name="products:global_update"}{/hook}

{include file="common_templates/subheader.tpl" title=$lang.products}

{include file="pickers/products_picker.tpl" type="links" input_name="update_data[product_ids]" no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.products}

<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_text=$lang.apply but_role="button_main" but_name="dispatch[products.global_update]"}
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.global_update content=$smarty.capture.mainbox}