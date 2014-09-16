{* $Id: customer_info.tpl 10248 2010-07-28 12:51:29Z angel $ *}

{*
login_type: login/register/guest
*}

{include file="views/profiles/components/profiles_scripts.tpl" location="checkout"}

{if $settings.General.one_page_checkout != "Y"}
	{include file="views/checkout/components/progressbar.tpl"}
{/if}


{if !$auth.user_id && !$cart.user_data.email}
<div id="profiles_auth" {if $login_type && $login_type != "login"}class="hidden"{/if}>
	{include file="views/checkout/components/checkout_login.tpl" no_box="Y" checkout_login="Y" checkout_type="classic"}
</div>
{/if}
<div id="profiles_box" {if !$auth.user_id && !$cart.user_data.email && (!$login_type || $login_type == "login")}class="hidden"{/if}>

{if !$auth.user_id}
<p>{$lang.text_checkout_new_profile_notice}</p>
<p class="float-right"><a href="{"checkout.customer_info"|fn_url}">{$lang.more_sign_in_options}</a></p>
{/if}

{$lang.text_mandatory_fields}

<form name="profile_form" action="{""|fn_url}" method="post">

{if $cart.user_data}
	{assign var="udata" value=$cart.user_data}
	{assign var="sh_to_a" value=$cart.ship_to_another}
{else}
	{assign var="udata" value=$saved_user_data}
	{assign var="sh_to_a" value=$ship_to_another}
{/if}

{capture name="group"}
	{if !$auth.user_id && !$cart.user_data.email && $login_type != "guest"}
		<div id="account_box" {if !$cart.profile_registration_attempt && $login_type != "register"}class="hidden"{/if}>{include file="views/profiles/components/profiles_account.tpl" place_to_box=false user_data=$udata location="checkout"}</div>
	{/if}
	
	{include file="views/profiles/components/profile_fields.tpl" user_data=$udata section="C" title=$lang.contact_information place_to_box=false location="checkout"}
	
	{include file="views/profiles/components/multiple_profiles.tpl" user_data=$udata profile_id=$cart.profile_id hide_profile_delete=true}
	
	{include file="views/profiles/components/profile_fields.tpl" user_data=$udata section="B" title=$lang.billing_address place_to_box=false}
	
	{include file="views/profiles/components/profile_fields.tpl" user_data=$udata section="S" title=$lang.shipping_address place_to_box=false body_id="sa" shipping_flag=$profile_fields|fn_compare_shipping_billing ship_to_another=$sh_to_a}

	{hook name="checkout:checkout_steps"}{/hook}
{/capture}
{include file="common_templates/group.tpl" content=$smarty.capture.group}

{if $settings.Image_verification.use_for_checkout == "Y" && !$cart.user_data.email}
	{include file="common_templates/image_verification.tpl" id="checkout" align="center"}
{/if}

<div class="buttons-container right">
	{include file="buttons/button.tpl" but_name="dispatch[checkout.customer_info]" but_role="big" but_text=$lang.proceed_to_the_next_step}
</div>

</form>

</div>
