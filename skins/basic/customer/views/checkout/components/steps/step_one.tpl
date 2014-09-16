{* $Id: step_one.tpl 10284 2010-07-30 09:08:26Z angel $ *}

<div class="step-container{if $edit}-active{/if}" id="step_one">
	<h2 class="step-title{if $edit}-active{/if}">
		<span class="float-left">1.</span>

		{if $complete && !$edit}
			<img src="{$images_dir}/icons/icon_step_close.gif" width="19" height="17" border="0" alt="" class="float-right" />
		{/if}

		{if ($settings.General.disable_anonymous_checkout == "Y" && !$auth.user_id) || ($settings.General.disable_anonymous_checkout != "Y" && !$auth.user_id && !$contact_info_population) || $smarty.session.failed_registration == true}
			{assign var="title" value=$lang.please_sign_in}
		{else}
			{if $auth.user_id != 0}
				{assign var="login_info" value="`$user_data.firstname`&nbsp;`$user_data.lastname`"}
			{else}
				{assign var="login_info" value=$lang.guest}
			{/if}
			{assign var="title" value="`$lang.signed_in_as`&nbsp;`$login_info` - `$user_data.email`"}
		{/if}
		
		<a class="title{if $contact_info_population && !$edit} cm-ajax{/if}" {if $contact_info_population && !$edit}href="{"checkout.checkout?edit_step=step_one&amp;from_step=`$edit_step`"|fn_url}" rev="checkout_steps"{/if}>{$title}</a>
	</h2>
	{assign var="curl" value=$config.current_url|fn_query_remove:"login_type"}
	<div id="step_one_body" class="step-body{if $edit}-active{/if}">
		{if ($settings.General.disable_anonymous_checkout == "Y" && !$auth.user_id) || ($settings.General.disable_anonymous_checkout != "Y" && !$auth.user_id && !$contact_info_population) || $smarty.session.failed_registration == true}
			<div id="step_one_login" {if $login_type != "login"}class="hidden"{/if}>
				<div class="clear">
					{include file="views/checkout/components/checkout_login.tpl" checkout_type="one_page"}
				</div>
			</div>
			<div id="step_one_register" class="width50{if $login_type != "register"} hidden{/if}">
				<form name="step_one_register_form" class="{$ajax_form}" action="{""|fn_url}" method="post">
				<input type="hidden" name="result_ids" value="checkout_steps,sign_io" />
				<input type="hidden" name="return_to" value="checkout" />

				{include file="views/profiles/components/profiles_account.tpl" nothing_extra="Y" location="checkout"}
				{include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y"}
				
				{hook name="checkout:checkout_steps"}{/hook}
				
				{if $settings.Image_verification.use_for_register == "Y"}
					{include file="common_templates/image_verification.tpl" id="register" align="center"}
				{/if}

				<div class="buttons-container margin-top">
					{include file="buttons/button.tpl" but_name="dispatch[checkout.add_profile]" but_text=$lang.continue}
					&nbsp;{$lang.or}&nbsp; 
					{include file="buttons/button.tpl" but_href=$curl but_onclick="$('#step_one_register').hide(); $('#step_one_login').show();" but_text=$lang.cancel but_role="tool"} 
				</div>
				</form>
			</div>
		{else}
			<form name="step_one_contact_information_form" class="{$ajax_form} {$ajax_form_force}" action="{""|fn_url}" method="{if !$edit}get{else}post{/if}">
			<input type="hidden" name="update_step" value="step_one" />
			<input type="hidden" name="next_step" value="{if $smarty.request.from_step && $smarty.request.from_step != "step_one"}{$smarty.request.from_step}{else}step_two{/if}" />
			<input type="hidden" name="result_ids" value="checkout_steps" />
				{if $edit}
					<div class="width50">
						{include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y" email_extra=$smarty.capture.email_extra}
						<div class="right"><a href="{"auth.change_login"|fn_url}">{$lang.sign_in_as_different}</a></div>

						{hook name="checkout:checkout_steps"}
							<div class="buttons-container margin-top">
								{include file="buttons/button.tpl" but_name="dispatch[checkout.update_steps]" but_text=$but_text}
							</div>
						{/hook}
					</div>
				{/if}
			</form>
		{/if}
		
		{if !$edit}
			<div class="right">
				{include file="buttons/button.tpl" but_href="checkout.checkout?edit_step=step_one&amp;from_step=`$edit_step`" but_rev="checkout_steps" but_meta="cm-ajax" but_text=$lang.change but_role="tool"}
			</div>
		{/if}
	</div>
<!--step_one--></div>