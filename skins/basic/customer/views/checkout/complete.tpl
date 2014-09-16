{* $Id: complete.tpl 8349 2009-12-03 09:42:19Z lexa $ *}

{if $order_info.payment_method.instructions}
<h2 class="subheader">{$lang.payment_instructions}</h2>
<div class="wysiwyg-content">
	{$order_info.payment_method.instructions|unescape}
</div>
{/if}

{if $order_info && $settings.General.allow_create_account_after_order == "Y" && !$auth.user_id}
<h2 class="subheader">{$lang.create_account}</h2>
<form name="order_register_form" action="{""|fn_url}" method="post">
	<input type="hidden" name="order_id" value="{$order_info.order_id}" />

	{if $settings.General.use_email_as_login != "Y"}
	<div class="form-field">
		<label for="user_login_profile" class="cm-required">{$lang.username}:</label>
		<input id="user_login_profile" type="text" name="user_data[user_login]" size="32" maxlength="32" value="" class="input-text" />
	</div>
	{/if}

	<div class="form-field">
		<label for="password1" class="cm-required cm-password">{$lang.password}:</label>
		<input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="" class="input-text" autocomplete="off" />
	</div>

	<div class="form-field">
		<label for="password2" class="cm-required cm-password">{$lang.confirm_password}:</label>
		<input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="" class="input-text" autocomplete="off" />
	</div>

	<div class="buttons-container margin-top">
		<p>{include file="buttons/button.tpl" but_name="dispatch[checkout.create_profile]" but_text=$lang.create}</p>
	</div>
</form>
{/if}

<div class="buttons-container clear{if !$order_info || !$settings.General.allow_create_account_after_order == "Y" || $auth.user_id} margin-top{/if}">
	<div class="float-left">
		{include file="buttons/continue_shopping.tpl" but_href=$continue_url|default:$index_script}
	</div>
	<div class="float-right">
		{if $order_info}
			{if $order_info.child_ids}
				{include file="buttons/button.tpl" but_text=$lang.my_order_details but_href="`$index_script`?dispatch[orders.search]=Search&period=A&order_id=`$order_info.child_ids`"}
			{else}
				{include file="buttons/button.tpl" but_text=$lang.my_order_details but_href="orders.details?order_id=`$order_info.order_id`"}
			{/if}
		{/if}
		{include file="buttons/button.tpl" but_text=$lang.view_orders but_href="orders.search"}
	</div>
</div>

{capture name="mainbox_title"}{$lang.order}{/capture}