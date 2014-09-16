{* $Id: top.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

<div class="header-helper-container">
	<div class="logo-image">
		<a href="{""|fn_url}"><img src="{$images_dir}/{$manifest.Customer_logo.filename}" width="{$manifest.Customer_logo.width}" height="{$manifest.Customer_logo.height}" border="0" alt="{$manifest.Customer_logo.alt}" /></a>
	</div>
	
	{include file="top_quick_links.tpl"}
	
	{include file="top_menu.tpl"}
</div>

<div class="top-tools-container">
	<span class="float-left">&nbsp;</span>
	<span class="float-right">&nbsp;</span>
	<div class="top-tools-helper">
		<div class="float-right" id="sign_io"><!--dynamic:sign_io-->
			{assign var="escaped_current_url" value=$config.current_url|escape:url}
			{if !$auth.user_id}
				<a id="sw_login" {if $settings.General.secure_auth == "Y"} href="{if $controller == "auth" && $mode == "login_form"}{$config.current_url|fn_url}{else}{"auth.login_form?return_url=`$escaped_current_url`"|fn_url}{/if}"{else}class="cm-combination"{/if}>{$lang.sign_in}</a>
				{$lang.or}
				<a href="{"profiles.add"|fn_url}">{$lang.register}</a>
			{else}
				<a href="{"profiles.update"|fn_url}" class="strong">{if $user_info.firstname && $user_info.lastname}{$user_info.firstname}&nbsp;{$user_info.lastname}{else}{$user_info.email}{/if}</a>
				({include file="buttons/button.tpl" but_role="text" but_href="auth.logout?redirect_url=`$escaped_current_url`" but_text=$lang.sign_out})
			{/if}
			
			{if $settings.General.secure_auth != "Y"}
			<div id="login" class="cm-popup-box hidden">
				<div class="login-popup">
					<h1>{$lang.sign_in}</h1>
					{include file="views/auth/login_form.tpl" style="popup" form_name="login_popup_form" id="popup"}
				</div>
			</div>
			{/if}
		<!--/dynamic--><!--sign_io--></div>
		<div class="top-search">
			{include file="common_templates/search.tpl"}
		</div>
	</div>
</div>

<div class="content-tools">
	<span class="float-left">&nbsp;</span>
	<span class="float-right">&nbsp;</span>
	<div class="content-tools-helper clear">
		{include file="views/checkout/components/cart_status.tpl"}
		<div class="float-right">
			{if $localizations|sizeof > 1}
			<!--dynamic:localizations-->
				<div class="select-wrap localization">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"lc=" items=$localizations selected_id=$smarty.const.CART_LOCALIZATION display_icons=false key_name="localization" text=$lang.localization}</div>
			<!--/dynamic-->
			{/if}

			<!--dynamic:languages-->
			{if $languages|sizeof > 1}
				<div class="select-wrap">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"sl=" items=$languages selected_id=$smarty.const.CART_LANGUAGE display_icons=true key_name="name" language_var_name="sl"}</div>
			{/if}
			<!--/dynamic-->

			{if $currencies|sizeof > 1}
			<!--dynamic:currency-->
				<div class="select-wrap">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"currency=" items=$currencies selected_id=$secondary_currency display_icons=false key_name="description"}</div>
			<!--/dynamic-->
			{/if}
		</div>
	</div>
</div>