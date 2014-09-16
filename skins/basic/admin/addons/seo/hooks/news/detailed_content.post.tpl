{* $Id: detailed_content.post.tpl 6613 2008-12-19 12:46:16Z angel $ *}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.seo}
	
	<div class="form-field">
		<label for="seo_name">{$lang.seo_name}:</label>
		<input type="text" name="news_data[seo_name]" id="seo_name" size="55" maxlength="255" value="{$news_data.seo_name}" class="input-text" />
	</div>
</fieldset>