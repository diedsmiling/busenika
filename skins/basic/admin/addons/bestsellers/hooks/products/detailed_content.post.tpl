{* $Id: detailed_content.post.tpl 7745 2009-07-21 07:15:15Z alexions $ *}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.bestselling}

	<div class="form-field">
		<label for="sales_amount">{$lang.sales_amount}:</label>
		<input type="text" id="sales_amount" name="product_data[sales_amount]" value="{$product_data.sales_amount|default:"0"}" class="input-text" size="10" />
	</div>
</fieldset>