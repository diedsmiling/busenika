{* $Id: add_to_cart.tpl 9828 2010-06-22 08:00:34Z alexions $ *}

{assign var="c_url" value=$config.current_url|escape:url}

{if $settings.General.allow_anonymous_shopping == "Y" || $auth.user_id}
	{include file="buttons/button.tpl" but_id=$but_id but_text=$but_text|default:$lang.add_to_cart but_name=$but_name but_onclick=$but_onclick but_href=$but_href but_target=$but_target but_role=$but_role|default:"text"}
{else}
	<p class="wrapped"{if $block_width} style="width: {$settings.Thumbnails.product_lists_thumbnail_width}px"{/if}>{$lang.text_login_to_add_to_cart}</p>

	{if $controller == "auth" && $mode == "login_form"}
		{assign var="login_url" value=$config.current_url}
	{else}
		{assign var="login_url" value="auth.login_form?return_url=`$c_url`"}
	{/if}
	
	{include file="buttons/button.tpl" but_id=$but_id but_text=$lang.sign_in_to_buy but_href=$login_url but_role=$but_role|default:"text" but_name=""}
{/if}
