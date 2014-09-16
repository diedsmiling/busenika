{* $Id: form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<p>{if $product.age_warning_message}{$product.age_warning_message}{else}{$age_warning_message}{/if}</p>
<p>&nbsp;</p>

<form action="{""|fn_url}" method="post" name="age_verification">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<div class="form-field">
	<label for="age">{$lang.your_age}</label>
	<input type="text" name="age" id="age" size="10" class="input-text-short">
</div>

<div class="buttons-container">
	{include file="buttons/button.tpl" but_role="submit" but_text=$lang.verify but_name="dispatch[age_verification.verify]"}
</div>

</form>

{capture name="mainbox_title"}{$product.product|unescape}{/capture}
