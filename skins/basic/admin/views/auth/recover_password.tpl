{* $Id: recover_password.tpl 9845 2010-06-23 10:59:32Z lexa $ *}

<form action="{""|fn_url}" method="post" name="recover_form" class="cm-form-highlight">

<span class="right"><span>&nbsp;</span></span>
<!--<h1 class="clear">
	<a href="{$index_script|fn_url}" class="float-left"><img src="{$images_dir}/lindero_logo.gif" border="0"/></a>
	<span>{$lang.recover_password}</span>
</h1>-->

<div class="login-content">
	<p>{$lang.text_recover_password_notice}</p>
	<p><label for="user_login">{$lang.email}:&nbsp;</label></p>
	<input type="text" name="user_email" id="user_login" size="20" value="" class="input-text cm-focus" />

	<div class="buttons-container center">
		{include file="buttons/button.tpl" but_text=$lang.reset_password but_name="dispatch[auth.recover_password]" but_role="button_main"}
	</div>
</div>
</form>
