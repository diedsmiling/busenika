{* $Id: tabs_content.post.tpl 10209 2010-07-26 13:31:28Z angel $ *}

{if $page_type == $smarty.const.PAGE_TYPE_FORM}
<div id="content_build_form">

	<div class="form-field">
		<label for="form_submit_text">{$lang.form_submit_text}:</label>
		{assign var="form_submit_const" value=$smarty.const.FORM_SUBMIT}
		<textarea id="form_submit_text" class="input-textarea-long" rows="5" cols="50" name="page_data[form][general][{$form_submit_const}]" rows="5">{$form.$form_submit_const}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="form_submit_text"}
	</div>

	<div class="form-field">
		<label for="form_recipient" class="cm-required">{$lang.email_to}:</label>
		{assign var="form_recipient_const" value=$smarty.const.FORM_RECIPIENT}
		<input id="form_recipient" class="input-text" name="page_data[form][general][{$form_recipient_const}]" value="{$form.$form_recipient_const}" />
	</div>

	<div class="form-field">
		<label for="form_is_secure">{$lang.form_is_secure}:</label>
		{assign var="form_secure_const" value=$smarty.const.FORM_IS_SECURE}
		<input type="hidden" name="page_data[form][general][{$smarty.const.FORM_IS_SECURE}]" value="N" />
		<input type="checkbox" id="form_is_secure" class="checkbox" value="Y" {if $form.$form_secure_const == "Y"}checked="checked"{/if} name="page_data[form][general][{$form_secure_const}]" />
	</div>

	{include file="addons/form_builder/views/pages/components/pages_form_elements.tpl"}

</div>
{/if}