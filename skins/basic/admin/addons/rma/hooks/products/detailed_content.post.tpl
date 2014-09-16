{* $Id: detailed_content.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.rma}
	<div class="form-field">
		<label for="is_returnable">{$lang.returnable}:</label>
		<input type="hidden" name="product_data[is_returnable]" id="is_returnable" value="N" />
			<input type="checkbox" name="product_data[is_returnable]" value="Y" {if $product_data.is_returnable == "Y" || $mode == "add"}checked="checked"{/if} onclick="jQuery.disable_elms(['return_period'], !this.checked);" class="checkbox" />
	</div>

	<div class="form-field">
		<label for="return_period">{$lang.return_period_days}:</label>
		<input type="text" id="return_period" name="product_data[return_period]" value="{$product_data.return_period|default:"10"}" class="input-text" size="10"  {if $product_data.is_returnable != "Y" && $mode != "add"}disabled="disabled"{/if} />
	</div>
</fieldset>