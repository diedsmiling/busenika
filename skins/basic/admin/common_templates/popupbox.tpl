{* $Id: popupbox.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

{if $content|fn_check_view_permissions}

{if $act == "edit"}
	<a onclick="{$edit_onclick} jQuery.show_picker('{$id}', '', '.object-container'); return false;" class="{if !$link_text}text-button-edit{/if}{if $href} {$opener_ajax_class|default:"cm-ajax-update"}{/if}{if $link_class} {$link_class}{/if}"{if $href} href="{$href|fn_url}"{/if} id="opener_{$id}" rev="content_{$id}">{$link_text|default:$lang.edit|unescape}</a>
{elseif $act == "select_fields"}
	<span class="submit-button"><input id="opener_{$id}" type="button" onclick="{$edit_onclick} jQuery.show_picker('{$id}', '', '.object-container')" value="{$text}" /></span>
{elseif $act == "create"}
	{include file="buttons/button.tpl" but_onclick="`$edit_onclick` jQuery.show_picker('`$id`', '', '.object-container')" but_text=$but_text but_role="add" but_meta="text-button"}
{elseif $act == "notes"}
	<p><a id="opener_{$id}" onclick="{$edit_onclick} jQuery.show_picker('{$id}', '', '.object-container')">{$link_text}</a></p>
{elseif $act == "general"}
	<div class="tools-container">
		<span class="action-add">
		{if $content}
			<a id="opener_{$id}" onclick="{$edit_onclick} jQuery.show_picker('{$id}', '', '.object-container')">{$link_text|default:$lang.add}</a>
		{else}
			<a class="cm-external-click" rev="opener_{$id}">{$link_text|default:$lang.add}</a>
		{/if}
		</span>
	</div>
{elseif $act == "button"}
	{include file="buttons/button.tpl" but_text=$link_text but_href=$but_href but_role=$but_role but_id="openere_`$id`" but_onclick="`$edit_onclick` jQuery.show_picker('`$id`', '', '.object-container')"}
{elseif $act == "default"}
	<a{if $onclick} onclick="{$onclick}"{/if}{if $href} href="{$href|fn_url}"{/if} class="{$link_class|default:"text-button-edit"}">{$link_text}</a>
{/if}

{if $content || $href || $edit_picker}
<div id="{$id}" class="popup-{if $act == "edit" || $edit_picker}edit-{elseif $act == "notes" || $extra_act == "notes"}notes-{/if}content cm-popup-box cm-picker hidden{if $picker_meta} {$picker_meta}{/if}">
	<div class="cm-popup-hor-resizer cm-left-resizer"></div>
	<div class="cm-popup-hor-resizer cm-right-resizer"></div>
	<div class="cm-popup-corner-resizer cm-nw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-ne-resizer"></div>
	<div class="cm-popup-corner-resizer cm-sw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-se-resizer"></div>
	<div class="cm-popup-vert-resizer cm-top-resizer"></div>
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$text}{if $act != "edit"}:{/if}</h3>
	</div>

	<div class="cm-popup-content-footer" id="content_{$id}">
		{$content}
	</div>

	<div class="cm-popup-vert-resizer cm-bottom-resizer"></div>
</div>
{/if}

{else}{*
skipped {$text}
*}{/if}