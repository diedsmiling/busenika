{* $Id: step_two.tpl 10287 2010-07-30 12:38:08Z angel $ *}

<div class="step-container{if $edit}-active{/if}" id="step_tree">
	<h2 class="step-title{if $edit}-active{/if}">
		<span class="float-left">3.</span>

		{if $complete && !$edit}
			<img src="{$images_dir}/icons/icon_step_close.gif" width="19" height="17" border="0" alt="" class="float-right" />
		{/if}
		
		<a class="title{if $complete && !$edit} cm-ajax{/if}" {if $complete && !$edit}href="{"checkout.checkout?edit_step=step_three&amp;from_step=`$edit_step`"|fn_url}" rev="checkout_steps"{/if}>{$lang.address}</a>
	</h2>

	<div id="step_two_body" class="step-body{if $edit}-active{/if}{if !$edit && !$complete} hidden{/if}">
		<div>
			<form name="step_two_billing_address" class="{$ajax_form} {$ajax_form_force}" action="{""|fn_url}" method="{if !$edit}get{else}post{/if}">
			<input type="hidden" name="update_step" value="step_three" />
			<input type="hidden" name="next_step" value="{if $smarty.request.from_step && $smarty.request.from_step != "step_two" && $smarty.request.from_step != "step_two"}{$smarty.request.from_step}{else}step_four{/if}" />
			<input type="hidden" name="result_ids" value="checkout_steps,sign_io" />
			<input type="hidden" name="dispatch" value="checkout.checkout" />

			{if $smarty.request.profile == "new"}
				{assign var="hide_profile_name" value=false}
			{else}
				{assign var="hide_profile_name" value=true}
			{/if}
			
			{if !$edit}
			<div class="step-complete-wrapper multiple-profiles">
				{include file="views/profiles/components/multiple_profiles.tpl" hide_profile_name=$hide_profile_name hide_profile_delete=true profile_id=$cart.profile_id create_href="checkout.checkout?edit_step=step_two&amp;from_step=$edit_step&amp;profile=new"}
			</div>
			{else}
				{include file="views/profiles/components/multiple_profiles.tpl" show_text=true hide_profile_name=$hide_profile_name hide_profile_delete=true profile_id=$cart.profile_id create_href="checkout.checkout?edit_step=step_two&amp;from_step=$edit_step&amp;profile=new"}
			{/if}
			
			{if $settings.General.address_position == "billing_first"}
				{assign var="first_section" value="B"}
				{if !$selfService}{assign var="first_section_text" value=$lang.billing_address}{/if}
				{assign var="sec_section" value="S"}
				{assign var="sec_section_text" value=$lang.shipping_address}
				{assign var="ship_to_another_text" value=$lang.text_ship_to_billing}
				{assign var="body_id" value="sa"}
			{else}
				{assign var="first_section" value="S"}
				{if !$selfService}{assign var="first_section_text" value=$lang.shipping_address}{/if}
				{assign var="sec_section" value="B"}
				{assign var="sec_section_text" value=$lang.billing_address}
				{assign var="ship_to_another_text" value=$lang.text_billing_same_with_shipping}
				{assign var="body_id" value="ba"}
			{/if}
			
			{if !$edit}
				<table width="92%" cellpadding="0" cellspacing="0" border="0">
				<tr valign="top">
				{if $profile_fields[$first_section]}
					<td width="45%">
						{include file="views/profiles/components/step_profile_fields.tpl" section=$first_section text=$first_section_text}
					</td>
				{/if}
				<td width="10%">&nbsp;</td>
				{if $profile_fields[$sec_section]}
					<td width="45%">
						{if $cart.ship_to_another}
							{include file="views/profiles/components/step_profile_fields.tpl" section=$sec_section text=$sec_section_text}
						{else}
							<div class="step-complete-wrapper clear">
								<strong class="float-left">{$sec_section_text}: &nbsp;</strong>
								<p class="no-padding overflow-hidden">{$ship_to_another_text}</p>
							</div>
						{/if}
					</td>
				{/if}
				</tr>
				</table>
			{else}
				<div class="clear">
					{if $profile_fields[$first_section]}
						<div class="float-left">
							{include file="views/profiles/components/profile_fields.tpl" section=$first_section body_id="" ship_to_another="Y" title=$first_section_text}
						</div>
					{/if}

					{if $profile_fields[$sec_section]}
						<div class="float-right">
							{include file="views/profiles/components/profile_fields.tpl" section=$sec_section body_id=$body_id address_flag=$profile_fields|fn_compare_shipping_billing ship_to_another=$cart.ship_to_another title=$sec_section_text}
						</div>
					{/if}
				</div>
				
				<div class="buttons-container margin-top">
					{include file="buttons/button.tpl" but_name="dispatch[checkout.update_steps]" but_text=$lang.continue}
				</div>
			{/if}
			</form>
		</div>

		{if $complete && !$edit}
			<div class="right">
				{include file="buttons/button.tpl" but_meta="cm-ajax" but_href="checkout.checkout?edit_step=step_two&amp;from_step=$edit_step" but_rev="checkout_steps" but_text=$lang.change but_role="tool"}
			</div>
		{/if}
	</div>
<!--step_two--></div>