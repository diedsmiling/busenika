{* $Id: customer_info.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{include file="views/profiles/components/profiles_scripts.tpl"}

{capture name="mainbox"}

{include file="views/order_management/components/orders_header.tpl"}

<form name="profile_form" action="{""|fn_url}" method="post" class="cm-form-highlight">

{if $customer_auth.user_id && $settings.General.user_multiple_profiles == "Y"} {* Select user profile *}
	<div class="form-field">
		<label for="profile_id">{$lang.select_profile}:</label>
		<select name="profile_id" id="profile_id" onchange="jQuery.redirect('{"order_management.customer_info?profile_id="|fn_url}'+this.value);" class="select-expanded">
			{foreach from=$user_profiles item="user_profile"}
				<option value="{$user_profile.profile_id}" {if $cart.profile_id == $user_profile.profile_id}selected="selected"{/if}>{$user_profile.profile_name}</option>
			{/foreach}
		</select>
	</div>
{/if}

{include file="views/profiles/components/profile_fields.tpl" user_data=$cart.user_data section="C" title=$lang.contact_information}

{include file="views/profiles/components/profile_fields.tpl" user_data=$cart.user_data section="B" title=$lang.billing_address}
{include file="views/profiles/components/profile_fields.tpl" user_data=$cart.user_data section="S" title=$lang.shipping_address body_id="sa" shipping_flag=$profile_fields|fn_compare_shipping_billing ship_to_another=$cart.ship_to_another}

{if !$customer_auth.user_id && $settings.General.disable_anonymous_checkout == "Y"}
	{include file="views/profiles/components/profiles_account.tpl" redirect_denied=true}
{/if}

<div class="buttons-container buttons-bg center">
	<div class="float-left">
		{include file="buttons/save.tpl" but_name="dispatch[order_management.customer_info]" but_role="button_main"}
	</div>
	<div class="float-right">
		&nbsp;{include file="pickers/users_picker.tpl" extra_var="dispatch=order_management.select_customer&page=`$smarty.request.page`" display="radio" but_text="`$lang.choose` `$lang.user`" no_container=true}
	</div>
		{include file="buttons/button.tpl" but_text=$lang.proceed_to_the_next_step but_name="dispatch[order_management.customer_info.continue]" but_role="big"}
</div>
</form>
{/capture}
{if $cart.order_id == ""}
	{assign var="_title" value=$lang.create_new_order}
{else}
	{assign var="_title" value="`$lang.editing_order`:&nbsp;#`$cart.order_id`"}
{/if}
{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox anchor="profile" extra_tools=$smarty.capture.extra_tools}

