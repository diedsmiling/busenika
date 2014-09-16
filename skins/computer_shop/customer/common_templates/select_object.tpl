{* $Id: select_object.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{assign var="language_text" value=$text|default:$lang.select_descr_lang}
{assign var="icon_tpl" value="$images_dir/flags/%s.png"}

{if $style == "graphic"}
	{if $text}{$text}:{/if}
	
	{if $display_icons == true}
		<img src="{$selected_id|string_format:$icon_tpl|lower}" width="16" height="16" border="0" alt="" onclick="$('#sw_select_{$selected_id}_wrap_{$suffix}').click();" class="icons" />
	{/if}
	
	<a class="select-link cm-combo-on cm-combination" id="sw_select_{$selected_id}_wrap_{$suffix}">{$items.$selected_id.$key_name}{if $items.$selected_id.symbol}&nbsp;({$items.$selected_id.symbol}){/if}</a>

	<div id="select_{$selected_id}_wrap_{$suffix}" class="select-popup cm-popup-box hidden">
		<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="" class="close-icon no-margin cm-popup-switch" />
		<ul class="cm-select-list">
			{foreach from=$items item=item key=id}
				<li><a rel="nofollow" name="{$id}" href="{"`$link_tpl``$id`"|fn_url}" {if $display_icons == true}style="background-image: url('{$id|lower|string_format:$icon_tpl}');"{/if} class="{if $display_icons == true}item-link{/if} {if $selected_id == $id}active{/if}">{$item.$key_name|unescape}{if $item.symbol}&nbsp;({$item.symbol|unescape}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
{else}
	{if $text}<label for="id_{$var_name}">{$text}:</label>{/if}
	<select id="id_{$var_name}" name="{$var_name}" onchange="jQuery.redirect(this.value);" class="valign">
		{foreach from=$items item=item key=id}
			<option value="{"`$link_tpl``$id`"|fn_url}" {if $id == $selected_id}selected="selected"{/if}>{$item.$key_name|unescape}</option>
		{/foreach}
	</select>
{/if}
