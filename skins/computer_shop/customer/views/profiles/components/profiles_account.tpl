{* $Id: profiles_account.tpl 10180 2010-07-23 07:15:54Z angel $ *}

{if !$nothing_extra}
	{include file="common_templates/subheader.tpl" title=$lang.user_account_info}
{/if}

{hook name="profiles:account_info"}
{if $settings.General.use_email_as_login == "Y"}
{if $location != "checkout" || $settings.General.disable_anonymous_checkout == "Y"}
<div class="form-field">
	<label for="email" class="cm-required cm-email">{$lang.email}:</label>
	<input type="text" id="email" name="user_data[email]" size="32" maxlength="128" value="{$user_data.email}" class="input-text" />
</div>
{/if}
{else}
<div class="form-field">
	<label for="user_login_profile" class="cm-required">{$lang.username}:</label>
	<input id="user_login_profile" type="text" name="user_data[user_login]" size="32" maxlength="32" value="{$user_data.user_login}" class="input-text" />
</div>
{/if}

<div class="form-field">
	<label for="password1" class="cm-required cm-password">{$lang.password}:</label>
	<input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="{if $mode == "update"}            {/if}" class="input-text" autocomplete="off" />
</div>

<div class="form-field">
	<label for="password2" class="cm-required cm-password">{$lang.confirm_password}:</label>
	<input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="{if $mode == "update"}            {/if}" class="input-text" autocomplete="off" />
</div>
{/hook}
