{* $Id: payment_options.post.tpl 10081 2010-07-15 13:25:17Z alexions $ *}

{if $settings.General.one_page_checkout != "Y"}
	{assign var="additional_ids" value=",step_three"}
{/if}

{if $cart_products && $cart.points_info.total_price && $user_info.points > 0}
	<div class="buttons-container clear-both">
		{include file="buttons/button.tpl" but_role="text" but_text=$lang.point_payment but_id="sw_point_payment" but_meta="cm-combination"}
	</div>
	<div id="point_payment" class="right">
		<form class="cm-ajax" name="point_payment_form" action="{""|fn_url}" method="post">
		<input type="hidden" name="redirect_mode" value="{$location}" />
		<input type="hidden" name="result_ids" value="checkout_totals,checkout_steps{$additional_ids}" />
	
		<p>{$lang.text_point_in_account}&nbsp;<strong>{$user_info.points}</strong>.</p>
		<p>{$lang.text_points_in_order}&nbsp;<strong>{$cart.points_info.total_price}</strong>.</p>
		
		<p>
			<strong class="valign">{$lang.points_to_use}:</strong>
			<input type="text" class="input-text valign" name="points_to_use" size="40" value="" />
			{include file="buttons/button.tpl" but_role="text" but_name="dispatch[checkout.point_payment]" but_text=$lang.apply}
			<input type="submit" class="hidden" name="dispatch[checkout.point_payment]" value="" />
		</p>
		</form>
		
		{if $cart.points_info.in_use.points}
			{assign var="_redirect_url" value=$config.current_url|escape:url}
			{if $use_ajax}{assign var="_class" value="cm-ajax"}{/if}
			<span>{$lang.points_in_use}&nbsp;({$cart.points_info.in_use.points}&nbsp;{$lang.points}):</span>
			<strong>{include file="common_templates/price.tpl" value=$cart.points_info.in_use.cost}&nbsp;{include file="buttons/button.tpl" but_href="checkout.delete_points_in_use?redirect_url=`$_redirect_url`" but_meta=$_class but_role="delete" but_rev="checkout_totals,subtotal_price_in_points,checkout_steps`$additional_ids`"}</strong>
		{/if}
		
	<!--point_payment--></div>
{/if}