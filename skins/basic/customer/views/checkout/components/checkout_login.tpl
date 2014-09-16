{* $Id: checkout_login.tpl 10283 2010-07-29 14:54:48Z alexions $ *}

<script type="text/javascript">
//<![CDATA[

function fn_switch_checkout_type(status)
{$ldelim}
	{if $checkout_type == 'classic'}
		{literal}
		$('#profiles_auth').switchAvailability(true);
		$('#profiles_box').switchAvailability(false);
		$('#account_box').switchAvailability(!status);
		$('#sa').switchAvailability(!$('elm_ship_to_another').attr('checked'));
		{/literal}
	{else}
		{literal}
		if (status == true) {
			$('#step_one_register').show();
		} else {
			$('#step_one_anonymous_checkout').show();
		}
		$('#step_one_login').hide();
		{/literal}
	{/if}
{$rdelim}
//]]>
</script>
<table cellpadding="0" cellspacing="0" border="0" class="login-table">
<tr valign="top">
	<td width="50%" class="login-form">
		{include file="common_templates/subheader.tpl" title=$lang.returning_customer}
		{include file="views/auth/login_form.tpl" form_name="step_one_login_form" result_ids="sign_io,checkout_steps,cart_status" id="checkout"}
	</td>
	<td width="50%">
		{include file="common_templates/subheader.tpl" title=$lang.new_customer}
		{assign var="curl" value=$config.current_url|fn_query_remove:"login_type"}
		
		{if $settings.General.approve_user_profiles != "Y"}
			{$lang.text_dont_have_an_account_full}
			<div class="buttons-container right">{include file="buttons/button.tpl" but_href="$curl&amp;login_type=register" but_onclick="fn_switch_checkout_type(true);" but_text=$lang.register}</div>
			<div class="delim">&nbsp;</div>
		{/if}
		
		{if $settings.General.disable_anonymous_checkout != "Y"}
			{$lang.text_dont_want_to_register_an_account}
			<div id="anonymous_checkout">
				<form name="step_one_anonymous_checkout_form" class="{$ajax_form}" action="{""|fn_url}" method="post">
					<input type="hidden" name="result_ids" value="checkout_steps" />
					
					{include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y" id_prefix="soacf_" show_email=true}

					<div class="buttons-container right">
						{include file="buttons/button.tpl" but_name="dispatch[checkout.customer_info]" but_text=$lang.checkout_as_guest}
					</div>
				</form>
			</div>
		{/if}
	</td>
</tr>
</table>
