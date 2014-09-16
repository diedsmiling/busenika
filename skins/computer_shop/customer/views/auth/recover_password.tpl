{* $Id: recover_password.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div class="login">
<form name="recoverfrm" action="{""|fn_url}" method="post">

<p>{$lang.text_recover_password_notice}</p>
<div class="center">
	<div class="recover-password">
		<label class="strong" for="login_id">{$lang.email}:</label>
		<p class="break"><input type="text" id="login_id" name="user_email" size="30" value="" class="input-text cm-focus" /></p>
	</div>
	
	<div class="buttons-container">
		{include file="buttons/reset_password.tpl" but_name="dispatch[auth.recover_password]"}
	</div>
</div>
</form>
</div>

{capture name="mainbox_title"}{$lang.recover_password}{/capture}