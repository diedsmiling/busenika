{* $Id: login_form.tpl 9845 2010-06-23 10:59:32Z lexa $ *}

<form action="{$config.current_location}/{$index_script}" method="post" name="main_login_form" class="cm-form-highlight">
<input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$index_script}" />

<span class="right"><span>&nbsp;</span></span>
<!--<h1 class="clear">
	<a href="{$index_script|fn_url}" class="float-left"><img src="{$images_dir}/lindero_logo.gif" border="0" /></a>
	<span>{$lang.administration_panel}</span>
</h1>-->

<div class="login-content">
	<p><label for="username" class="cm-required">{if $settings.General.use_email_as_login == "Y"}{$lang.email}{else}{$lang.username}{/if}:</label></p>
	<input id="username" type="text" name="user_login" size="20" value="{$config.demo_username}" class="input-text cm-focus" tabindex="1" />
	<p><label for="password">{$lang.password}:</label></p>
	<input type="password" id="password" name="password" size="20" value="{$config.demo_password}" class="input-text" tabindex="2" />
	<div class="buttons-container nowrap right">
		<div class="float-left">
			<a href="{"auth.recover_password"|fn_url}" class="underlined">{$lang.forgot_password_question}</a>&nbsp;&nbsp;
		</div>
		
		{include file="buttons/sign_in.tpl" but_name="dispatch[auth.login]" but_role="button_main" tabindex="3"}
	</div>
</div>
</form>
