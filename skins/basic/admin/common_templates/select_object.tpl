{* $Id: select_object.tpl 10585 2010-09-02 18:01:38Z andyye $ *}

{if $items|sizeof > 1}
<div class="tools-container inline {$class}">
{assign var="language_text" value=$text|default:$lang.select_descr_lang}
{assign var="icon_tpl" value="$images_dir/flags/%s.png"}

{if $style == "graphic"}
	{if $display_icons == true}
		<img src="{$selected_id|string_format:$icon_tpl|lower}" width="16" height="16" border="0" alt="" onclick="$('#sw_select_{$selected_id}_wrap_{$suffix}').click();" class="icons" />
	{/if}

	<script type="text/javascript">
	//<![CDATA[
	{literal}
	$(document).ready(function() {
		$("#filter").keyup(function () {
			var filter = $(this).val(), count = 0;
			$(".filtered:first li").each(function () {
				if ($(this).text().search(new RegExp(filter, "i")) < 0) {
					$(this).hide();
				} else {
					$(this).show();
					count++;
				}
			});
		});
	});
	{/literal}
	//]]>
	</script>

	<a class="select-link cm-combo-on cm-combination" id="sw_select_{$selected_id}_wrap_{$suffix}">{$items.$selected_id.$key_name}{if $items.$selected_id.symbol}&nbsp;({$items.$selected_id.symbol}){/if}</a>
	<div id="select_{$selected_id}_wrap_{$suffix}" class="popup-tools cm-popup-box hidden">
		<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="" class="close-icon no-margin cm-popup-switch" />
		{if $key_name == 'company'}<input id="filter" class="input-text cm-filter" type="text" size="12"/>{/if}
		<ul class="cm-select-list{if $display_icons == true} popup-icons{/if}{if $key_name == 'company'} filtered{/if}">
			{foreach from=$items item=item key=id}
				<li><a name="{$id}" href="{"`$link_tpl``$id`"|fn_url}" {if $display_icons == true}style="background-image: url('{$id|string_format:$icon_tpl|lower}');"{/if}>{$item.$key_name|unescape}{if $item.symbol}&nbsp;({$item.symbol|unescape}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
{elseif $style == "select"}
	{if $text}<label for="id_{$var_name}">{$text}:</label>{/if}
	<select id="id_{$var_name}" name="{$var_name}" onchange="jQuery.redirect(this.value);" class="valign">
		{foreach from=$items item=item key=id}
			<option value="{$link_tpl}{$id}" {if $id == $selected_id}selected="selected"{/if}>{$item.$key_name|unescape}</option>
		{/foreach}
	</select>
{/if}
</div>
{/if}