{* $Id: top.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

<div class="header-helper-container">
	<div class="wrapper">
		<div class="logo-image">
			<a href="{""|fn_url}"><img src="{$images_dir}/korzin.net_05.png" border="0"/></a>
		</div>
		
		{include file="top_quick_links.tpl"}
		
		{include file="top_menu.tpl"}
	</div>
</div>

<div class="top-tools-container">
	<div class="wrapper">	
		<div class="cont-width">
			<div class="leftwards">{include file="common_templates/search.tpl"}</div>
			<div class="rightwards">
				{include file="views/checkout/components/cart_status.tpl"}	

				{if $localizations|sizeof > 1}			
				<!--dynamic:localizations-->				
				{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"lc=" items=$localizations selected_id=$smarty.const.CART_LOCALIZATION display_icons=false key_name="localization" text=$lang.localization}
				<!--/dynamic-->	
				{/if}			
				<!--dynamic:languages-->			
				{if $languages|sizeof > 1}				
				{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"sl=" items=$languages selected_id=$smarty.const.CART_LANGUAGE display_icons=true key_name="name" language_var_name="sl"}
				{/if}
				<!--/dynamic-->			
				{if $currencies|sizeof > 1}			
				<!--dynamic:currency-->				
				{include file="common_templates/select_object.tpl" style="graphic" link_tpl=$config.current_url|fn_link_attach:"currency=" items=$currencies selected_id=$secondary_currency display_icons=false key_name="description"}
				<!--/dynamic-->
				{/if}
			</div>
		</div>
	</div>
