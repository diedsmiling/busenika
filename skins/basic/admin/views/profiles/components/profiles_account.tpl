{* $Id: profiles_account.tpl 10559 2010-08-31 13:47:26Z alexions $ *}

{include file="common_templates/subheader.tpl" title=$lang.user_account_information}

{if $uid == 1 || ($user_data.user_type == "A" && "RESTRICTED_ADMIN"|defined)}
	<input type="hidden" name="user_data[status]" value="A" />
	<input type="hidden" name="user_data[user_type]" value="A" />
{/if}

{if $settings.General.use_email_as_login == "Y"}
<div class="form-field">
	<label for="email" class="cm-required cm-email">{$lang.email}:</label>
	<input type="text" id="email" name="user_data[email]" class="input-text" size="32" maxlength="128" value="{$user_data.email}" />
</div>

{else}

<div class="form-field">
	<label for="user_login_profile" class="cm-required">{$lang.username}:</label>
	<input id="user_login_profile" type="text" name="user_data[user_login]" class="input-text" size="32" maxlength="32" value="{$user_data.user_login}" />
</div>
{/if}

<div class="form-field">
	<label for="password1" class="cm-required">{$lang.password}:</label>
	<input type="password" id="password1" name="user_data[password1]" class="input-text" size="32" maxlength="32" value="{if $mode == "update"}            {/if}" autocomplete="off" />
</div>

<div class="form-field">
	<label for="password2" class="cm-required">{$lang.confirm_password}:</label>
	<input type="password" id="password2" name="user_data[password2]" class="input-text" size="32" maxlength="32" value="{if $mode == "update"}            {/if}" autocomplete="off" />
</div>


{if $uid != 1 || $user_data.user_type != "A" || "RESTRICTED_ADMIN"|defined}

{include file="common_templates/select_status.tpl" input_name="user_data[status]" id="user_data" obj=$user_data hidden=false}

{assign var="_u_type" value=$smarty.request.user_type|default:$user_data.user_type}
{if $mode == "add"}
	<input type="hidden" name="user_data[user_type]" value="{$_u_type}" />
{else}
<div class="form-field">
	<label for="user_type" class="cm-required">{$lang.account_type}:</label>
	{assign var="r_url" value=$config.current_url|fn_query_remove:"user_type"}
	<select id="user_type" name="user_data[user_type]"{if !$redirect_denied} onchange="jQuery.redirect('{"`$r_url`&user_type="|fn_url}' + this.value);"{/if}>
		{hook name="profiles:account"}
		<option value="C" {if $_u_type == "C"}selected="selected"{/if}>{$lang.customer}</option>
		{if $smarty.const.RESTRICTED_ADMIN != 1 || $user_data.user_id == $auth.user_id}
		<option value="A" {if $_u_type == "A"}selected="selected"{/if}>{$lang.administrator}</option>
		{/if}
		{/hook}
	</select>
</div>
{/if}

<div class="form-field">
	<label for="tax_exempt">{$lang.tax_exempt}:</label>
	<input type="hidden" name="user_data[tax_exempt]" value="N" />
	<input id="tax_exempt" type="checkbox" name="user_data[tax_exempt]" value="Y" {if $user_data.tax_exempt == "Y"}checked="checked"{/if} class="checkbox" />
</div>

{/if}

<div class="form-field">
	<label for="user_language">{$lang.language}</label>
	<select name="user_data[lang_code]" id="user_language">
		{foreach from=$languages item="language" key="lang_code"}
			<option value="{$lang_code}" {if $lang_code == $user_data.lang_code}selected="selected"{/if}>{$language.name}</option>
		{/foreach}
	</select>
</div>