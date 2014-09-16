{* $Id: checkout_totals.pre.tpl 10081 2010-07-15 13:25:17Z alexions $ *}

{if $cart.points_info.in_use}
	<li>
		{assign var="_redirect_url" value=$config.current_url|escape:url}
			{if $use_ajax}{assign var="_class" value="cm-ajax"}{/if}
		<span>{$lang.points_in_use}&nbsp;({$cart.points_info.in_use.points}&nbsp;{$lang.points}):</span>
		<strong>{include file="common_templates/price.tpl" value=$cart.points_info.in_use.cost}&nbsp;{if $settings.General.one_page_checkout == "Y"}{include file="buttons/button.tpl" but_href="checkout.delete_points_in_use?redirect_url=`$_redirect_url`" but_meta=$_class but_role="delete" but_rev="checkout_totals,subtotal_price_in_points,checkout_steps`$additional_ids`"}{/if}</strong>
	</li>
{/if}

{if $cart.points_info.reward}
	<li>
		<span>{$lang.points}:</span>
		<strong>{$cart.points_info.reward}</strong>
	</li>
{/if}