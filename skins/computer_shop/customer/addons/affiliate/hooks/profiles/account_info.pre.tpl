{* $Id: account_info.pre.tpl 10006 2010-07-07 14:02:09Z klerik $ *}

{if $user_data.user_type != "A"}
{assign var="u_type" value=$smarty.request.user_type|default:$user_data.user_type}
{if $controller != 'checkout'}
<div class="form-field">
	<label for="user_type">{$lang.account_type}:</label>
	<select id="user_type" name="user_data[user_type]" onchange="jQuery.redirect('{"`$controller`.`$mode`?user_type="|fn_url}' + this.value);">
		<option value="C" {if $u_type == "C"}selected="selected"{/if}>{$lang.customer}</option>
		<option value="P" {if $u_type == "P"}selected="selected"{/if}>{$lang.affiliate}</option>
	</select>
</div>
{/if}

{if $u_type == "P" && $u_type != $user_data.user_type}
{if $mode == "add"}{assign var="_but" value=$lang.register}{else}{assign var="_but" value=$lang.save}{/if}
<p id="id_affiliate_agree_notification">{$lang.affiliate_agree_to_terms_conditions|replace:"[button_name]":$_but}</p>
{/if}
{/if}