{* $Id: detailed_content.post.tpl 6613 2008-12-19 12:46:16Z angel $ *}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.seo}
	
	<div class="form-field">
		<label for="product_seo_name">{$lang.seo_name}:</label>
		<input type="text" name="product_data[seo_name]" id="product_seo_name" size="55" value="{$product_data.seo_name}" class="input-text-large" />
	</div>
</fieldset>