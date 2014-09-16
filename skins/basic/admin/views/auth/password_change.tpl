{* $Id: password_change.tpl 0 2009-12-28 08:25:03Z 2tl $ *}

<form action="{""|fn_url}" method="post" name="main_login_form" class="cm-form-highlight">
<input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$index_script}" />
<input type="hidden" name="user_data[password_change]" value="true" />

<span class="right"><span>&nbsp;</span></span>
<!--<h1 class="clear">
	<a href="{$index_script|fn_url}" class="float-left"><img src="{$images_dir}/lindero_logo.gif" border="0"/></a>
	<span>{$lang.administration_panel}</span>
</h1>-->

<div class="login-content">
	<p>{$lang.error_password_expired}</p>

	{if $settings.General.use_email_as_login == "Y"}
		<p><label>{$lang.email}:</label></p>
		<div id="email" class="input-text">{$user_data.email}</div>
	{else}
		<p><label>{$lang.username}:</label></p>
		<div id="user_login_profile" class="input-text">{$user_data.user_login}</div>
		<input type="hidden" id="email" name="user_data[email]" value="{$user_data.email}" />
	{/if}
	
	<p><label for="password1" class="cm-required">{$lang.password}:</label></p>
	<input type="password" id="password1" name="user_data[password1]" class="input-text" size="20" maxlength="32" value="            " autocomplete="off" />

	<p><label for="password2" class="cm-required">{$lang.confirm_password}:</label></p>
	<input type="password" id="password2" name="user_data[password2]" class="input-text" size="20" maxlength="32" value="            " autocomplete="off" />
	
	<div class="buttons-container center">
		{include file="buttons/button.tpl" but_text=$lang.save but_name="dispatch[profiles.update]" but_role="button_main" tabindex="3"}
	</div>	
</div>
</form>