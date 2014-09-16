{* $Id: detailed_content.post.tpl 6613 2008-12-19 12:46:16Z angel $ *}

<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.age_verification}
	{include file="addons/age_verification/views/age_verification/components/update_fields.tpl" array_name="product_data" record=$product_data}
</fieldset>