{* $Id: top.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

<div class="header-helper-container">
	<div class="logo-image">
		<a href="{""|fn_url}"><img src="{$images_dir}/lindero_logo2.gif" border="0"/></a>
	</div>
	
	{include file="top_quick_links.tpl"}
	
	{include file="top_menu.tpl"}
</div>

<div class="top-tools-container">	<div class="cont-width">
		<span class="float-left">&nbsp;</span>
		<span class="float-right">&nbsp;</span>
		<div class="top-tools-helper">
			<div class="top-search">
				{include file="common_templates/search.tpl"}
			</div>
		</div>				<div class="content-tools">	<span class="float-left">&nbsp;</span>	<span class="float-right">&nbsp;</span>	<div class="content-tools-helper clear">		{include file="views/checkout/components/cart_status.tpl"}		<div class="float-right">			{if $localizations|sizeof > 1}			<!--dynamic:localizations-->				<div class="select-wrap localization">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"lc=" items=$localizations selected_id=$smarty.const.CART_LOCALIZATION display_icons=false key_name="localization" text=$lang.localization}</div>			<!--/dynamic-->			{/if}			<!--dynamic:languages-->			{if $languages|sizeof > 1}				<div class="select-wrap">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"sl=" items=$languages selected_id=$smarty.const.CART_LANGUAGE display_icons=true key_name="name" language_var_name="sl"}</div>			{/if}			<!--/dynamic-->			{if $currencies|sizeof > 1}			<!--dynamic:currency-->				<div class="select-wrap">{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"currency=" items=$currencies selected_id=$secondary_currency display_icons=false key_name="description"}</div>			<!--/dynamic-->			{/if}		</div>	</div></div>					</div>
</div>